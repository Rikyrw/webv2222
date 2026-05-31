<?php

namespace App\Http\Controllers;

use App\Mail\NasabahPasswordResetLinkMail;
use App\Mail\NasabahVerificationLinkMail;
use App\Models\Nasabah;
use App\Services\FirebasePasswordResetLinkGenerator;
use App\Services\MobileNasabahTokenService;
use App\Support\PasswordPolicy;
use App\Support\UsernamePolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class MobileNasabahAuthController extends Controller
{
    private const DEACTIVATED_LOGIN_MESSAGE = 'Akun Anda sedang nonaktif. Silakan hubungi CS GreenPoint untuk bantuan lebih lanjut.';

    private const INACTIVE_LOGIN_MESSAGE = 'Akun Anda belum aktif. Silakan hubungi CS GreenPoint untuk bantuan lebih lanjut.';

    private const VERIFICATION_LINK_TTL_MINUTES = 60;

    private const RESEND_COOLDOWN_SECONDS = 60;

    public function __construct(
        private FirebasePasswordResetLinkGenerator $passwordResetLinks,
        private MobileNasabahTokenService $tokens,
    ) {}

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => UsernamePolicy::rules(),
            'email' => ['required', 'email', 'max:255'],
            'password' => PasswordPolicy::rules(),
            'konfirmasi_password' => PasswordPolicy::confirmationRules(),
            'alamat' => ['nullable', 'string'],
            'no_hp' => ['required', 'string', 'max:20'],
        ], [
            ...PasswordPolicy::messages(),
            ...UsernamePolicy::messages('username'),
        ]);

        try {
            $email = strtolower(trim($validated['email']));
            $username = trim($validated['username']);

            if ($this->nasabahExists($email, $username)) {
                return response()->json([
                    'message' => 'Email atau username sudah terdaftar.',
                    'errors' => [
                        'email' => ['Email atau username sudah terdaftar.'],
                    ],
                ], 422);
            }

            $token = $this->generateVerificationToken();
            $expiresAt = now()->addMinutes(self::VERIFICATION_LINK_TTL_MINUTES);
            $user = Nasabah::create([
                    'nama_lengkap' => $validated['nama'],
                    'user_name' => $username,
                    'email' => $email,
                    'password' => password_hash($validated['password'], PASSWORD_BCRYPT),
                    'alamat' => $validated['alamat'] ?? '',
                    'no_hp' => $validated['no_hp'],
                    'created_at' => now()->toDateTimeString(),
                    'status' => 'aktif',
                    'saldo' => 0,
                    'email_verified_at' => null,
                    'email_verification_token_hash' => $this->tokenHash($token),
                    'email_verification_expires_at' => $expiresAt->toIso8601String(),
                    'email_verification_sent_at' => now()->toIso8601String(),
                ])->getAttributes();

            $this->sendVerificationLink($user, $token, $expiresAt);

            return response()->json([
                'message' => 'Akun dibuat. Link verifikasi sudah dikirim ke email Anda.',
                'email' => $email,
            ], 201);
        } catch (\Throwable $exception) {
            Log::warning('Mobile nasabah registration failed.', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Pendaftaran belum dapat diproses. Coba lagi beberapa saat.',
            ], 500);
        }
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($validated['email']));

        try {
            $user = $this->findNasabahByEmail($email);

            if ($user && $this->emailNeedsVerification($user)) {
                $waitSeconds = $this->resendWaitSeconds($user);

                if ($waitSeconds > 0) {
                    return response()->json([
                        'message' => 'Tunggu '.$waitSeconds.' detik sebelum mengirim ulang email verifikasi.',
                    ], 429);
                }

                $token = $this->generateVerificationToken();
                $expiresAt = now()->addMinutes(self::VERIFICATION_LINK_TTL_MINUTES);

                Nasabah::where('id_nasabah', (int) $user['id_nasabah'])->update([
                        'email_verification_token_hash' => $this->tokenHash($token),
                        'email_verification_expires_at' => $expiresAt->toIso8601String(),
                        'email_verification_sent_at' => now()->toIso8601String(),
                    ]);

                $this->sendVerificationLink($user, $token, $expiresAt);
            }
        } catch (\Throwable $exception) {
            Log::warning('Mobile verification resend failed.', [
                'email' => $email,
                'message' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'message' => 'Jika akun masih belum verified, email verifikasi baru sudah dikirim.',
        ]);
    }

    public function sendPasswordReset(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
        ]);

        $identifier = trim($validated['identifier']);

        try {
            $user = $this->findNasabahByIdentifier($identifier);

            if (! $user) {
                return response()->json([
                    'message' => 'Email atau username tidak ditemukan. Periksa kembali atau daftar akun baru.',
                ], 404);
            }

            if (! $this->isManualAccount($user)) {
                return response()->json([
                    'message' => 'Akun ini terdaftar lewat Google. Silakan masuk menggunakan tombol Masuk dengan Google.',
                ], 422);
            }

            $email = strtolower((string) $user['email']);

            if ($email === '') {
                return response()->json([
                    'message' => 'Email akun belum lengkap. Silakan hubungi CS GreenPoint.',
                ], 422);
            }

            if ($this->emailNeedsVerification($user)) {
                return response()->json([
                    'message' => 'Email akun belum diverifikasi. Verifikasi email dulu sebelum reset password.',
                    'email' => $email,
                ], 403);
            }

            if (! $this->ensureFirebasePasswordAccountExists($email, $user['password'] ?? null)) {
                return response()->json([
                    'message' => 'Reset password belum bisa diproses. Konfigurasi Firebase perlu dicek.',
                ], 503);
            }

            if (! $this->sendPasswordResetLink($email, $user, $request->ip())) {
                return response()->json([
                    'message' => 'Gagal mengirim email reset password. Coba lagi nanti atau hubungi CS GreenPoint.',
                ], 503);
            }

            $this->markAccountAsFirebasePending((int) $user['id_nasabah'], $user['password'] ?? null);
        } catch (\Throwable $exception) {
            Log::warning('Mobile password reset email failed.', [
                'identifier' => $identifier,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal memproses reset password saat ini. Coba lagi nanti.',
            ], 500);
        }

        return response()->json([
            'message' => 'Link reset password sudah dikirim. Cek inbox atau folder spam email Anda.',
        ]);
    }

    public function verifyLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        try {
            $user = $this->findNasabahByIdentifier($validated['identifier']);

            if (! $user || ! $this->isManualAccount($user)) {
                return response()->json([
                    'message' => 'Email/username atau password salah.',
                ], 401);
            }

            if ($statusMessage = $this->accountStatusMessage($user)) {
                return response()->json([
                    'message' => $statusMessage,
                    'account_status' => $user['status'] ?? null,
                ], 403);
            }

            if ($this->emailNeedsVerification($user)) {
                return response()->json([
                    'message' => 'Email belum diverifikasi.',
                    'email' => strtolower((string) ($user['email'] ?? '')),
                ], 403);
            }

            if (! $this->passwordMatches($validated['password'], $user['password'] ?? null)) {
                return response()->json([
                    'message' => 'Email/username atau password salah.',
                ], 401);
            }

            $nasabah = Nasabah::find((int) $user['id_nasabah']);
            if (! $nasabah) {
                return response()->json([
                    'message' => 'Data nasabah tidak ditemukan.',
                ], 404);
            }

            $passwordWarning = PasswordPolicy::warningFor($validated['password']);

            return response()->json([
                'message' => 'Login manual terverifikasi.',
                'requires_password_change' => $passwordWarning !== null,
                'password_warning' => $passwordWarning,
                'user' => [
                    'id_nasabah' => $user['id_nasabah'] ?? null,
                    'email' => strtolower((string) ($user['email'] ?? '')),
                    'nama_lengkap' => $user['nama_lengkap'] ?? null,
                    'user_name' => $user['user_name'] ?? null,
                    'alamat' => $user['alamat'] ?? null,
                    'no_hp' => $user['no_hp'] ?? null,
                    'photo_url' => $user['photo_url'] ?? null,
                    'google_id' => $user['google_id'] ?? null,
                    'saldo' => $user['saldo'] ?? 0,
                ],
                ...$this->tokens->payload($nasabah, $request->input('device_name')),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Mobile manual login verification failed.', [
                'identifier' => $validated['identifier'],
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Login belum dapat diproses. Coba lagi beberapa saat.',
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    private function sendVerificationLink(array $user, string $token, Carbon $expiresAt): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'nasabah.verification.verify',
            $expiresAt,
            [
                'id' => (int) $user['id_nasabah'],
                'token' => $token,
            ],
        );

        Mail::to($user['email'])->send(new NasabahVerificationLinkMail(
            $user['nama_lengkap'] ?? 'Nasabah',
            $verificationUrl,
            self::VERIFICATION_LINK_TTL_MINUTES,
        ));
    }

    private function sendPasswordResetLink(string $email, array $user, ?string $userIp): bool
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
                    Log::warning('Mobile custom password reset email failed, falling back to Firebase email.', [
                        'email' => $email,
                        'message' => $exception->getMessage(),
                    ]);
                }
            }

            $this->passwordResetLinks->sendPasswordResetEmail($email, $userIp);

            return true;
        } catch (\Throwable $exception) {
            Log::warning('Mobile password reset email delivery failed.', [
                'email' => $email,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function nasabahExists(string $email, string $username): bool
    {
        return $this->findNasabahByEmail($email) !== null
            || $this->findNasabahByUsername($username) !== null;
    }

    private function findNasabahByIdentifier(string $identifier): ?array
    {
        $identifier = trim($identifier);

        if (str_contains($identifier, '@')) {
            return $this->findNasabahByEmail($identifier);
        }

        return $this->findNasabahByUsername($identifier);
    }

    private function findNasabahByEmail(string $email): ?array
    {
        return Nasabah::whereEmailInsensitive($email)->first()?->getAttributes();
    }

    private function findNasabahByUsername(string $username): ?array
    {
        return Nasabah::whereUsernameInsensitive($username)->first()?->getAttributes();
    }

    private function emailNeedsVerification(array $user): bool
    {
        return array_key_exists('email_verified_at', $user)
            && empty($user['email_verified_at']);
    }

    private function resendWaitSeconds(array $user): int
    {
        $sentAt = $user['email_verification_sent_at'] ?? null;

        if (! is_string($sentAt) || $sentAt === '') {
            return 0;
        }

        try {
            $nextAllowedAt = Carbon::parse($sentAt)->addSeconds(self::RESEND_COOLDOWN_SECONDS);

            return $nextAllowedAt->isFuture() ? (int) now()->diffInSeconds($nextAllowedAt) : 0;
        } catch (\Throwable) {
            return 0;
        }
    }

    private function ensureFirebasePasswordAccountExists(string $email, ?string $storedPassword): bool
    {
        $firebaseApiKey = config('services.firebase.api_key');

        if (! $firebaseApiKey) {
            Log::warning('Mobile Firebase password account creation skipped because FIREBASE_API_KEY is missing.');

            return $this->isFirebaseUidMarker($storedPassword);
        }

        try {
            $response = Http::acceptJson()->post(
                'https://identitytoolkit.googleapis.com/v1/accounts:signUp?key='.urlencode($firebaseApiKey),
                [
                    'email' => $email,
                    'password' => Str::password(32),
                    'returnSecureToken' => true,
                ],
            );

            if ($response->successful()) {
                return true;
            }

            if (str_contains(strtoupper((string) data_get($response->json(), 'error.message', '')), 'EMAIL_EXISTS')) {
                return true;
            }

            Log::warning('Mobile Firebase password account creation failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Mobile Firebase password account creation failed unexpectedly.', [
                'message' => $exception->getMessage(),
            ]);

            return $this->isFirebaseUidMarker($storedPassword);
        }

        return $this->isFirebaseUidMarker($storedPassword);
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
            Log::warning('Mobile failed to mark nasabah as Firebase pending unexpectedly.', [
                'id_nasabah' => $idNasabah,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function isManualAccount(array $user): bool
    {
        return empty($user['google_sub']);
    }

    private function accountStatusMessage(array $user): ?string
    {
        $status = strtolower(trim((string) ($user['status'] ?? 'aktif')));

        if ($status === '' || $status === 'aktif') {
            return null;
        }

        if (in_array($status, ['nonaktif', 'ditolak', 'inactive', 'disabled', 'banned'], true)) {
            return self::DEACTIVATED_LOGIN_MESSAGE;
        }

        return self::INACTIVE_LOGIN_MESSAGE;
    }

    private function isFirebaseBackedPassword(?string $storedPassword): bool
    {
        return is_string($storedPassword)
            && (str_starts_with($storedPassword, 'firebase-auth:') || $storedPassword === 'firebase-auth-pending');
    }

    private function isFirebaseUidMarker(?string $storedPassword): bool
    {
        return is_string($storedPassword) && str_starts_with($storedPassword, 'firebase-auth:');
    }

    private function passwordMatches(string $password, mixed $storedPassword): bool
    {
        if (! is_string($storedPassword) || $storedPassword === '' || $this->isFirebaseBackedPassword($storedPassword)) {
            return false;
        }

        if (password_verify($password, $storedPassword)) {
            return true;
        }

        return hash_equals($storedPassword, $password);
    }

    private function generateVerificationToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function tokenHash(string $token): string
    {
        return hash('sha256', $token);
    }

}
