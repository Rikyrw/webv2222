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

        // Dummy jenis sampah
        $waste_types = [
            [
                'id_jenis' => 1,
                'nama_jenis' => 'Kertas',
                'harga_per_kg' => 1500
            ],
            [
                'id_jenis' => 2,
                'nama_jenis' => 'Plastik',
                'harga_per_kg' => 2000
            ],
            [
                'id_jenis' => 3,
                'nama_jenis' => 'Logam',
                'harga_per_kg' => 5000
            ],
            [
                'id_jenis' => 4,
                'nama_jenis' => 'Kaca',
                'harga_per_kg' => 1000
            ]
        ];

        session([
            'nama_nasabah' => $user['nama_nasabah'],
            'saldo' => $user['saldo'],
            'alamat' => $user['alamat']
        ]);

        // Handle profile update
        $profile_success = null;
        $profile_error = null;
        if ($request->isMethod('post') && $request->has('update_profile')) {
            $validated = $request->validate([
                'nama_nasabah' => 'required|string|max:255',
                'alamat' => 'nullable|string|max:500',
            ], [
                'nama_nasabah.required' => 'Nama lengkap harus diisi',
                'nama_nasabah.max' => 'Nama lengkap tidak boleh lebih dari 255 karakter',
                'alamat.max' => 'Alamat tidak boleh lebih dari 500 karakter',
            ]);

            // Attempt to PATCH Supabase if logged in
            if ($user['id_nasabah']) {
                try {
                    $supabaseUrl = env('SUPABASE_URL');
                    $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY') ?: env('SUPABASE_KEY');

                    $payload = [
                        'nama_lengkap' => $validated['nama_nasabah'],
                        'alamat' => $validated['alamat'] ?? '',
                    ];

                    $resp = Http::withHeaders([
                        'apikey' => $serviceKey,
                        'Authorization' => 'Bearer ' . $serviceKey,
                        'Content-Type' => 'application/json',
                        'Prefer' => 'return=representation',
                    ])->patch($supabaseUrl . '/rest/v1/nasabah?id_nasabah=eq.' . intval($user['id_nasabah']), $payload);

                    $body = $resp->body();
                    \Log::info('Supabase update nasabah from setor: ' . $resp->status() . ' ' . substr($body,0,300));

                    $result = null;
                    try { $result = $resp->json(); } catch (\Exception $e) { }

                    if ($resp->successful() && is_array($result) && count($result) > 0) {
                        $row = $result[0];
                        session([
                            'nama_nasabah' => $row['nama_lengkap'] ?? $validated['nama_nasabah'],
                            'alamat' => $row['alamat'] ?? ($validated['alamat'] ?? ''),
                        ]);
                        $user['nama_nasabah'] = session('nama_nasabah');
                        $user['alamat'] = session('alamat');
                        $profile_success = 'Profil berhasil diperbarui';
                    } else {
                        $profile_error = 'Gagal memperbarui profil.';
                        if (!empty($result) && is_array($result)) {
                            $profile_error = isset($result['message']) ? $result['message'] : json_encode($result);
                        } else {
                            $profile_error = 'Gagal memperbarui profil (HTTP ' . $resp->status() . '). ' . substr($body,0,300);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Setor profile update error: ' . $e->getMessage());
                    $profile_error = 'Terjadi kesalahan saat memperbarui profil';
                }
            }
        }

        // Handle setor transaction submission
        $success = null;
        $error = null;
        if ($request->isMethod('post') && $request->has('submit_transaction')) {
            $waste_items = $request->input('waste_items', []);
            
            if (empty($waste_items)) {
                $error = "Tambahkan minimal 1 item sebelum mengajukan setor";
            } else {
                // Simulate successful transaction
                $total_berat = $request->input('total_berat', 0);
                $total_nilai = $request->input('total_nilai', 0);
                
                // In real implementation, this would be saved to database via Supabase
                $success = "Transaksi setor sampah berhasil diajukan! Status: Menunggu persetujuan admin.";
                
                // Update user saldo (dummy)
                $user['saldo'] = $user['saldo'] + (float)$total_nilai;
                session(['saldo' => $user['saldo']]);
            }
        }

        return view('nasabah.transaksi_setor', [
            'activePage' => $activePage,
            'user' => $user,
            'waste_types' => $waste_types,
            'profile_success' => $profile_success,
            'profile_error' => $profile_error,
            'success' => $success,
            'error' => $error
        ]);
    }
}
