<?php

namespace App\Http\Controllers;

use App\Mail\NasabahVerificationLinkMail;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use RuntimeException;

class NasabahEmailVerificationController extends Controller
{
    public const SESSION_KEY = 'nasabah.pending_email_verification';

    private const VERIFICATION_LINK_TTL_MINUTES = 60;

    private const RESEND_COOLDOWN_SECONDS = 60;

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

            $response = $this->supabaseRequest(true)
                ->withHeaders(['Prefer' => 'return=minimal'])
                ->patch($this->supabaseUrl().'/rest/v1/nasabah?id_nasabah=eq.'.$id, [
                    'email_verified_at' => now()->toIso8601String(),
                    'email_verification_token_hash' => null,
                    'email_verification_expires_at' => null,
                ]);

            if (! $response->successful()) {
                Log::warning('Gagal menandai email nasabah sebagai verified.', [
                    'id_nasabah' => $id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return redirect()
                    ->route('nasabah.login')
                    ->with('error', 'Email belum dapat diverifikasi. Coba link terbaru atau kirim ulang email verifikasi.');
            }

            $request->session()->forget(self::SESSION_KEY);

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

                $response = $this->supabaseRequest(true)
                    ->withHeaders(['Prefer' => 'return=representation'])
                    ->patch($this->supabaseUrl().'/rest/v1/nasabah?id_nasabah=eq.'.(int) $user['id_nasabah'], [
                        'email_verification_token_hash' => $this->tokenHash($token),
                        'email_verification_expires_at' => $expiresAt->toIso8601String(),
                        'email_verification_sent_at' => now()->toIso8601String(),
                    ]);

                if ($response->successful()) {
                    $updatedUser = $this->firstRow($response->json()) ?? $user;
                    $this->sendVerificationLink($updatedUser, $token, $expiresAt);
                } else {
                    Log::warning('Gagal memperbarui token verifikasi email nasabah.', [
                        'id_nasabah' => $user['id_nasabah'] ?? null,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
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
        return empty($user['google_sub'])
            && array_key_exists('email_verified_at', $user)
            && empty($user['email_verified_at']);
    }

    private function emailIsVerified(array $user): bool
    {
        return ! empty($user['google_sub'])
            || (array_key_exists('email_verified_at', $user) && ! empty($user['email_verified_at']));
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
        $response = $this->supabaseRequest()->get($this->supabaseUrl().'/rest/v1/nasabah', [
            'select' => '*',
            'id_nasabah' => 'eq.'.$id,
            'limit' => 1,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Gagal membaca akun nasabah dari Supabase.');
        }

        return $this->firstRow($response->json());
    }

    private function findNasabahByEmail(string $email): ?array
    {
        $response = $this->supabaseRequest()->get($this->supabaseUrl().'/rest/v1/nasabah', [
            'select' => '*',
            'email' => 'eq.'.$email,
            'limit' => 1,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Gagal membaca akun nasabah dari Supabase.');
        }

        return $this->firstRow($response->json());
    }

    private function supabaseRequest(bool $useServiceRole = false): PendingRequest
    {
        $key = $useServiceRole
            ? (env('SUPABASE_SERVICE_ROLE_KEY') ?: env('SUPABASE_KEY'))
            : env('SUPABASE_KEY');

        if (! $this->supabaseUrl() || ! $key) {
            throw new RuntimeException('Konfigurasi Supabase belum lengkap.');
        }

        return Http::acceptJson()->withHeaders([
            'apikey' => $key,
            'Authorization' => 'Bearer '.$key,
            'Content-Type' => 'application/json',
        ]);
    }

    private function supabaseUrl(): string
    {
        $url = rtrim((string) env('SUPABASE_URL'), '/');

        if ($url === '') {
            throw new RuntimeException('SUPABASE_URL belum diatur.');
        }

        return $url;
    }

    private function firstRow(mixed $rows): ?array
    {
        return is_array($rows) && isset($rows[0]) && is_array($rows[0]) ? $rows[0] : null;
    }
}
