<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\TransaksiPenarikan;
use App\Models\TransaksiSetor;
use Illuminate\Http\Request;

class NasabahDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (! session('id_nasabah')) {
            if (session()->has(NasabahEmailVerificationController::SESSION_KEY)) {
                return redirect()
                    ->route('nasabah.verification.notice')
                    ->with('error', 'Email belum diverifikasi.');
            }

            return redirect()->route('nasabah.login')->with('error', 'Silakan login terlebih dahulu');
        }

        $user_id = session('id_nasabah');
        $user_name = session('nama_nasabah') ?? 'Guest User';
        $saldo = (float) (session('saldo') ?? 0);

        try {
            $user = Nasabah::find($user_id);
            if ($user) {
                session([
                    'nama_nasabah' => $user->nama_lengkap ?: 'User',
                    'saldo' => $user->saldo ?? 0,
                ]);
                $user_name = $user->nama_lengkap ?: 'User';
                $saldo = (float) $user->saldo;
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard user fetch error: '.$e->getMessage());
        }

        $recent_setor = [];
        try {
            $recent_setor = TransaksiSetor::with('detailSetor')
                ->where('id_nasabah', $user_id)
                ->orderByDesc('tanggal_setor')
                ->limit(5)
                ->get()
                ->map(fn (TransaksiSetor $item): array => [
                    'id_transaksi' => $item->id_transaksi_setor,
                    'total_berat' => $item->detailSetor->sum(fn ($detail) => (float) $detail->berat_kg),
                    'total_nilai' => (float) $item->total_nilai,
                    'status' => $item->status ?? 'menunggu',
                    'tanggal_setor' => $item->tanggal_setor?->toDateString(),
                ])
                ->all();
        } catch (\Exception $e) {
            \Log::error('Recent setor error: '.$e->getMessage());
        }

        $recent_ppob = [];
        try {
            $recent_ppob = TransaksiPenarikan::where('id_nasabah', $user_id)
                ->orderByDesc('tanggal_pengajuan')
                ->limit(5)
                ->get()
                ->map(fn (TransaksiPenarikan $r): array => [
                    'type' => 'penarikan',
                    'id' => $r->id_penarikan,
                    'service' => $r->jenis_penukaran ?: 'Penarikan',
                    'amount' => (float) $r->nominal,
                    'status' => $r->status ?? 'menunggu',
                    'deskripsi' => $r->deskripsi ?? '',
                    'created_at' => $r->tanggal_pengajuan?->toDateString(),
                ])
                ->all();
        } catch (\Exception $e) {
            \Log::error('Recent PPOB error: '.$e->getMessage());
        }

        $setor_count = 0;
        $total_berat_sampah = 0;
        $ppob_total = 0;
        $ppob_month_total = 0;
        try {
            $setorQuery = TransaksiSetor::with('detailSetor')->where('id_nasabah', $user_id);
            $setor_count = (clone $setorQuery)->count();
            $total_berat_sampah = (float) $setorQuery->get()
                ->flatMap(fn (TransaksiSetor $setor) => $setor->detailSetor)
                ->sum(fn ($detail) => (float) $detail->berat_kg);

            $ppob_total = (float) TransaksiPenarikan::where('id_nasabah', $user_id)->sum('nominal');
            $monthStart = date('Y-m-01');
            $nextMonthStart = date('Y-m-01', strtotime('+1 month'));
            $ppob_month_total = (float) TransaksiPenarikan::where('id_nasabah', $user_id)
                ->where('tanggal_pengajuan', '>=', $monthStart)
                ->where('tanggal_pengajuan', '<', $nextMonthStart)
                ->sum('nominal');
        } catch (\Exception $e) {
            \Log::error('Aggregates error: '.$e->getMessage());
        }

        $activePage = 'dashboard';

        return view('nasabah.dashboard', compact(
            'activePage',
            'user_name',
            'saldo',
            'setor_count',
            'total_berat_sampah',
            'ppob_total',
            'ppob_month_total',
            'recent_setor',
            'recent_ppob'
        ));
    }

    public function logout()
    {
        session()->flush();

        return redirect()->route('nasabah.login')->with('success', 'Anda telah logout');
    }
}
