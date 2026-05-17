<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class NasabahPulsaController extends Controller
{
    public function index(Request $request)
    {
        $user_id = session('id_nasabah') ?? 1;
        $saldo_val = 0;

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
            \Log::error('Pulsa balance fetch error: ' . $e->getMessage());
        }

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

        $user_id = session('id_nasabah') ?? 1;
        $nominal = (int) $request->input('nominal');
        $category = $request->input('category');
        $target = $request->input('target');

        if ($nominal % 5000 !== 0) {
            return redirect()->back()->with('error', 'Nominal tidak valid. Pilih kelipatan 5.000 (min 5.000, max 50.000).');
        }

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
            \Log::error('Pulsa balance check error: ' . $e->getMessage());
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
                'jenis_penukaran' => 'Pulsa',
                'nominal' => $nominal,
                'status' => 'menunggu',
                'tanggal_pengajuan' => date('Y-m-d'),
                'deskripsi' => "Pulsa {$category} ke {$target}",
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
            \Log::error('Pulsa transaction error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses transaksi.');
        }
    }
}
