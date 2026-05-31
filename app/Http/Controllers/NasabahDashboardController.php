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
                ->limit(12)
                ->get()
                ->filter(fn (TransaksiPenarikan $r): bool => $this->isPpobTransaction($r))
                ->take(5)
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
        $completed_setor_value = 0;
        $waiting_setor_count = 0;
        $completed_setor_count = 0;
        $rejected_setor_count = 0;
        $monthly_setor_count = 0;
        $monthly_total_berat_sampah = 0;
        $monthly_completed_setor_value = 0;
        $monthly_waiting_setor_count = 0;
        $monthly_completed_setor_count = 0;
        $monthly_rejected_setor_count = 0;
        $ppob_total = 0;
        $ppob_month_total = 0;
        $ppob_count = 0;
        $monthly_ppob_count = 0;
        $withdrawal_count = 0;
        $withdrawal_amount = 0;
        try {
            $monthStart = date('Y-m-01');
            $nextMonthStart = date('Y-m-01', strtotime('+1 month'));

            $setorRows = TransaksiSetor::with('detailSetor')
                ->where('id_nasabah', $user_id)
                ->get();

            $setor_count = $setorRows->count();

            foreach ($setorRows as $setor) {
                $bucket = $this->setorStatusBucket($setor->status ?? null);

                if ($bucket === 'waiting') {
                    $waiting_setor_count++;
                } elseif ($bucket === 'completed') {
                    $completed_setor_count++;
                    $completed_setor_value += (int) round((float) $setor->total_nilai);
                } elseif ($bucket === 'rejected') {
                    $rejected_setor_count++;
                }

                $rowWeight = (float) $setor->detailSetor->sum(fn ($detail) => (float) $detail->berat_kg);
                $total_berat_sampah += $rowWeight;

                $setorDate = $setor->tanggal_setor?->toDateString() ?? '';
                $isMonthlySetor = $setorDate >= $monthStart && $setorDate < $nextMonthStart;

                if ($isMonthlySetor) {
                    $monthly_setor_count++;
                    $monthly_total_berat_sampah += $rowWeight;

                    if ($bucket === 'waiting') {
                        $monthly_waiting_setor_count++;
                    } elseif ($bucket === 'completed') {
                        $monthly_completed_setor_count++;
                        $monthly_completed_setor_value += (int) round((float) $setor->total_nilai);
                    } elseif ($bucket === 'rejected') {
                        $monthly_rejected_setor_count++;
                    }
                }
            }

            $ppobRows = TransaksiPenarikan::where('id_nasabah', $user_id)->get();

            foreach ($ppobRows as $row) {
                $nominal = (float) $row->nominal;
                $pengajuanDate = $row->tanggal_pengajuan?->toDateString() ?? '';
                $isMonthlyPpob = $pengajuanDate >= $monthStart && $pengajuanDate < $nextMonthStart;

                if ($this->isPpobTransaction($row)) {
                    $ppob_count++;
                    $ppob_total += $nominal;

                    if ($isMonthlyPpob) {
                        $monthly_ppob_count++;
                        $ppob_month_total += $nominal;
                    }
                } else {
                    $withdrawal_count++;
                    $withdrawal_amount += $nominal;
                }
            }
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
            'completed_setor_value',
            'waiting_setor_count',
            'completed_setor_count',
            'rejected_setor_count',
            'monthly_setor_count',
            'monthly_total_berat_sampah',
            'monthly_completed_setor_value',
            'monthly_waiting_setor_count',
            'monthly_completed_setor_count',
            'monthly_rejected_setor_count',
            'ppob_total',
            'ppob_month_total',
            'ppob_count',
            'monthly_ppob_count',
            'withdrawal_count',
            'withdrawal_amount',
            'recent_setor',
            'recent_ppob'
        ));
    }

    private function setorStatusBucket(mixed $status): ?string
    {
        $value = strtolower(trim((string) $status));

        if (in_array($value, ['pending', 'menunggu', 'diproses', 'process'], true)) {
            return 'waiting';
        }

        if (in_array($value, ['success', 'approved', 'berhasil', 'selesai', 'sukses'], true)) {
            return 'completed';
        }

        if (in_array($value, ['rejected', 'reject', 'ditolak', 'failed', 'gagal', 'cancelled', 'canceled'], true)) {
            return 'rejected';
        }

        return null;
    }

    private function isPpobTransaction(TransaksiPenarikan $row): bool
    {
        $description = strtolower(trim((string) $row->deskripsi));

        if (
            str_starts_with($description, 'emoney:')
            || str_starts_with($description, 'pln:')
            || str_starts_with($description, 'pulsa:')
        ) {
            return true;
        }

        $type = strtolower(trim((string) $row->jenis_penukaran));

        return in_array($type, ['dana', 'gopay', 'ovo', 'shopeepay', 'linkaja', 'emoney', 'e-money', 'pln', 'pulsa'], true);
    }

    public function logout()
    {
        session()->flush();

        return redirect()->route('nasabah.login')->with('success', 'Anda telah logout');
    }
}
