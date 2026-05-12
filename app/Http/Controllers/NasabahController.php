<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NasabahController extends Controller
{
    public function daftar(Request $request)
    {
        $activePage = 'nasabah';
        $pageTitle = 'Daftar Nasabah';
        $flash = '';
        $nasabahs = [];
        $databaseError = null;

        try {
            // Handle POST action: approve or reject nasabah
            if ($request->isMethod('post') && $request->filled('action') && $request->filled('id_nasabah')) {
                $id = (int) $request->input('id_nasabah');
                $action = $request->input('action'); // expected: aktifkan | tolak

                // Validate CSRF token
                if (!hash_equals(session('csrf_token', ''), $request->input('csrf_token', ''))) {
                    $flash = 'Token keamanan tidak valid.';
                } else {
                    if ($action === 'aktifkan') {
                        $newStatus = 'aktif';
                    } elseif ($action === 'tolak') {
                        $newStatus = 'nonaktif';
                    } else {
                        $newStatus = null;
                    }

                    if ($newStatus) {
                        $response = $this->supabaseRequest(
                            'patch',
                            '/rest/v1/nasabah?id_nasabah=eq.' . $id,
                            ['status' => $newStatus],
                            true
                        );

                        if ($response->successful()) {
                            $flash = 'Status nasabah berhasil diperbarui.';
                        } else {
                            $flash = 'Gagal memperbarui status nasabah.';
                        }
                    } else {
                        $flash = 'Aksi tidak dikenali.';
                    }
                }
            }

            // Check session flash
            if (session()->has('flash_nasabah')) {
                $flash = session('flash_nasabah');
                session()->forget('flash_nasabah');
            }

            $nasabahs = $this->fetchNasabahList();
        } catch (\Exception $e) {
            \Log::error('NasabahController Database Error: ' . $e->getMessage());
            $databaseError = 'Tidak dapat terhubung ke database. Periksa koneksi internet Anda.';
            $nasabahs = [];
        }

        return view('admin.daftar_nasabah', compact(
            'activePage',
            'pageTitle',
            'flash',
            'nasabahs',
            'databaseError'
        ));
    }

    private function fetchNasabahList(): array
    {
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/nasabah?select=id_nasabah,nama_lengkap,alamat,no_hp,saldo,status,created_at&order=created_at.desc',
            null,
            false
        );

        if (!$response->successful()) {
            return [];
        }

        $items = $response->json();
        if (!is_array($items)) {
            return [];
        }

        $mapped = [];
        foreach ($items as $item) {
            $tanggal = $item['created_at'] ?? null;
            $mapped[] = [
                'id_nasabah' => $item['id_nasabah'] ?? null,
                'nama_nasabah' => $item['nama_lengkap'] ?? null,
                'alamat' => $item['alamat'] ?? '-',
                'no_hp' => $item['no_hp'] ?? '-',
                'saldo' => $item['saldo'] ?? 0,
                'status_akun' => $item['status'] ?? 'verifikasi',
                'tanggal_daftar' => is_string($tanggal) ? $tanggal : '-',
            ];
        }

        return $mapped;
    }

    private function supabaseRequest(string $method, string $path, ?array $payload, bool $returnRepresentation)
    {
        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = env('SUPABASE_KEY');

        $request = Http::withHeaders([
            'apikey' => $supabaseKey,
            'Authorization' => 'Bearer ' . $supabaseKey,
        ]);

        if ($returnRepresentation) {
            $request = $request->withHeaders([
                'Prefer' => 'return=representation',
            ]);
        }

        $url = $supabaseUrl . $path;

        if ($method === 'get') {
            return $request->get($url);
        }

        if ($method === 'post') {
            return $request->post($url, $payload ?? []);
        }

        if ($method === 'patch') {
            return $request->patch($url, $payload ?? []);
        }

        if ($method === 'delete') {
            return $request->delete($url);
        }

        return $request->get($url);
    }
}
