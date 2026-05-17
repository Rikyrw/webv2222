<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class NasabahPlnController extends Controller
{
    public function index(Request $request)
    {
        // Design-only: don't call backend. Provide a minimal $user array like emoney.
        $user = [
            'saldo' => session('saldo') ?? 0,
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

        $saldo = 0;
        try {
            $supabaseUrl = env('SUPABASE_URL');
            $supabaseKey = env('SUPABASE_KEY');

            $response = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/nasabah?select=saldo&id_nasabah=eq.' . $user_id);

            $userData = $response->json();
            if (is_array($userData) && count($userData) > 0) {
                $saldo = $userData[0]['saldo'] ?? 0;
            }
        } catch (\Exception $e) {
            \Log::error('PLN balance check error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memeriksa saldo.');
        }

        if ($saldo < $nominal) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi.');
        }

        try {
            $supabaseUrl = env('SUPABASE_URL');
            $supabaseKey = env('SUPABASE_KEY');
            $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY') ?: $supabaseKey;

            $data = [
                'id_nasabah' => $user_id,
                'jenis_penukaran' => 'PLN',
                'nominal' => $nominal,
                'status' => 'menunggu',
                'tanggal_pengajuan' => date('Y-m-d'),
                'deskripsi' => "PLN ke {$target}",
            ];

            $insertResponse = Http::withHeaders([
                'apikey' => $serviceKey,
                'Authorization' => 'Bearer ' . $serviceKey,
                'Content-Type' => 'application/json',
            ])->post($supabaseUrl . '/rest/v1/penarikan_saldo', $data);

            if (!$insertResponse->successful()) {
                return redirect()->back()->with('error', 'Gagal memproses transaksi.');
            }

            return redirect()->back()->with('success', 'Transaksi berhasil diajukan dan menunggu persetujuan admin.');
        } catch (\Exception $e) {
            \Log::error('PLN transaction error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses transaksi.');
        }
    }
}