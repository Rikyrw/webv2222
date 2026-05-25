<?php

namespace App\Http\Controllers;

use App\Mail\NasabahVerificationLinkMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class NasabahRegisterController extends Controller
{
    private const VERIFICATION_LINK_TTL_MINUTES = 60;

    public function showRegister(): View
    {
        return view('nasabah.register');
    }

    public function store(Request $request): RedirectResponse
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

        if ($validated['password'] !== $validated['konfirmasi_password']) {
            return back()->withInput()->withErrors(['password' => 'Password dan konfirmasi password tidak sama!']);
        }

        if (strlen($validated['password']) > 8) {
            return back()->withInput()->withErrors(['password' => 'Password maksimal 8 karakter!']);
        }

        try {
            $email = strtolower(trim($validated['email']));
            $username = trim($validated['username']);

            if ($this->nasabahExists($email, $username)) {
                return back()->withInput()->withErrors(['email' => 'Email atau username sudah terdaftar!']);
            }

            $token = $this->generateVerificationToken();
            $expiresAt = now()->addMinutes(self::VERIFICATION_LINK_TTL_MINUTES);
            $newUser = [
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
            ];

            $insertResponse = $this->insertNasabah($newUser);
            $result = $insertResponse->json();
            $user = $this->firstRow($result);

            Log::info('Supabase insert nasabah pending email verification status: '.$insertResponse->status().' body: '.$insertResponse->body());

            if (! $insertResponse->successful() || ! $user) {
                return back()->withInput()->withErrors([
                    'email' => $this->registrationInsertError($insertResponse->status(), $insertResponse->body(), $result),
                ]);
            }

            $request->session()->put(NasabahEmailVerificationController::SESSION_KEY, [
                'id_nasabah' => $user['id_nasabah'],
                'email' => $email,
            ]);

            try {
                $this->sendVerificationLink($user, $token, $expiresAt);
            } catch (\Throwable $exception) {
                Log::warning('Gagal mengirim link verifikasi pendaftaran nasabah.', [
                    'id_nasabah' => $user['id_nasabah'] ?? null,
                    'message' => $exception->getMessage(),
                ]);

                return redirect()
                    ->route('nasabah.verification.notice')
                    ->withErrors(['email' => 'Akun dibuat, tetapi email verifikasi belum dapat dikirim. Tekan kirim ulang untuk mencoba lagi.']);
            }

            return redirect()
                ->route('nasabah.verification.notice')
                ->with('success', 'Akun dibuat. Link verifikasi sudah dikirim ke email Anda.');
        } catch (\Throwable $exception) {
            Log::warning('Gagal mendaftarkan nasabah manual.', [
                'message' => $exception->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'email' => 'Pendaftaran belum dapat diproses. Coba lagi beberapa saat.',
            ]);
        }
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

    private function generateVerificationToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function tokenHash(string $token): string
    {
        return hash('sha256', $token);
    }

    private function nasabahExists(string $email, string $username): bool
    {
        $query = 'nasabah?select=id_nasabah&or=(email.eq.'.urlencode($email).',user_name.eq.'.urlencode($username).')';
        $response = $this->supabaseRequest()->get($this->supabaseUrl().'/rest/v1/'.$query);

        if (! $response->successful()) {
            throw new \RuntimeException('Gagal memeriksa email atau username nasabah.');
        }

        $existing = $response->json();

        return is_array($existing) && count($existing) > 0;
    }

    private function insertNasabah(array $newUser)
    {
        return $this->supabaseRequest(true)
            ->withHeaders(['Prefer' => 'return=representation'])
            ->post($this->supabaseUrl().'/rest/v1/nasabah', $newUser);
    }

    private function supabaseRequest(bool $useServiceRole = false)
    {
        $supabaseKey = env('SUPABASE_KEY');
        $key = $useServiceRole
            ? (env('SUPABASE_SERVICE_ROLE_KEY') ?: $supabaseKey)
            : $supabaseKey;

        if (! $this->supabaseUrl() || ! $key) {
            throw new \RuntimeException('Konfigurasi Supabase belum lengkap.');
        }

        if ($useServiceRole && $key === $supabaseKey) {
            Log::warning('Using SUPABASE_KEY for insert; if RLS is enabled this may be blocked. Consider setting SUPABASE_SERVICE_ROLE_KEY to the service_role key.');
        }

        return Http::acceptJson()->withHeaders([
            'apikey' => $key,
            'Authorization' => 'Bearer '.$key,
            'Content-Type' => 'application/json',
        ]);
    }

    private function supabaseUrl(): string
    {
        return rtrim((string) env('SUPABASE_URL'), '/');
    }

    private function firstRow(mixed $rows): ?array
    {
        return is_array($rows) && isset($rows[0]) && is_array($rows[0]) ? $rows[0] : null;
    }

    private function registrationInsertError(int $status, string $body, mixed $result): string
    {
        if (is_array($result)) {
            if (isset($result['message'])) {
                return 'Gagal mendaftar: '.$result['message'];
            }

            if (isset($result['hint'])) {
                return 'Gagal mendaftar: '.$result['hint'];
            }

            if ($result !== []) {
                return 'Gagal mendaftar: '.json_encode($result);
            }
        }

        return 'Gagal mendaftar (HTTP '.$status.'). '.substr($body, 0, 300);
    }
}
