<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiSetor;
use App\Models\TransaksiPenarikan;
use App\Models\DetailSetor;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $activePage = 'transaksi';
        $pageTitle = 'Transaksi';
        $tab = $request->get('tab', 'setor'); // Default tab: setor

        // Get setor requests from database
        $setorRequests = DB::table('transaksi_setor')
            ->join('nasabah', 'transaksi_setor.id_nasabah', '=', 'nasabah.id_nasabah')
            ->where('transaksi_setor.status', 'pending')
            ->select(
                'transaksi_setor.id_transaksi_setor',
                'transaksi_setor.id_nasabah',
                'nasabah.nama_lengkap as nama_nasabah',
                'transaksi_setor.total_nilai',
                'transaksi_setor.tanggal_setor',
                'transaksi_setor.status'
            )
            ->orderBy('transaksi_setor.tanggal_setor', 'desc')
            ->get()
            ->map(function ($item) {
                // Get waste types for this transaction
                $details = DetailSetor::join('jenis_sampah', 'detail_setor.id_jenis', '=', 'jenis_sampah.id_jenis_sampah')
                    ->where('detail_setor.id_transaksi_setor', $item->id_transaksi_setor)
                    ->select('jenis_sampah.nama_jenis')
                    ->pluck('nama_jenis')
                    ->implode(', ');
                
                $totalBerat = DetailSetor::where('id_transaksi_setor', $item->id_transaksi_setor)
                    ->sum('berat_kg');
                
                return [
                    'id_transaksi' => $item->id_transaksi_setor,
                    'id_nasabah' => $item->id_nasabah,
                    'nama_nasabah' => $item->nama_nasabah,
                    'total_berat' => $totalBerat,
                    'total_nilai' => $item->total_nilai,
                    'jenis' => $details ?: 'N/A',
                    'tanggal_setor' => $item->tanggal_setor,
                    'status' => $item->status,
                ];
            })
            ->toArray();

        // Get penarikan requests from database
        $penarikanRequests = DB::table('penarikan_saldo')
            ->join('nasabah', 'penarikan_saldo.id_nasabah', '=', 'nasabah.id_nasabah')
            ->where('penarikan_saldo.status', 'pending')
            ->select(
                'penarikan_saldo.id_penarikan',
                'penarikan_saldo.id_nasabah',
                'nasabah.nama_lengkap as nama_nasabah',
                'penarikan_saldo.jenis_penukaran',
                'penarikan_saldo.nominal',
                'penarikan_saldo.deskripsi',
                'penarikan_saldo.tanggal_pengajuan',
                'penarikan_saldo.status'
            )
            ->orderBy('penarikan_saldo.tanggal_pengajuan', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id_penukaran' => $item->id_penarikan,
                    'id_nasabah' => $item->id_nasabah,
                    'nama_nasabah' => $item->nama_nasabah,
                    'jenis_penukaran' => $item->jenis_penukaran ?? 'Transfer Bank',
                    'nominal' => $item->nominal,
                    'deskripsi' => $item->deskripsi ?? 'Permintaan penarikan saldo',
                    'tanggal_pengajuan' => $item->tanggal_pengajuan,
                    'status' => $item->status,
                ];
            })
            ->toArray();

        // Combined history from both setor and penarikan
        $history = [];
        
        // Add setor to history
        foreach ($setorRequests as $s) {
            $history[] = [
                'type' => 'setor',
                'waktu' => $s['tanggal_setor'],
                'nama' => $s['nama_nasabah'],
                'jumlah' => $s['total_nilai'],
                'keterangan' => 'Setor Sampah: ' . $s['jenis'],
            ];
        }
        
        // Add penarikan to history (all statuses for history view)
        $allPenarikan = DB::table('penarikan_saldo')
            ->join('nasabah', 'penarikan_saldo.id_nasabah', '=', 'nasabah.id_nasabah')
            ->select(
                'penarikan_saldo.id_penarikan',
                'nasabah.nama_lengkap as nama_nasabah',
                'penarikan_saldo.jenis_penukaran',
                'penarikan_saldo.nominal',
                'penarikan_saldo.deskripsi',
                'penarikan_saldo.tanggal_pengajuan'
            )
            ->orderBy('penarikan_saldo.tanggal_pengajuan', 'desc')
            ->get();
        
        foreach ($allPenarikan as $p) {
            $history[] = [
                'type' => 'penarikan',
                'waktu' => $p->tanggal_pengajuan,
                'nama' => $p->nama_nasabah,
                'jumlah' => $p->nominal,
                'keterangan' => ($p->jenis_penukaran ?? 'Transfer Bank') . ' - ' . ($p->deskripsi ?? 'Permintaan penarikan saldo'),
            ];
        }

        // Sort by date descending
        usort($history, function ($a, $b) {
            return strtotime($b['waktu']) - strtotime($a['waktu']);
        });

        return view('admin.transaksi', compact(
            'activePage',
            'pageTitle',
            'tab',
            'setorRequests',
            'penarikanRequests',
            'history'
        ));
    }
}
