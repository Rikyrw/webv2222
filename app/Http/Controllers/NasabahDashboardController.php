<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NasabahDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Temporarily disabled auth check for development
        $user_id = session('id_nasabah') ?? 1; // Default to user ID 1 if not logged in
        $user_name = session('nama_nasabah') ?? 'Guest User';
        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = env('SUPABASE_KEY');

        // Fetch user data
        try {
            $response = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/nasabah?select=*&id_nasabah=eq.' . $user_id);

            $userData = $response->json();
            if (is_array($userData) && count($userData) > 0) {
                $user = $userData[0];
                session([
                    'nama_nasabah' => $user['nama_lengkap'] ?? ($user['nama_nasabah'] ?? 'User'),
                    'saldo' => $user['saldo'] ?? 0,
                ]);
                $user_name = $user['nama_lengkap'] ?? ($user['nama_nasabah'] ?? 'User');
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard user fetch error: ' . $e->getMessage());
        }

        // Fetch recent setor transactions
        $recent_setor = [];
        try {
            $response = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/transaksi_setor?select=id_transaksi_setor,total_nilai,status,tanggal_setor,detail_setor(berat_kg)&id_nasabah=eq.' . $user_id . '&order=tanggal_setor.desc&limit=5');

            $items = $response->json() ?: [];
            $recent_setor = [];
            if (is_array($items)) {
                foreach ($items as $item) {
                    $totalBerat = 0;
                    $details = isset($item['detail_setor']) && is_array($item['detail_setor']) ? $item['detail_setor'] : [];
                    foreach ($details as $detail) {
                        $totalBerat += isset($detail['berat_kg']) ? (float) $detail['berat_kg'] : 0;
                    }
                    $recent_setor[] = [
                        'id_transaksi' => $item['id_transaksi_setor'] ?? null,
                        'total_berat' => $totalBerat,
                        'total_nilai' => isset($item['total_nilai']) ? (float) $item['total_nilai'] : 0,
                        'status' => $item['status'] ?? 'menunggu',
                        'tanggal_setor' => $item['tanggal_setor'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::error('Recent setor error: ' . $e->getMessage());
        }

        // Fetch recent penarikan saldo
        $recent_ppob = [];
        try {
            $penarikan = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/penarikan_saldo?select=id_penarikan,jenis_penukaran,nominal,status,tanggal_pengajuan,deskripsi&id_nasabah=eq.' . $user_id . '&order=tanggal_pengajuan.desc&limit=5')->json() ?: [];

            $hist = [];
            if (is_array($penarikan)) {
                foreach ($penarikan as $r) {
                    $hist[] = [
                        'type' => 'penarikan',
                        'id' => $r['id_penarikan'] ?? null,
                        'service' => $r['jenis_penukaran'] ?? 'Penarikan',
                        'amount' => isset($r['nominal']) ? floatval($r['nominal']) : 0,
                        'status' => $r['status'] ?? 'menunggu',
                        'deskripsi' => $r['deskripsi'] ?? '',
                        'created_at' => $r['tanggal_pengajuan'] ?? null,
                    ];
                }
            }

            $recent_ppob = $hist;
        } catch (\Exception $e) {
            \Log::error('Recent PPOB error: ' . $e->getMessage());
        }

        // Compute aggregates
        $setor_count = 0;
        $ppob_total = 0;
        try {
            $all_setor = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/transaksi_setor?select=id_transaksi_setor&id_nasabah=eq.' . $user_id)->json() ?: [];
            $setor_count = is_array($all_setor) ? count($all_setor) : 0;

            $all_penarikan = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/penarikan_saldo?select=nominal&id_nasabah=eq.' . $user_id)->json() ?: [];
            $sum_penarikan = 0;
            if (is_array($all_penarikan)) {
                foreach ($all_penarikan as $pp) {
                    $sum_penarikan += isset($pp['nominal']) ? floatval($pp['nominal']) : 0;
                }
            }

            $ppob_total = $sum_penarikan;
        } catch (\Exception $e) {
            \Log::error('Aggregates error: ' . $e->getMessage());
        }

        $activePage = 'dashboard';

        return view('nasabah.dashboard', compact(
            'activePage',
            'user_name',
            'setor_count',
            'ppob_total',
            'recent_setor',
            'recent_ppob'
        ));
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('nasabah.login')->with('success', 'Anda telah logout');
    }
}
