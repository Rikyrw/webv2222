<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiSetor;
use App\Models\DetailSetor;
use Illuminate\Support\Facades\DB;

class NasabahRiwayatSetorController extends Controller
{
    public function index(Request $request)
    {
        $id_nasabah = session('id_nasabah') ?? 1;
        $user_name = session('nama_nasabah') ?? 'Guest User';

        $activePage = 'riwayat-setor';
        $transactions = [];
        $databaseError = null;

        try {
            // Query real data dari Supabase
            $transactions = DetailSetor::join('transaksi_setor', 'detail_setor.id_transaksi_setor', '=', 'transaksi_setor.id_transaksi_setor')
                ->join('jenis_sampah', 'detail_setor.id_jenis', '=', 'jenis_sampah.id_jenis_sampah')
                ->where('transaksi_setor.id_nasabah', $id_nasabah)
                ->select(
                    'transaksi_setor.id_transaksi_setor as id_transaksi',
                    'jenis_sampah.nama_jenis',
                    'detail_setor.berat_kg',
                    'jenis_sampah.harga_per_kg',
                    'detail_setor.subtotal',
                    'transaksi_setor.tanggal_setor',
                    'transaksi_setor.status'
                )
                ->orderBy('transaksi_setor.tanggal_setor', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id_transaksi' => $item->id_transaksi,
                        'nama_jenis' => $item->nama_jenis,
                        'berat_kg' => (float)$item->berat_kg,
                        'harga_per_kg' => (float)$item->harga_per_kg,
                        'subtotal' => (float)$item->subtotal,
                        'tanggal_setor' => $item->tanggal_setor,
                        'status' => $item->status,
                    ];
                })
                ->toArray();

        } catch (\Exception $e) {
            \Log::error('NasabahRiwayatSetorController Database Error: ' . $e->getMessage());
            $databaseError = 'Tidak dapat mengambil data transaksi. Periksa koneksi internet Anda.';
            $transactions = [];
        }

        return view('nasabah.riwayat_setor', compact(
            'activePage',
            'user_name',
            'transactions',
            'databaseError'
        ));
    }
}
