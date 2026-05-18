<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NasabahPasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('nasabah.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($validated['email']));
        $user = $this->findNasabahByEmail($email);

        if ($user && $this->isManualAccount($user)) {
            if ($this->isFirebaseBackedPassword($user['password'] ?? null)) {
                $this->sendFirebasePasswordReset($email);
            } else {
                $this->sendLocalPasswordReset($email);
            }
        }

        return back()->with('success', 'Jika email terdaftar sebagai akun manual, link reset password sudah dikirim.');
    }

    public function showResetForm(string $token, Request $request)
    {
        return view('nasabah.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'max:8', 'confirmed'],
        ]);

        $email = strtolower(trim($validated['email']));
        $resetRecord = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (
            !$resetRecord ||
            !$resetRecord->created_at ||
            now()->diffInMinutes($resetRecord->created_at) > 60 ||
            !Hash::check($validated['token'], $resetRecord->token)
        ) {
            return back()->withInput()->withErrors([
                'email' => 'Link reset password tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        $user = $this->findNasabahByEmail($email);

        if (!$user || !$this->isManualAccount($user) || $this->isFirebaseBackedPassword($user['password'] ?? null)) {
            return redirect()->route('nasabah.login')->with('error', 'Link reset password tidak dapat dipakai untuk akun ini.');
        }

        $updated = $this->updateNasabahPassword((int) $user['id_nasabah'], $validated['password']);

        if (!$updated) {
            return back()->withInput()->withErrors([
                'email' => 'Gagal memperbarui password. Silakan coba lagi.',
            ]);
        }

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect()->route('nasabah.login')->with('success', 'Password berhasil diubah. Silakan login kembali.');
    }

    private function sendLocalPasswordReset(string $email): void
    {
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $resetUrl = route('nasabah.password.reset.form', [
            'token' => $token,
            'email' => $email,
        ]);

        Mail::send('emails.nasabah-password-reset', [
            'resetUrl' => $resetUrl,
        ], function ($message) use ($email): void {
            $message->to($email)->subject('Reset Password GreenPoint');
        });
    }

    private function sendFirebasePasswordReset(string $email): void
    {
        $firebaseApiKey = config('services.firebase.api_key');

        if (!$firebaseApiKey) {
            Log::warning('Firebase password reset skipped because FIREBASE_API_KEY is missing.');
            return;
        }

        try {
            $response = Http::acceptJson()->post(
                'https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode?key=' . urlencode($firebaseApiKey),
                [
                    'requestType' => 'PASSWORD_RESET',
                    'email' => $email,
                ]
            );

            if (!$response->successful()) {
                Log::warning('Firebase password reset request failed.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $exception) {
            Log::warning('Firebase password reset request failed unexpectedly.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function findNasabahByEmail(string $email): ?array
    {
        $response = Http::acceptJson()->withHeaders([
            'apikey' => env('SUPABASE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
        ])->get(rtrim((string) env('SUPABASE_URL'), '/') . '/rest/v1/nasabah', [
            'select' => '*',
            'email' => 'eq.' . $email,
            'limit' => 1,
        ]);

        if (!$response->successful()) {
            Log::warning('Failed to fetch nasabah for password reset.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        $rows = $response->json();

        return is_array($rows) && isset($rows[0]) && is_array($rows[0]) ? $rows[0] : null;
    }

    private function updateNasabahPassword(int $idNasabah, string $password): bool
    {
        $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY') ?: env('SUPABASE_KEY');

        $response = Http::acceptJson()->withHeaders([
            'apikey' => $serviceKey,
            'Authorization' => 'Bearer ' . $serviceKey,
            'Content-Type' => 'application/json',
            'Prefer' => 'return=representation',
        ])->patch(rtrim((string) env('SUPABASE_URL'), '/') . '/rest/v1/nasabah?id_nasabah=eq.' . $idNasabah, [
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'salt' => null,
        ]);

        return $response->successful();
    }

    private function isManualAccount(array $user): bool
    {
        return empty($user['google_sub']);
    }

    private function isFirebaseBackedPassword(?string $storedPassword): bool
    {
        return is_string($storedPassword) && str_starts_with($storedPassword, 'firebase-auth:');
    }
}
