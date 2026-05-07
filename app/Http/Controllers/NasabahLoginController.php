<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NasabahLoginController extends Controller
{
    public function showLogin()
    {
        return view('nasabah.login');
    }

    public function authenticate(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $validated['username'];
        $password = $validated['password'];

        try {
            // Query ke Supabase untuk cari nasabah aktif
            $supabaseUrl = env('SUPABASE_URL');
            $supabaseKey = env('SUPABASE_KEY');
            
            // Sesuaikan dengan kolom tabel Supabase: `user_name`, `email`, `status`
            $query = "nasabah?select=*&or=(user_name.eq." . urlencode($username) . ",email.eq." . urlencode($username) . ")&status=eq.aktif";
            
            $response = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/' . $query);

            $users = $response->json();

            if (is_array($users) && count($users) > 0) {
                $user = $users[0];

                // Verifikasi password dengan metode Android (jika ada salt)
                $loginSuccess = $this->verifyPasswordLikeAndroid($password, $user['password'], $user['salt'] ?? null);

                // Fallback untuk user lama atau jika tabel tidak menyimpan salt: coba password_verify dan plain compare
                if (!$loginSuccess) {
                    if (isset($user['password']) && password_verify($password, $user['password'])) {
                        $loginSuccess = true;
                    } elseif (isset($user['password']) && $password === $user['password']) {
                        $loginSuccess = true;
                    }
                }

                if ($loginSuccess) {
                    // Set session (samakan dengan kolom tabel: `nama_lengkap`, `user_name`)
                    session([
                        'id_nasabah' => $user['id_nasabah'],
                        'nama_nasabah' => $user['nama_lengkap'] ?? ($user['nama_nasabah'] ?? null),
                        'username' => $user['user_name'] ?? ($user['username'] ?? null),
                        'email' => $user['email'] ?? null,
                        'saldo' => $user['saldo'] ?? 0,
                    ]);

                    return redirect()->route('nasabah.dashboard')->with('success', 'Login berhasil!');
                } else {
                    return back()->withInput()->with('error', 'Username/email atau password salah!');
                }
            } else {
                return back()->withInput()->with('error', 'Username/email atau password salah!');
            }
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Verifikasi password dengan metode Android (SHA256 + Salt + Base64)
     */
    private function verifyPasswordLikeAndroid($inputPassword, $storedHash, $salt)
    {
        if (empty($salt)) {
            return false;
        }

        try {
            // Decode salt dari Base64
            $saltBinary = base64_decode($salt);

            // Hash seperti Android
            $hashedInput = hash('sha256', $saltBinary . $inputPassword, true);
            $hashedInputBase64 = base64_encode($hashedInput);

            return $hashedInputBase64 === $storedHash;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('nasabah.login')->with('success', 'Anda telah berhasil logout');
    }
}
