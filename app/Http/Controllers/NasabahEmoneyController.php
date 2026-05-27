<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\TransaksiPenarikan;
use Illuminate\Http\Request;

class NasabahEmoneyController extends Controller
{
    public function index(Request $request)
    {
        $user_id = session('id_nasabah') ?? 1;
        $saldo_val = 0;

        $saldo_val = (float) (Nasabah::where('id_nasabah', $user_id)->value('saldo') ?? 0);

        return view('nasabah.emoney', compact('saldo_val'));
    }

    public function store(Request $request)
    {
        $user_id = session('id_nasabah') ?? 1;

        $request->validate([
            'target' => 'required|string|max:255',
            'category' => 'required|in:DANA,GOPAY',
            'nominal' => 'required|integer|in:20000,50000,100000',
        ]);

        $nominal = $request->input('nominal');
        $category = $request->input('category');
        $target = $request->input('target');

        $saldo = (float) (Nasabah::where('id_nasabah', $user_id)->value('saldo') ?? 0);

        if ($saldo < $nominal) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi.');
        }

        try {
            TransaksiPenarikan::create([
                'id_nasabah' => $user_id,
                'jenis_penukaran' => 'E-money',
                'nominal' => $nominal,
                'status' => 'menunggu',
                'tanggal_pengajuan' => date('Y-m-d'),
                'deskripsi' => "Top-up {$category} ke {$target}",
            ]);

            return redirect()->back()->with('success', 'Transaksi berhasil diajukan dan menunggu persetujuan admin.');
        } catch (\Exception $e) {
            \Log::error('Emoney transaction error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses transaksi.');
        }
    }
} 
