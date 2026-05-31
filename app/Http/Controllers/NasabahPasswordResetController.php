<?php

namespace App\Http\Controllers;

use App\Mail\NasabahPasswordResetLinkMail;
use App\Models\Nasabah;
use App\Services\FirebasePasswordResetLinkGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NasabahPasswordResetController extends Controller
{
    public function __construct(
        private FirebasePasswordResetLinkGenerator $passwordResetLinks,
    ) {}

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

        if ($user && $this->isManualAccount($user) && ! $this->emailIsVerified($user)) {
            $request->session()->put(NasabahEmailVerificationController::SESSION_KEY, [
                'id_nasabah' => (int) $user['id_nasabah'],
                'email' => $email,
            ]);

            return redirect()
                ->route('nasabah.verification.notice')
                ->withErrors(['email' => 'Email belum diverifikasi. Verifikasi email dulu sebelum reset password.']);
        }

        if ($user && $this->isManualAccount($user) && $this->emailIsVerified($user)) {
            if ($this->ensureFirebasePasswordAccountExists($email, $user['password'] ?? null)) {
                if ($this->sendPasswordResetMail($email, $user, $request->ip())) {
                    $this->markAccountAsFirebasePending((int) $user['id_nasabah'], $user['password'] ?? null);
                }
            }
        }

        return back()->with('success', 'Jika email terdaftar sebagai akun manual, link reset password sudah dikirim.');
    }

    private function sendPasswordResetMail(string $email, array $user, ?string $userIp): bool
    {
        try {
            if ($this->passwordResetLinks->canGenerateCustomLink()) {
                try {
                    $resetUrl = $this->passwordResetLinks->generate($email, $userIp);
                    $recipientName = trim((string) ($user['nama_lengkap'] ?? ''));

                    Mail::to($email)->send(new NasabahPasswordResetLinkMail(
                        $recipientName !== '' ? $recipientName : 'Nasabah',
                        $resetUrl,
                    ));

                    return true;
                } catch (\Throwable $exception) {
                    Log::warning('GreenPoint custom password reset email failed, falling back to Firebase email.', [
                        'email' => $email,
                        'message' => $exception->getMessage(),
                    ]);
                }
            }

            $this->passwordResetLinks->sendPasswordResetEmail($email, $userIp);

            return true;
        } catch (\Throwable $exception) {
            Log::warning('GreenPoint password reset email failed.', [
                'email' => $email,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function ensureFirebasePasswordAccountExists(string $email, ?string $storedPassword): bool
    {
        $firebaseApiKey = config('services.firebase.api_key');

        if (! $firebaseApiKey) {
            Log::warning('Firebase password account creation skipped because FIREBASE_API_KEY is missing.');

            return $this->isFirebaseUidMarker($storedPassword);
        }

        try {
            $response = Http::acceptJson()->post(
                'https://identitytoolkit.googleapis.com/v1/accounts:signUp?key='.urlencode($firebaseApiKey),
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

            return $this->isFirebaseUidMarker($storedPassword);
        } catch (\Throwable $exception) {
            Log::warning('Firebase password account creation failed unexpectedly.', [
                'message' => $exception->getMessage(),
            ]);

            return $this->isFirebaseUidMarker($storedPassword);
        }

        return $this->isFirebaseUidMarker($storedPassword);
    }

    private function findNasabahByEmail(string $email): ?array
    {
        return Nasabah::whereEmailInsensitive($email)->first()?->getAttributes();
    }

    private function isManualAccount(array $user): bool
    {
        return empty($user['google_sub']);
    }

    private function emailIsVerified(array $user): bool
    {
        return array_key_exists('email_verified_at', $user)
            && ! empty($user['email_verified_at']);
    }

    private function isFirebaseUidMarker(?string $storedPassword): bool
    {
        return is_string($storedPassword) && str_starts_with($storedPassword, 'firebase-auth:');
    }

    private function markAccountAsFirebasePending(int $idNasabah, ?string $storedPassword): void
    {
        if ($storedPassword === 'firebase-auth-pending') {
            return;
        }

        try {
            Nasabah::where('id_nasabah', $idNasabah)->update([
                'password' => 'firebase-auth-pending',
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Failed to mark nasabah as Firebase pending unexpectedly.', [
                'id_nasabah' => $idNasabah,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
