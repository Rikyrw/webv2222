<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\TransaksiPenarikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NasabahPulsaController extends Controller
{
    public function index(Request $request)
    {
        $user_id = (int) session('id_nasabah');
        $saldo_val = 0;

        $saldo_val = (float) (Nasabah::where('id_nasabah', $user_id)->value('saldo') ?? 0);

        $user = [
            'saldo' => $saldo_val,
        ];

        // Pass an empty error message by default so the view can render design state
        $pulsa_error = Session::get('pulsa_error', '');

        return view('nasabah.pulsa', compact('user', 'pulsa_error'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'target' => 'required|string|max:255',
            'category' => 'required|in:TELKOMSEL,INDOSAT,XL',
            'nominal' => 'required|integer|min:5000|max:50000',
        ]);

        $user_id = (int) session('id_nasabah');
        $nominal = (int) $request->input('nominal');
        $category = $request->input('category');
        $target = $request->input('target');

        if ($nominal % 5000 !== 0) {
            return redirect()->back()->with('error', 'Nominal tidak valid. Pilih kelipatan 5.000 (min 5.000, max 50.000).');
        }

        $saldo = (float) (Nasabah::where('id_nasabah', $user_id)->value('saldo') ?? 0);

        if ($saldo < $nominal) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi.');
        }

        try {
            TransaksiPenarikan::create([
                'id_nasabah' => $user_id,
                'jenis_penukaran' => 'Pulsa',
                'nominal' => $nominal,
                'status' => 'menunggu',
                'tanggal_pengajuan' => date('Y-m-d'),
                'deskripsi' => "Pulsa {$category} ke {$target}",
            ]);

            return redirect()->back()->with('success', 'Transaksi berhasil diajukan dan menunggu persetujuan admin.');
        } catch (\Exception $e) {
            \Log::error('Pulsa transaction error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses transaksi.');
        }
    }
}
