<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class NasabahEmoneyController extends Controller
{
    public function index(Request $request)
    {
        $user_id = session('id_nasabah') ?? 1;
        $saldo_val = 0;

        // Fetch user balance
        try {
            $supabaseUrl = env('SUPABASE_URL');
            $supabaseKey = env('SUPABASE_KEY');

            $response = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/nasabah?select=saldo&id_nasabah=eq.' . $user_id);

            $userData = $response->json();
            if (is_array($userData) && count($userData) > 0) {
                $saldo_val = $userData[0]['saldo'] ?? 0;
            }
        } catch (\Exception $e) {
            \Log::error('Emoney balance fetch error: ' . $e->getMessage());
        }

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

        // Fetch current balance
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
            \Log::error('Emoney balance check error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memeriksa saldo.');
        }

        if ($saldo < $nominal) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi.');
        }

        // Insert into penarikan table
        try {
            $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY') ?: $supabaseKey;
            $data = [
                'id_nasabah' => $user_id,
                'jenis_penukaran' => 'E-money',
                'nominal' => $nominal,
                'status' => 'menunggu',
                'tanggal_pengajuan' => date('Y-m-d'),
                'deskripsi' => "Top-up {$category} ke {$target}",
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
            \Log::error('Emoney transaction error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses transaksi.');
        }
    }
} 
