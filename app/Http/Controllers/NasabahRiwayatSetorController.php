<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NasabahRiwayatSetorController extends Controller
{
    public function index(Request $request)
    {
        $user_id = session('id_nasabah') ?? 1;
        $user_name = session('nama_nasabah') ?? 'Guest User';

        // Dummy data
        $transactions = [
            [
                'id_transaksi' => 'TRX001',
                'nama_jenis' => 'Kertas',
                'berat_kg' => 5.50,
                'harga_per_kg' => 2000,
                'subtotal' => 11000,
                'tanggal_setor' => '2024-01-15',
                'status' => 'selesai'
            ],
            [
                'id_transaksi' => 'TRX002',
                'nama_jenis' => 'Plastik',
                'berat_kg' => 3.25,
                'harga_per_kg' => 5000,
                'subtotal' => 16250,
                'tanggal_setor' => '2024-01-14',
                'status' => 'selesai'
            ],
            [
                'id_transaksi' => 'TRX003',
                'nama_jenis' => 'Logam',
                'berat_kg' => 2.75,
                'harga_per_kg' => 8000,
                'subtotal' => 22000,
                'tanggal_setor' => '2024-01-13',
                'status' => 'menunggu'
            ],
            [
                'id_transaksi' => 'TRX004',
                'nama_jenis' => 'Kaca',
                'berat_kg' => 4.00,
                'harga_per_kg' => 1500,
                'subtotal' => 6000,
                'tanggal_setor' => '2024-01-12',
                'status' => 'selesai'
            ],
            [
                'id_transaksi' => 'TRX005',
                'nama_jenis' => 'Kertas',
                'berat_kg' => 6.75,
                'harga_per_kg' => 2000,
                'subtotal' => 13500,
                'tanggal_setor' => '2024-01-11',
                'status' => 'ditolak'
            ],
            [
                'id_transaksi' => 'TRX006',
                'nama_jenis' => 'Plastik',
                'berat_kg' => 2.50,
                'harga_per_kg' => 5000,
                'subtotal' => 12500,
                'tanggal_setor' => '2024-01-10',
                'status' => 'selesai'
            ],
        ];

        $activePage = 'riwayat-setor';

        return view('nasabah.riwayat_setor', compact(
            'activePage',
            'user_name',
            'transactions'
        ));
    }
}
