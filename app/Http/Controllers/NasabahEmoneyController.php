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
        $emoney_error = Session::get('emoney_error', '');

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

        return view('nasabah.emoney', compact('saldo_val', 'emoney_error'));
    }

    public function store(Request $request)
    {
        $user_id = session('id_nasabah') ?? 1;

        $request->validate([
            'target' => 'required|string|max:255',
            'category' => 'required|in:OVO,DANA,GOPAY,LINKAJA',
            'nominal' => 'required|integer|min:5000|max:100000|multiple_of:5000',
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
            return redirect()->back()->with('emoney_error', 'Gagal memeriksa saldo.');
        }

        if ($saldo < $nominal) {
            return redirect()->back()->with('emoney_error', 'Saldo tidak mencukupi.');
        }

        // Insert into penarikan table
        try {
            $data = [
                'id_nasabah' => $user_id,
                'jenis_penukaran' => 'E-money',
                'nominal' => $nominal,
                'status' => 'menunggu',
                'tanggal_pengajuan' => now()->toDateTimeString(),
                'deskripsi' => "Top-up {$category} ke {$target}",
            ];

            $insertResponse = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
                'Content-Type' => 'application/json',
            ])->post($supabaseUrl . '/rest/v1/penarikan', $data);

            if (!$insertResponse->successful()) {
                return redirect()->back()->with('emoney_error', 'Gagal memproses transaksi.');
            }

            // Update balance
            $newSaldo = $saldo - $nominal;
            $updateResponse = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
                'Content-Type' => 'application/json',
            ])->patch($supabaseUrl . '/rest/v1/nasabah?id_nasabah=eq.' . $user_id, ['saldo' => $newSaldo]);

            if (!$updateResponse->successful()) {
                // Optionally rollback the penarikan insert, but for simplicity, just log
                \Log::error('Failed to update balance after penarikan insert');
            }

            // Update session saldo
            session(['saldo' => $newSaldo]);

            return redirect()->back()->with('emoney_error', 'Transaksi berhasil diajukan.');
        } catch (\Exception $e) {
            \Log::error('Emoney transaction error: ' . $e->getMessage());
            return redirect()->back()->with('emoney_error', 'Terjadi kesalahan saat memproses transaksi.');
        }
    }
} 
