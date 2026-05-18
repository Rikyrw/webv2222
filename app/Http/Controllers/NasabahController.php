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
        $nasabahsMeta = [
            'page' => 1,
            'has_next' => false,
            'has_prev' => false,
            'offset' => 0,
        ];
        $databaseError = null;

        try {
            // Handle POST action: approve or reject nasabah
            if ($request->isMethod('post') && $request->filled('action') && $request->filled('id_nasabah')) {
                $id = (int) $request->input('id_nasabah');
                $action = $request->input('action'); // expected: aktifkan | tolak

                // Validate CSRF token
                if (!hash_equals(session('_token', ''), $request->input('_token', ''))) {
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

            $page = max(1, (int) $request->get('page', 1));
            $result = $this->fetchNasabahList($page, 10);
            $nasabahs = $result['items'];
            $nasabahsMeta = $result['meta'];
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
            'nasabahsMeta',
            'databaseError'
        ));
    }

    public function edit(int $id)
    {
        $activePage = 'nasabah';
        $pageTitle = 'Edit Nasabah';

        $nasabah = $this->fetchNasabahById($id);
        if (!$nasabah) {
            abort(404);
        }

        return view('admin.edit_nasabah', compact('activePage', 'pageTitle', 'nasabah'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'alamat' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:30',
            'saldo' => 'nullable|numeric|min:0',
            'status' => 'required|in:aktif,menunggu,nonaktif',
            'user_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
        ]);

        try {
            $payload = [
                'nama_lengkap' => $request->input('nama_lengkap'),
                'alamat' => $request->input('alamat') ?: null,
                'no_hp' => $request->input('no_hp') ?: null,
                'saldo' => $request->input('saldo') ?? 0,
                'status' => $request->input('status'),
                'user_name' => $request->input('user_name') ?: null,
                'email' => $request->input('email') ?: null,
            ];

            $response = $this->supabaseRequest(
                'patch',
                '/rest/v1/nasabah?id_nasabah=eq.' . $id,
                $payload,
                true
            );

            if (!$response->successful()) {
                throw new \RuntimeException('Supabase update gagal');
            }

            return redirect()->route('admin.nasabah.daftar')->with('flash_nasabah', 'Data nasabah berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('NasabahController Update Error: ' . $e->getMessage());
            return redirect()->back()->with('flash_nasabah', 'Gagal memperbarui data nasabah.');
        }
    }

    public function destroy(int $id)
    {
        try {
            $response = $this->supabaseRequest(
                'delete',
                '/rest/v1/nasabah?id_nasabah=eq.' . $id,
                null,
                true
            );

            if (!$response->successful()) {
                throw new \RuntimeException('Supabase delete gagal');
            }

            return redirect()->route('admin.nasabah.daftar')->with('flash_nasabah', 'Nasabah berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('NasabahController Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('flash_nasabah', 'Gagal menghapus nasabah.');
        }
    }

    public function riwayat(int $id)
    {
        $activePage = 'nasabah';
        $pageTitle = 'Riwayat Nasabah';
        $nasabah = $this->fetchNasabahById($id);
        if (!$nasabah) {
            abort(404);
        }

        $setorList = [];
        $penarikanList = [];
        $databaseError = null;

        try {
            $setorResponse = $this->supabaseRequest(
                'get',
                '/rest/v1/transaksi_setor?select=id_transaksi_setor,total_nilai,status,tanggal_setor,tanggal_proses,detail_setor(berat_kg,jenis_sampah(nama_jenis))&id_nasabah=eq.' . $id . '&order=tanggal_setor.desc&limit=20',
                null,
                false
            );

            if ($setorResponse->successful()) {
                $items = $setorResponse->json();
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $totalBerat = 0;
                        $jenisList = [];
                        $detailItems = isset($item['detail_setor']) && is_array($item['detail_setor']) ? $item['detail_setor'] : [];
                        foreach ($detailItems as $detail) {
                            $totalBerat += isset($detail['berat_kg']) ? (float) $detail['berat_kg'] : 0;
                            $jenisNama = isset($detail['jenis_sampah']['nama_jenis']) ? $detail['jenis_sampah']['nama_jenis'] : null;
                            if ($jenisNama) {
                                $jenisList[] = $jenisNama;
                            }
                        }

                        $setorList[] = [
                            'id' => $item['id_transaksi_setor'] ?? null,
                            'tanggal' => $item['tanggal_setor'] ?? null,
                            'tanggal_proses' => $item['tanggal_proses'] ?? null,
                            'total_berat' => $totalBerat,
                            'total_nilai' => isset($item['total_nilai']) ? (float) $item['total_nilai'] : 0,
                            'jenis' => count($jenisList) > 0 ? implode(', ', array_values(array_unique($jenisList))) : 'N/A',
                            'status' => $item['status'] ?? 'menunggu',
                        ];
                    }
                }
            }

            $penarikanResponse = $this->supabaseRequest(
                'get',
                '/rest/v1/penarikan_saldo?select=id_penarikan,jenis_penukaran,nominal,deskripsi,status,tanggal_pengajuan,tanggal_proses&id_nasabah=eq.' . $id . '&order=tanggal_pengajuan.desc&limit=20',
                null,
                false
            );

            if ($penarikanResponse->successful()) {
                $items = $penarikanResponse->json();
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $penarikanList[] = [
                            'id' => $item['id_penarikan'] ?? null,
                            'jenis' => $item['jenis_penukaran'] ?? 'Penarikan',
                            'nominal' => isset($item['nominal']) ? (float) $item['nominal'] : 0,
                            'status' => $item['status'] ?? 'menunggu',
                            'deskripsi' => $item['deskripsi'] ?? '-',
                            'tanggal' => $item['tanggal_pengajuan'] ?? null,
                            'tanggal_proses' => $item['tanggal_proses'] ?? null,
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('NasabahController Riwayat Error: ' . $e->getMessage());
            $databaseError = 'Tidak dapat mengambil riwayat nasabah.';
        }

        return view('admin.riwayat_nasabah', compact(
            'activePage',
            'pageTitle',
            'nasabah',
            'setorList',
            'penarikanList',
            'databaseError'
        ));
    }

    private function fetchNasabahList(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $limit = $perPage + 1;
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/nasabah?select=id_nasabah,user_name,nama_lengkap,email,no_hp,status,saldo,alamat,created_at,google_id,photo_url,provider&order=created_at.desc&limit=' . $limit . '&offset=' . $offset,
            null,
            false
        );

        if (!$response->successful()) {
            return [
                'items' => [],
                'meta' => [
                    'page' => $page,
                    'has_next' => false,
                    'has_prev' => $page > 1,
                    'offset' => $offset,
                ],
            ];
        }

        $items = $response->json();
        if (!is_array($items)) {
            return [
                'items' => [],
                'meta' => [
                    'page' => $page,
                    'has_next' => false,
                    'has_prev' => $page > 1,
                    'offset' => $offset,
                ],
            ];
        }

        $hasNext = count($items) > $perPage;
        if ($hasNext) {
            $items = array_slice($items, 0, $perPage);
        }

        $mapped = [];
        foreach ($items as $item) {
            $tanggal = $item['created_at'] ?? null;
            $mapped[] = [
                'id_nasabah' => $item['id_nasabah'] ?? null,
                'user_name' => $item['user_name'] ?? null,
                'nama_nasabah' => $item['nama_lengkap'] ?? null,
                'email' => $item['email'] ?? null,
                'alamat' => $item['alamat'] ?? '-',
                'no_hp' => $item['no_hp'] ?? '-',
                'saldo' => $item['saldo'] ?? 0,
                'status_akun' => $item['status'] ?? 'verifikasi',
                'tanggal_daftar' => is_string($tanggal) ? $tanggal : '-',
                'google_id' => $item['google_id'] ?? null,
                'photo_url' => $item['photo_url'] ?? null,
                'provider' => $item['provider'] ?? null,
            ];
        }

        return [
            'items' => $mapped,
            'meta' => [
                'page' => $page,
                'has_next' => $hasNext,
                'has_prev' => $page > 1,
                'offset' => $offset,
            ],
        ];
    }

    private function fetchNasabahById(int $id): ?array
    {
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/nasabah?select=id_nasabah,nama_lengkap,alamat,no_hp,saldo,status,user_name,email,created_at&id_nasabah=eq.' . $id . '&limit=1',
            null,
            false
        );

        if (!$response->successful()) {
            return null;
        }

        $items = $response->json();
        if (!is_array($items) || count($items) === 0) {
            return null;
        }

        return $items[0];
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
