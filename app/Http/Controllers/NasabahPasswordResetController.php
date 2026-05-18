<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
            if ($this->ensureFirebasePasswordAccountExists($email, $user['password'] ?? null)) {
                if ($this->sendFirebasePasswordReset($email)) {
                    $this->markAccountAsFirebasePending((int) $user['id_nasabah'], $user['password'] ?? null);
                }
            }
        }

        return back()->with('success', 'Jika email terdaftar sebagai akun manual, link reset password sudah dikirim.');
    }

    private function sendFirebasePasswordReset(string $email): bool
    {
        $firebaseApiKey = config('services.firebase.api_key');

        if (!$firebaseApiKey) {
            Log::warning('Firebase password reset skipped because FIREBASE_API_KEY is missing.');
            return false;
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

                return false;
            }

            return true;
        } catch (\Throwable $exception) {
            Log::warning('Firebase password reset request failed unexpectedly.', [
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function ensureFirebasePasswordAccountExists(string $email, ?string $storedPassword): bool
    {
        if ($this->isFirebaseBackedPassword($storedPassword)) {
            return true;
        }

        $firebaseApiKey = config('services.firebase.api_key');

        if (!$firebaseApiKey) {
            Log::warning('Firebase password account creation skipped because FIREBASE_API_KEY is missing.');
            return false;
        }

        try {
            $response = Http::acceptJson()->post(
                'https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=' . urlencode($firebaseApiKey),
                [
                    'email' => $email,
                    'password' => Str::password(32),
                    'returnSecureToken' => true,
                ]
            );

            if ($response->successful()) {
                return true;
            }

            $errorMessage = strtoupper((string) data_get($response->json(), 'error.message', ''));

            if (str_contains($errorMessage, 'EMAIL_EXISTS')) {
                return true;
            }

            Log::warning('Firebase password account creation failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Throwable $exception) {
            Log::warning('Firebase password account creation failed unexpectedly.', [
                'message' => $exception->getMessage(),
            ]);

            return false;
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

    private function isManualAccount(array $user): bool
    {
        return empty($user['google_sub']);
    }

    private function isFirebaseBackedPassword(?string $storedPassword): bool
    {
        return is_string($storedPassword) && (str_starts_with($storedPassword, 'firebase-auth:') || $storedPassword === 'firebase-auth-pending');
    }

    private function markAccountAsFirebasePending(int $idNasabah, ?string $storedPassword): void
    {
        if ($this->isFirebaseBackedPassword($storedPassword)) {
            return;
        }

        $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY') ?: env('SUPABASE_KEY');

        try {
            $response = Http::acceptJson()->withHeaders([
                'apikey' => $serviceKey,
                'Authorization' => 'Bearer ' . $serviceKey,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=minimal',
            ])->patch(rtrim((string) env('SUPABASE_URL'), '/') . '/rest/v1/nasabah?id_nasabah=eq.' . $idNasabah, [
                'password' => 'firebase-auth-pending',
            ]);

            if (!$response->successful()) {
                Log::warning('Failed to mark nasabah as Firebase pending after reset.', [
                    'id_nasabah' => $idNasabah,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $exception) {
            Log::warning('Failed to mark nasabah as Firebase pending unexpectedly.', [
                'id_nasabah' => $idNasabah,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
