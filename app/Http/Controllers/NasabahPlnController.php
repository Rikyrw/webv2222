<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\TransaksiPenarikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NasabahPlnController extends Controller
{
    public function index(Request $request)
    {
        $userId = session('id_nasabah') ?? 1;
        $user = [
            'saldo' => (float) (Nasabah::where('id_nasabah', $userId)->value('saldo') ?? session('saldo') ?? 0),
        ];
        $pln_error = Session::get('pln_error', '');

        return view('nasabah.pln', compact('user', 'pln_error'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'target' => 'required|string|max:255',
            'nominal' => 'required|integer|in:20000,50000,100000',
        ]);

        $user_id = session('id_nasabah') ?? 1;
        $nominal = (int) $request->input('nominal');
        $target = $request->input('target');

        $saldo = (float) (Nasabah::where('id_nasabah', $user_id)->value('saldo') ?? 0);

        if ($saldo < $nominal) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi.');
        }

        try {
            TransaksiPenarikan::create([
                'id_nasabah' => $user_id,
                'jenis_penukaran' => 'PLN',
                'nominal' => $nominal,
                'status' => 'menunggu',
                'tanggal_pengajuan' => date('Y-m-d'),
                'deskripsi' => "PLN ke {$target}",
            ]);

            return redirect()->back()->with('success', 'Transaksi berhasil diajukan dan menunggu persetujuan admin.');
        } catch (\Exception $e) {
            \Log::error('PLN transaction error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses transaksi.');
        }
    }
}
