<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NasabahRegisterController extends Controller
{
    public function showRegister()
    {
        return view('nasabah.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:8',
            'konfirmasi_password' => 'required|string|max:8',
            'alamat' => 'nullable|string',
            'no_hp' => 'required|string|max:20',
        ]);

        // Validasi: Konfirmasi password
        if ($validated['password'] !== $validated['konfirmasi_password']) {
            return back()->withInput()->withErrors(['password' => 'Password dan konfirmasi password tidak sama!']);
        }

        // Validasi: Panjang password
        if (strlen($validated['password']) > 8) {
            return back()->withInput()->withErrors(['password' => 'Password maksimal 8 karakter!']);
        }

        try {
            // Check existing user
            $supabaseUrl = env('SUPABASE_URL');
            $supabaseKey = env('SUPABASE_KEY');
            
            // Cek apakah email atau user_name sudah terdaftar (sesuaikan kolom)
            $query = "nasabah?select=id_nasabah&or=(email.eq." . urlencode($validated['email']) . ",user_name.eq." . urlencode($validated['username']) . ")";
            
            $response = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/' . $query);

            $existing = $response->json();

            if (is_array($existing) && count($existing) > 0) {
                return back()->withInput()->withErrors(['email' => 'Email atau username sudah terdaftar!']);
            }

            // Hash password securely with PHP's password_hash (no separate salt column)
            $password_hash = password_hash($validated['password'], PASSWORD_BCRYPT);

            // Create new user (samakan dengan kolom tabel Supabase)
            $newUser = [
                'nama_lengkap' => $validated['nama'],
                'user_name' => $validated['username'],
                'email' => $validated['email'],
                'password' => $password_hash,
                'alamat' => $validated['alamat'] ?? '',
                'no_hp' => $validated['no_hp'],
                'created_at' => now()->toDateTimeString(),
                'status' => 'aktif',
                'saldo' => 0,
            ];

            // Use service role key for inserts if available (service role bypasses RLS)
            $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY') ?: $supabaseKey;
            if ($serviceKey === $supabaseKey) {
                \Log::warning('Using SUPABASE_KEY for insert; if RLS is enabled this may be blocked. Consider setting SUPABASE_SERVICE_ROLE_KEY to the service_role key.');
            } else {
                \Log::info('Using SUPABASE_SERVICE_ROLE_KEY for insert operations');
            }

            $insertResponse = Http::withHeaders([
                'apikey' => $serviceKey,
                'Authorization' => 'Bearer ' . $serviceKey,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation',
            ])->post($supabaseUrl . '/rest/v1/nasabah', $newUser);

            $status = $insertResponse->status();
            $body = $insertResponse->body();

            // Log response for debugging
            \Log::info('Supabase insert nasabah status: ' . $status . ' body: ' . $body);

            $result = null;
            try {
                $result = $insertResponse->json();
            } catch (\Exception $e) {
                // ignore JSON parse errors
            }

            if ($insertResponse->successful() && is_array($result) && count($result) > 0) {
                return redirect()->route('nasabah.login')->with('success', 'Pendaftaran berhasil! Silakan login.');
            } else {
                // Try to extract a helpful message from Supabase response
                $errorMsg = 'Gagal mendaftar.';
                if (!empty($result) && is_array($result)) {
                    // common Supabase error shape may contain 'message' or 'hint' or 'details'
                    if (isset($result['message'])) {
                        $errorMsg = 'Gagal mendaftar: ' . $result['message'];
                    } elseif (isset($result['hint'])) {
                        $errorMsg = 'Gagal mendaftar: ' . $result['hint'];
                    } else {
                        $errorMsg = 'Gagal mendaftar: ' . json_encode($result);
                    }
                } else {
                    $errorMsg = 'Gagal mendaftar (HTTP ' . $status . '). ' . substr($body, 0, 300);
                }

                return back()->withInput()->withErrors(['email' => $errorMsg]);
            }
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['email' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
