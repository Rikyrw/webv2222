<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
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

        $username = trim($validated['username']);
        $password = $validated['password'];

        if (str_contains($username, '@')) {
            $username = strtolower($username);

            if (! filter_var($username, FILTER_VALIDATE_EMAIL)) {
                return back()
                    ->withInput()
                    ->withErrors(['username' => 'Format email login tidak valid. Periksa kembali penulisan email Anda.']);
            }
        }

        try {
            $user = $this->findActiveNasabahForLogin($username);

            if ($user) {
                if ($this->emailNeedsVerification($user)) {
                    return $this->redirectToVerificationNotice($user);
                }

                $storedPassword = $user['password'] ?? null;

                // Verifikasi password dengan metode Android (jika ada salt)
                $loginSuccess = ! $this->mustUseFirebaseOnly($storedPassword)
                    && $this->verifyPasswordLikeAndroid($password, $storedPassword, $user['salt'] ?? null);

                // Fallback untuk user lama atau jika tabel tidak menyimpan salt: coba password_verify dan plain compare
                if (! $loginSuccess && ! $this->mustUseFirebaseOnly($storedPassword)) {
                    if ($storedPassword && password_verify($password, $storedPassword)) {
                        $loginSuccess = true;
                    } elseif ($storedPassword && $password === $storedPassword) {
                        $loginSuccess = true;
                    }
                }

                // Firebase menjadi fallback untuk:
                // 1. akun lama dari mobile yang menyimpan marker `firebase-auth:{uid}`;
                // 2. akun lokal bcrypt yang baru saja reset password lewat Firebase.
                if (! $loginSuccess) {
                    $firebaseUid = $this->signInWithFirebase($user['email'] ?? null, $password);

                    if ($firebaseUid) {
                        if ($this->isFirebaseUidMarker($storedPassword) && ! hash_equals(Str::after($storedPassword, 'firebase-auth:'), $firebaseUid)) {
                            $loginSuccess = false;
                        } else {
                            $loginSuccess = true;

                            if (! $this->isFirebaseUidMarker($storedPassword)) {
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
                    session()->forget(NasabahEmailVerificationController::SESSION_KEY);

                    return redirect()->route('nasabah.dashboard')->with('success', 'Login berhasil!');
                } else {
                    return back()->withInput()->with('error', 'Username/email atau password salah!');
                }
            } else {
                return back()->withInput()->with('error', 'Username/email atau password salah!');
            }
        } catch (\Throwable $e) {
            Log::error('Nasabah login failed unexpectedly.', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', $this->loginSystemErrorMessage($e));
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
            $hashedInput = hash('sha256', $saltBinary.$inputPassword, true);
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

    private function emailNeedsVerification(array $user): bool
    {
        return empty($user['google_sub'])
            && array_key_exists('email_verified_at', $user)
            && empty($user['email_verified_at']);
    }

    private function findActiveNasabahForLogin(string $username): ?array
    {
        return Nasabah::where('status', 'aktif')
            ->where(function ($query) use ($username) {
                $query->where('user_name', $username)
                    ->orWhere('email', $username);
            })
            ->orderByDesc('id_nasabah')
            ->first()
            ?->getAttributes();
    }

    private function loginSystemErrorMessage(\Throwable $e): string
    {
        $message = $e->getMessage();

        if (
            str_contains($message, 'SQLSTATE[08006]')
            || str_contains($message, 'could not translate host name')
            || str_contains($message, 'No such host is known')
            || str_contains($message, 'Network is unreachable')
            || str_contains($message, 'Connection refused')
        ) {
            return 'Database belum bisa dijangkau. Periksa koneksi database/pooler Supabase, lalu coba lagi.';
        }

        return 'Terjadi kesalahan sistem. Silakan coba beberapa saat lagi.';
    }

    private function redirectToVerificationNotice(array $user)
    {
        session()->put(NasabahEmailVerificationController::SESSION_KEY, [
            'id_nasabah' => $user['id_nasabah'] ?? null,
            'email' => $user['email'] ?? null,
        ]);

        return redirect()
            ->route('nasabah.verification.notice')
            ->with('error', 'Email belum diverifikasi.');
    }

    private function signInWithFirebase(?string $email, string $password): ?string
    {
        $firebaseApiKey = config('services.firebase.api_key');

        if (! $firebaseApiKey || ! $email) {
            Log::warning('Firebase-backed nasabah login could not be verified because configuration or account data is incomplete.', [
                'has_api_key' => (bool) $firebaseApiKey,
                'has_email' => (bool) $email,
            ]);

            return null;
        }

        try {
            $response = Http::acceptJson()->post(
                'https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key='.urlencode($firebaseApiKey),
                [
                    'email' => $email,
                    'password' => $password,
                    'returnSecureToken' => true,
                ]
            );

            if (! $response->successful()) {
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
        try {
            Nasabah::where('id_nasabah', $idNasabah)->update([
                'password' => 'firebase-auth:'.$firebaseUid,
            ]);
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
