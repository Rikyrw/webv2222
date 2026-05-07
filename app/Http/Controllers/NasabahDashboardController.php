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

        // Fetch user data
        try {
            $supabaseUrl = env('SUPABASE_URL');
            $supabaseKey = env('SUPABASE_KEY');

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
            ])->get($supabaseUrl . '/rest/v1/transaksi_setor?select=id_transaksi,id_nasabah,total_berat,total_nilai,status,tanggal_setor&id_nasabah=eq.' . $user_id . '&order=tanggal_setor.desc&limit=5');

            $recent_setor = $response->json() ?: [];
        } catch (\Exception $e) {
            \Log::error('Recent setor error: ' . $e->getMessage());
        }

        // Fetch recent PPOB transactions
        $recent_ppob = [];
        try {
            $penarikan = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/penarikan?select=id_penukaran,jenis_penukaran,nominal,status,tanggal_pengajuan,deskripsi&id_nasabah=eq.' . $user_id . '&order=tanggal_pengajuan.desc&limit=5')->json() ?: [];

            $transaksi = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/transaksi?select=id,id_nasabah,jenis,total,status,created_at,deskripsi&id_nasabah=eq.' . $user_id . '&order=created_at.desc&limit=5')->json() ?: [];

            $hist = [];
            foreach ($penarikan as $r) {
                $hist[] = [
                    'type' => 'penarikan',
                    'id' => $r['id_penukaran'] ?? null,
                    'service' => $r['jenis_penukaran'] ?? 'PPOB',
                    'amount' => isset($r['nominal']) ? floatval($r['nominal']) : 0,
                    'status' => $r['status'] ?? 'menunggu',
                    'deskripsi' => $r['deskripsi'] ?? '',
                    'created_at' => $r['tanggal_pengajuan'] ?? null,
                ];
            }
            foreach ($transaksi as $r) {
                $hist[] = [
                    'type' => 'transaksi',
                    'id' => $r['id'] ?? null,
                    'service' => $r['jenis'] ?? 'Transaksi',
                    'amount' => isset($r['total']) ? floatval($r['total']) : 0,
                    'status' => $r['status'] ?? 'success',
                    'deskripsi' => $r['deskripsi'] ?? '',
                    'created_at' => $r['created_at'] ?? null,
                ];
            }
            usort($hist, function($a, $b) {
                $ta = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
                $tb = isset($b['created_at']) ? strtotime($b['created_at']) : 0;
                return $tb <=> $ta;
            });
            $recent_ppob = array_slice($hist, 0, 5);
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
            ])->get($supabaseUrl . '/rest/v1/transaksi_setor?select=id_transaksi&id_nasabah=eq.' . $user_id)->json() ?: [];
            $setor_count = is_array($all_setor) ? count($all_setor) : 0;

            $all_penarikan = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/penarikan?select=nominal&id_nasabah=eq.' . $user_id)->json() ?: [];
            $sum_penarikan = 0;
            if (is_array($all_penarikan)) {
                foreach ($all_penarikan as $pp) {
                    $sum_penarikan += isset($pp['nominal']) ? floatval($pp['nominal']) : 0;
                }
            }

            $all_transaksi = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/transaksi?select=total,id_nasabah,jenis&id_nasabah=eq.' . $user_id)->json() ?: [];
            $sum_transaksi = 0;
            if (is_array($all_transaksi)) {
                foreach ($all_transaksi as $tt) {
                    $sum_transaksi += isset($tt['total']) ? floatval($tt['total']) : 0;
                }
            }

            $ppob_total = $sum_penarikan + $sum_transaksi;
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
