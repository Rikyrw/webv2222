<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NasabahTransaksiPPOBController extends Controller
{
    public function index(Request $request)
    {
        $user_id = session('id_nasabah') ?? 1;
        $user_name = session('nama_nasabah') ?? 'Guest User';

        // Dummy data
        $hist = [
            [
                'type' => 'penarikan',
                'id' => '001',
                'service' => 'E-money',
                'amount' => 150000,
                'status' => 'success',
                'deskripsi' => 'Transfer ke GCash',
                'created_at' => '2024-01-15 10:30:00',
            ],
            [
                'type' => 'transaksi',
                'id' => '102',
                'service' => 'Pulsa',
                'amount' => 50000,
                'status' => 'success',
                'deskripsi' => 'Pulsa Telkomsel 50rb',
                'created_at' => '2024-01-14 14:20:00',
            ],
            [
                'type' => 'penarikan',
                'id' => '002',
                'service' => 'PLN',
                'amount' => 200000,
                'status' => 'pending',
                'deskripsi' => 'Tagihan listrik token',
                'created_at' => '2024-01-13 09:15:00',
            ],
            [
                'type' => 'transaksi',
                'id' => '103',
                'service' => 'E-money',
                'amount' => 100000,
                'status' => 'success',
                'deskripsi' => 'Transfer ke OVO',
                'created_at' => '2024-01-12 16:45:00',
            ],
            [
                'type' => 'penarikan',
                'id' => '003',
                'service' => 'Pulsa',
                'amount' => 75000,
                'status' => 'failed',
                'deskripsi' => 'Pulsa Indosat gagal',
                'created_at' => '2024-01-11 11:00:00',
            ],
            [
                'type' => 'transaksi',
                'id' => '104',
                'service' => 'PLN',
                'amount' => 250000,
                'status' => 'success',
                'deskripsi' => 'Pembayaran token PLN',
                'created_at' => '2024-01-10 13:30:00',
            ],
        ];

        $activePage = 'transaksi';

        return view('nasabah.transaksi_ppob', compact(
            'activePage',
            'user_name',
            'hist'
        ));
    }
}
