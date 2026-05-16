<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NasabahTransaksiSetorController extends Controller
{
    public function index(Request $request)
    {
        $activePage = 'setor';

        $user = null;
        $id = session('id_nasabah');

        // Try to load real user profile from Supabase when logged in
        if ($id) {
            try {
                $supabaseUrl = env('SUPABASE_URL');
                $supabaseKey = env('SUPABASE_KEY');

                $resp = Http::withHeaders([
                    'apikey' => $supabaseKey,
                    'Authorization' => 'Bearer ' . $supabaseKey,
                ])->get($supabaseUrl . '/rest/v1/nasabah?select=*&id_nasabah=eq.' . intval($id));

                $data = $resp->json();
                if (is_array($data) && count($data) > 0) {
                    $row = $data[0];
                    $user = [
                        'id_nasabah' => $row['id_nasabah'] ?? $id,
                        'nama_nasabah' => $row['nama_lengkap'] ?? ($row['nama_nasabah'] ?? session('nama_nasabah')),
                        'alamat' => $row['alamat'] ?? session('alamat'),
                        'saldo' => isset($row['saldo']) ? (float)$row['saldo'] : (session('saldo') ?? 0),
                        'email' => $row['email'] ?? session('email'),
                        'no_hp' => $row['no_hp'] ?? session('no_hp'),
                        'username' => $row['user_name'] ?? session('username'),
                        'tanggal_daftar' => $row['created_at'] ?? null,
                    ];

                    // sync session
                    session([
                        'nama_nasabah' => $user['nama_nasabah'],
                        'alamat' => $user['alamat'],
                        'saldo' => $user['saldo'],
                        'email' => $user['email'],
                        'no_hp' => $user['no_hp'],
                        'username' => $user['username'],
                    ]);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to fetch nasabah for setor: ' . $e->getMessage());
            }
        }

        // Fallback to session or dummy if still null
        if (!$user) {
            $user = [
                'id_nasabah' => session('id_nasabah') ?? 1,
                'nama_nasabah' => session('nama_nasabah') ?? 'Ridho Pratama',
                'alamat' => session('alamat') ?? 'Jl. Merdeka No. 42, Jakarta Selatan',
                'saldo' => session('saldo') ?? 250000,
                'email' => session('email') ?? '',
                'no_hp' => session('no_hp') ?? '',
                'username' => session('username') ?? '',
            ];
        }

        $waste_types = $this->fetchWasteTypes();

        session([
            'nama_nasabah' => $user['nama_nasabah'],
            'saldo' => $user['saldo'],
            'alamat' => $user['alamat']
        ]);

        // Handle setor transaction submission
        $success = null;
        $error = null;
        if ($request->isMethod('post') && $request->has('submit_transaction')) {
            $waste_items = $request->input('waste_items', []);
            $waste_photos = $request->input('waste_photos', []);

            if (empty($waste_items)) {
                $error = 'Tambahkan minimal 1 item sebelum mengajukan setor';
            } elseif (!$user['id_nasabah']) {
                $error = 'Akun tidak ditemukan. Silakan login ulang.';
            } else {
                $validationError = $this->validateWasteItems($waste_items, $waste_photos);
                if ($validationError) {
                    $error = $validationError;
                } else {
                    try {
                        $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY') ?: env('SUPABASE_KEY');
                        $supabaseUrl = env('SUPABASE_URL');

                        $calcResult = $this->calculateTotalsFromSupabase($waste_items, $serviceKey, $supabaseUrl);
                        if ($calcResult['error']) {
                            $error = $calcResult['error'];
                        } else {
                            $totalNilai = $calcResult['total_nilai'];
                            $detailRows = $calcResult['detail_rows'];

                            $transaksiPayload = [
                                'id_nasabah' => intval($user['id_nasabah']),
                                'total_nilai' => $totalNilai,
                                'tanggal_setor' => date('Y-m-d'),
                                'status' => 'menunggu',
                            ];

                            $transaksiResp = Http::withHeaders([
                                'apikey' => $serviceKey,
                                'Authorization' => 'Bearer ' . $serviceKey,
                                'Content-Type' => 'application/json',
                                'Prefer' => 'return=representation',
                            ])->post($supabaseUrl . '/rest/v1/transaksi_setor', $transaksiPayload);

                            if (!$transaksiResp->successful()) {
                                $error = 'Gagal mengajukan transaksi setor (HTTP ' . $transaksiResp->status() . ').';
                            } else {
                                $transaksiData = $transaksiResp->json();
                                $transaksiId = is_array($transaksiData) && count($transaksiData) > 0
                                    ? ($transaksiData[0]['id_transaksi_setor'] ?? null)
                                    : null;

                                if (!$transaksiId) {
                                    $error = 'Transaksi dibuat tetapi ID tidak ditemukan.';
                                } else {
                                    foreach ($detailRows as &$row) {
                                        $row['id_transaksi_setor'] = intval($transaksiId);
                                    }
                                    unset($row);

                                    $detailResp = Http::withHeaders([
                                        'apikey' => $serviceKey,
                                        'Authorization' => 'Bearer ' . $serviceKey,
                                        'Content-Type' => 'application/json',
                                        'Prefer' => 'return=representation',
                                    ])->post($supabaseUrl . '/rest/v1/detail_setor', $detailRows);

                                    if (!$detailResp->successful()) {
                                        $error = 'Transaksi dibuat, tetapi detail setor gagal disimpan.';
                                    } else {
                                        $fotoRows = [];
                                        foreach ($waste_items as $index => $item) {
                                            $foto = $waste_photos[$index] ?? null;
                                            if ($foto) {
                                                $fotoRows[] = [
                                                    'id_transaksi_setor' => intval($transaksiId),
                                                    'foto_url' => $foto,
                                                ];
                                            }
                                        }

                                        if (count($fotoRows) > 0) {
                                            $fotoResp = Http::withHeaders([
                                                'apikey' => $serviceKey,
                                                'Authorization' => 'Bearer ' . $serviceKey,
                                                'Content-Type' => 'application/json',
                                                'Prefer' => 'return=representation',
                                            ])->post($supabaseUrl . '/rest/v1/foto_setor', $fotoRows);

                                            if (!$fotoResp->successful()) {
                                                $error = 'Transaksi dibuat, tetapi foto gagal disimpan.';
                                            }
                                        }

                                        if (!$error) {
                                            $success = 'Transaksi setor sampah berhasil diajukan! Status: Menunggu persetujuan admin.';
                                        }
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error('Setor transaksi error: ' . $e->getMessage());
                        $error = 'Terjadi kesalahan saat mengajukan setor.';
                    }
                }
            }
        }

        return view('nasabah.transaksi_setor', [
            'activePage' => $activePage,
            'user' => $user,
            'waste_types' => $waste_types,
            'success' => $success,
            'error' => $error
        ]);
    }

    private function fetchWasteTypes(): array
    {
        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = env('SUPABASE_KEY');
        if (!$supabaseUrl || !$supabaseKey) {
            return [];
        }

        try {
            $resp = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/jenis_sampah?select=id_jenis_sampah,nama_jenis,harga_per_kg&order=nama_jenis.asc');

            $rows = $resp->json();
            if (!is_array($rows)) {
                return [];
            }

            return array_map(function ($row) {
                return [
                    'id_jenis' => $row['id_jenis_sampah'] ?? null,
                    'nama_jenis' => $row['nama_jenis'] ?? '-',
                    'harga_per_kg' => $row['harga_per_kg'] ?? 0,
                ];
            }, $rows);
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch jenis sampah: ' . $e->getMessage());
            return [];
        }
    }

    private function validateWasteItems(array $wasteItems, array $wastePhotos): ?string
    {
        foreach ($wasteItems as $index => $item) {
            $idJenis = isset($item['id_jenis']) ? intval($item['id_jenis']) : 0;
            $berat = isset($item['berat']) ? (float) $item['berat'] : 0;

            if ($idJenis <= 0) {
                return 'Jenis sampah tidak valid.';
            }

            if ($berat < 1) {
                return 'Berat minimal 1 kg untuk setiap item.';
            }

            if (!isset($wastePhotos[$index]) || trim((string) $wastePhotos[$index]) === '') {
                return 'Setiap item wajib memiliki foto.';
            }
        }

        return null;
    }

    private function calculateTotalsFromSupabase(array $wasteItems, string $serviceKey, string $supabaseUrl): array
    {
        $ids = array_values(array_unique(array_map(function ($item) {
            return intval($item['id_jenis'] ?? 0);
        }, $wasteItems)));

        $ids = array_filter($ids, function ($id) {
            return $id > 0;
        });

        if (count($ids) === 0) {
            return ['error' => 'Jenis sampah tidak ditemukan.', 'total_nilai' => 0, 'detail_rows' => []];
        }

        $idFilter = implode(',', $ids);
        $resp = Http::withHeaders([
            'apikey' => $serviceKey,
            'Authorization' => 'Bearer ' . $serviceKey,
        ])->get($supabaseUrl . '/rest/v1/jenis_sampah?select=id_jenis_sampah,harga_per_kg&id_jenis_sampah=in.(' . $idFilter . ')');

        if (!$resp->successful()) {
            return ['error' => 'Gagal memuat harga jenis sampah.', 'total_nilai' => 0, 'detail_rows' => []];
        }

        $rows = $resp->json();
        if (!is_array($rows)) {
            return ['error' => 'Data jenis sampah tidak valid.', 'total_nilai' => 0, 'detail_rows' => []];
        }

        $priceMap = [];
        foreach ($rows as $row) {
            $id = $row['id_jenis_sampah'] ?? null;
            if ($id !== null) {
                $priceMap[intval($id)] = (float) ($row['harga_per_kg'] ?? 0);
            }
        }

        $totalNilai = 0;
        $detailRows = [];

        foreach ($wasteItems as $item) {
            $idJenis = intval($item['id_jenis'] ?? 0);
            $berat = (float) ($item['berat'] ?? 0);
            if (!isset($priceMap[$idJenis])) {
                return ['error' => 'Jenis sampah tidak ditemukan.', 'total_nilai' => 0, 'detail_rows' => []];
            }

            $harga = $priceMap[$idJenis];
            $subtotal = round($harga * $berat, 2);
            $totalNilai += $subtotal;

            $detailRows[] = [
                'id_jenis' => $idJenis,
                'berat_kg' => $berat,
                'harga_kg' => $harga,
                'subtotal' => $subtotal,
                'status_item' => 'pending',
            ];
        }

        return ['error' => null, 'total_nilai' => $totalNilai, 'detail_rows' => $detailRows];
    }
}
