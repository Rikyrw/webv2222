<?php

namespace App\Http\Controllers;

use App\Mail\NasabahVerificationLinkMail;
use App\Models\Nasabah;
use App\Services\FirebaseAuthUserManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class NasabahEmailVerificationController extends Controller
{
    public const SESSION_KEY = 'nasabah.pending_email_verification';

    private const VERIFICATION_LINK_TTL_MINUTES = 60;

    private const RESEND_COOLDOWN_SECONDS = 60;

    public function __construct(
        private FirebaseAuthUserManager $firebaseUsers,
    ) {}

    public function notice(Request $request): View
    {
        return view('nasabah.email-not-verified', [
            'email' => data_get($request->session()->get(self::SESSION_KEY), 'email'),
        ]);
    }

    public function verify(Request $request, int $id, string $token): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            return redirect()
                ->route('nasabah.login')
                ->with('error', 'Link verifikasi tidak valid atau sudah kedaluwarsa.');
        }

        try {
            $user = $this->findNasabahById($id);

            if (! $user) {
                return redirect()
                    ->route('nasabah.login')
                    ->with('error', 'Link verifikasi tidak ditemukan.');
            }

            if ($this->emailIsVerified($user)) {
                $request->session()->forget(self::SESSION_KEY);

                return redirect()
                    ->route('nasabah.login')
                    ->with('error', 'Link verifikasi sudah pernah dipakai. Silakan login.');
            }

            if (! $this->verificationTokenIsUsable($user, $token)) {
                return redirect()
                    ->route('nasabah.login')
                    ->with('error', 'Link verifikasi tidak valid atau sudah kedaluwarsa.');
            }

            Nasabah::where('id_nasabah', $id)->update([
                'email_verified_at' => now(),
                'email_verification_token_hash' => null,
                'email_verification_expires_at' => null,
            ]);

            $request->session()->forget(self::SESSION_KEY);
            $this->syncFirebaseEmailIfNeeded($user);

            return redirect()
                ->route('nasabah.login')
                ->with('success', 'Email berhasil diverifikasi. Silakan login.');
        } catch (\Throwable $exception) {
            Log::warning('Verifikasi email nasabah gagal.', [
                'id_nasabah' => $id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('nasabah.login')
                ->with('error', 'Email belum dapat diverifikasi. Coba lagi beberapa saat.');
        }
    }

    public function resend(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['nullable', 'email'],
        ]);

        $email = strtolower(trim((string) ($validated['email'] ?? data_get($request->session()->get(self::SESSION_KEY), 'email', ''))));

        if ($email === '') {
            return back()->withErrors(['email' => 'Login dulu dengan akun yang belum verified untuk mengirim ulang email.']);
        }

        try {
            $user = $this->findNasabahByEmail($email);

            if ($user && $this->emailNeedsVerification($user)) {
                $waitSeconds = $this->resendWaitSeconds($user);

                if ($waitSeconds > 0) {
                    return back()->withErrors(['email' => 'Tunggu '.$waitSeconds.' detik sebelum mengirim ulang email verifikasi.']);
                }

                $token = $this->generateVerificationToken();
                $expiresAt = now()->addMinutes(self::VERIFICATION_LINK_TTL_MINUTES);

                Nasabah::where('id_nasabah', (int) $user['id_nasabah'])->update([
                    'email_verification_token_hash' => $this->tokenHash($token),
                    'email_verification_expires_at' => $expiresAt,
                    'email_verification_sent_at' => now(),
                ]);

                $this->sendVerificationLink($user, $token, $expiresAt);
            }
        } catch (\Throwable $exception) {
            Log::warning('Gagal mengirim ulang email verifikasi nasabah.', [
                'email' => $email,
                'message' => $exception->getMessage(),
            ]);
        }

        return back()->with('success', 'Jika akun masih belum verified, email verifikasi baru sudah dikirim.');
    }

    private function sendVerificationLink(array $user, string $token, $expiresAt): void
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

    private function emailNeedsVerification(array $user): bool
    {
        return array_key_exists('email_verified_at', $user)
            && empty($user['email_verified_at']);
    }

    private function emailIsVerified(array $user): bool
    {
        return array_key_exists('email_verified_at', $user) && ! empty($user['email_verified_at']);
    }

    private function syncFirebaseEmailIfNeeded(array $user): bool
    {
        $firebaseUid = $this->firebaseUid($user['password'] ?? null);
        if ($firebaseUid === null) {
            return true;
        }

        $email = strtolower(trim((string) ($user['email'] ?? '')));
        if ($email === '') {
            return false;
        }

        return $this->firebaseUsers->updateEmailByUid($firebaseUid, $email, true);
    }

    private function firebaseUid(mixed $storedPassword): ?string
    {
        if (! is_string($storedPassword) || ! str_starts_with($storedPassword, 'firebase-auth:')) {
            return null;
        }

        $uid = substr($storedPassword, strlen('firebase-auth:'));

        return $uid !== '' ? $uid : null;
    }

    private function verificationTokenIsUsable(array $user, string $token): bool
    {
        $storedHash = (string) ($user['email_verification_token_hash'] ?? '');
        $expiresAt = $user['email_verification_expires_at'] ?? null;

        if ($storedHash === '' || ! is_string($expiresAt) || $expiresAt === '') {
            return false;
        }

        try {
            if (Carbon::parse($expiresAt)->isPast()) {
                return false;
            }
        } catch (\Throwable) {
            return false;
        }

        return hash_equals($storedHash, $this->tokenHash($token));
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

    private function generateVerificationToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function tokenHash(string $token): string
    {
        return hash('sha256', $token);
    }

    private function findNasabahById(int $id): ?array
    {
        return Nasabah::where('id_nasabah', $id)->first()?->getAttributes();
    }

    private function findNasabahByEmail(string $email): ?array
    {
        return Nasabah::whereEmailInsensitive($email)->first()?->getAttributes();
    }
}
