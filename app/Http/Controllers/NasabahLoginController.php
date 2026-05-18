<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
                $storedPassword = $user['password'] ?? null;

                // Verifikasi password dengan metode Android (jika ada salt)
                $loginSuccess = !$this->mustUseFirebaseOnly($storedPassword)
                    && $this->verifyPasswordLikeAndroid($password, $storedPassword, $user['salt'] ?? null);

                // Fallback untuk user lama atau jika tabel tidak menyimpan salt: coba password_verify dan plain compare
                if (!$loginSuccess && !$this->mustUseFirebaseOnly($storedPassword)) {
                    if ($storedPassword && password_verify($password, $storedPassword)) {
                        $loginSuccess = true;
                    } elseif ($storedPassword && $password === $storedPassword) {
                        $loginSuccess = true;
                    }
                }

                // Firebase menjadi fallback untuk:
                // 1. akun lama dari mobile yang menyimpan marker `firebase-auth:{uid}`;
                // 2. akun lokal bcrypt yang baru saja reset password lewat Firebase.
                if (!$loginSuccess) {
                    $firebaseUid = $this->signInWithFirebase($user['email'] ?? null, $password);

                    if ($firebaseUid) {
                        if ($this->isFirebaseUidMarker($storedPassword) && !hash_equals(Str::after($storedPassword, 'firebase-auth:'), $firebaseUid)) {
                            $loginSuccess = false;
                        } else {
                            $loginSuccess = true;

                            if (!$this->isFirebaseUidMarker($storedPassword)) {
                                $this->migrateToFirebaseBackedPassword((int) $user['id_nasabah'], $firebaseUid);
                            }
                        }
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

    private function isFirebaseBackedPassword(?string $storedPassword): bool
    {
        return is_string($storedPassword) && (str_starts_with($storedPassword, 'firebase-auth:') || $storedPassword === 'firebase-auth-pending');
    }

    private function isFirebaseUidMarker(?string $storedPassword): bool
    {
        return $this->isFirebaseBackedPassword($storedPassword) && $storedPassword !== 'firebase-auth-pending';
    }

    private function mustUseFirebaseOnly(?string $storedPassword): bool
    {
        return $this->isFirebaseBackedPassword($storedPassword);
    }

    private function signInWithFirebase(?string $email, string $password): ?string
    {
        $firebaseApiKey = config('services.firebase.api_key');

        if (!$firebaseApiKey || !$email) {
            Log::warning('Firebase-backed nasabah login could not be verified because configuration or account data is incomplete.', [
                'has_api_key' => (bool) $firebaseApiKey,
                'has_email' => (bool) $email,
            ]);

            return null;
        }

        try {
            $response = Http::acceptJson()->post(
                'https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=' . urlencode($firebaseApiKey),
                [
                    'email' => $email,
                    'password' => $password,
                    'returnSecureToken' => true,
                ]
            );

            if (!$response->successful()) {
                return null;
            }

            $payload = $response->json();
            $firebaseLocalId = isset($payload['localId']) ? (string) $payload['localId'] : '';

            return $firebaseLocalId !== '' ? $firebaseLocalId : null;
        } catch (\Throwable $exception) {
            Log::warning('Firebase password verification failed unexpectedly.', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function migrateToFirebaseBackedPassword(int $idNasabah, string $firebaseUid): void
    {
        $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY') ?: env('SUPABASE_KEY');

        try {
            $response = Http::acceptJson()->withHeaders([
                'apikey' => $serviceKey,
                'Authorization' => 'Bearer ' . $serviceKey,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=minimal',
            ])->patch(rtrim((string) env('SUPABASE_URL'), '/') . '/rest/v1/nasabah?id_nasabah=eq.' . $idNasabah, [
                'password' => 'firebase-auth:' . $firebaseUid,
            ]);

            if (!$response->successful()) {
                Log::warning('Failed to migrate nasabah password marker to Firebase.', [
                    'id_nasabah' => $idNasabah,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $exception) {
            Log::warning('Failed to migrate nasabah password marker to Firebase unexpectedly.', [
                'id_nasabah' => $idNasabah,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('nasabah.login')->with('success', 'Anda telah berhasil logout');
    }
}
