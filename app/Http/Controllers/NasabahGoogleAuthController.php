<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class NasabahGoogleAuthController extends Controller
{
    public function authenticate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'credential' => ['required', 'string'],
        ]);

        try {
            $googleProfile = $this->verifyGoogleCredential($validated['credential']);
            $user = $this->findOrCreateNasabah($googleProfile);

            session([
                'id_nasabah' => $user['id_nasabah'],
                'nama_nasabah' => $user['nama_lengkap'] ?? ($user['nama_nasabah'] ?? null),
                'username' => $user['user_name'] ?? ($user['username'] ?? null),
                'email' => $user['email'] ?? null,
                'saldo' => $user['saldo'] ?? 0,
                'google_login' => true,
            ]);

            return response()->json([
                'message' => 'Login Google berhasil.',
                'redirect' => route('nasabah.dashboard'),
            ]);
        } catch (RuntimeException $exception) {
            Log::warning('Google SSO nasabah gagal.', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Throwable $exception) {
            Log::error('Google SSO nasabah mengalami error tak terduga.', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses login Google.',
            ], 500);
        }
    }

    private function verifyGoogleCredential(string $credential): array
    {
        $clientId = config('services.google.client_id');

        if (!$clientId) {
            throw new RuntimeException('Google SSO belum dikonfigurasi.');
        }

        $response = Http::acceptJson()->get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $credential,
        ]);

        if (!$response->successful()) {
            throw new RuntimeException('Token Google tidak valid atau sudah kedaluwarsa.');
        }

        $payload = $response->json();
        $issuer = $payload['iss'] ?? null;
        $audience = $payload['aud'] ?? null;
        $expiresAt = isset($payload['exp']) ? (int) $payload['exp'] : 0;
        $emailVerified = filter_var($payload['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($audience !== $clientId) {
            throw new RuntimeException('Token Google bukan untuk aplikasi ini.');
        }

        if (!in_array($issuer, ['accounts.google.com', 'https://accounts.google.com'], true)) {
            throw new RuntimeException('Penerbit token Google tidak valid.');
        }

        if ($expiresAt <= time()) {
            throw new RuntimeException('Token Google sudah kedaluwarsa.');
        }

        if (!$emailVerified || empty($payload['email']) || empty($payload['sub'])) {
            throw new RuntimeException('Akun Google belum terverifikasi dengan benar.');
        }

        return [
            'sub' => (string) $payload['sub'],
            'email' => (string) $payload['email'],
            'name' => (string) ($payload['name'] ?? Str::before($payload['email'], '@')),
        ];
    }

    private function findOrCreateNasabah(array $googleProfile): array
    {
        $user = $this->findNasabahByGoogleSub($googleProfile['sub']);

        if (!$user) {
            $user = $this->findNasabahByEmail($googleProfile['email']);
        }

        if ($user) {
            if (($user['status'] ?? 'aktif') !== 'aktif') {
                throw new RuntimeException('Akun nasabah belum aktif.');
            }

            if (!empty($user['google_sub']) && $user['google_sub'] !== $googleProfile['sub']) {
                throw new RuntimeException('Akun ini sudah tertaut ke akun Google lain.');
            }

            return $this->linkGoogleSubIfSupported($user, $googleProfile['sub']);
        }

        return $this->createNasabahFromGoogle($googleProfile);
    }

    private function findNasabahByGoogleSub(string $googleSub): ?array
    {
        $response = $this->supabaseRequest()->get($this->supabaseUrl() . '/rest/v1/nasabah', [
            'select' => '*',
            'google_sub' => 'eq.' . $googleSub,
            'limit' => 1,
        ]);

        if ($response->successful()) {
            return $this->firstRow($response->json());
        }

        if ($this->googleSubColumnIsMissing($response->body())) {
            return null;
        }

        throw new RuntimeException('Gagal membaca akun nasabah dari Supabase.');
    }

    private function findNasabahByEmail(string $email): ?array
    {
        $response = $this->supabaseRequest()->get($this->supabaseUrl() . '/rest/v1/nasabah', [
            'select' => '*',
            'email' => 'eq.' . $email,
            'limit' => 1,
        ]);

        if (!$response->successful()) {
            throw new RuntimeException('Gagal membaca akun nasabah dari Supabase.');
        }

        return $this->firstRow($response->json());
    }

    private function linkGoogleSubIfSupported(array $user, string $googleSub): array
    {
        if (($user['google_sub'] ?? null) === $googleSub) {
            return $user;
        }

        $response = $this->supabaseRequest(true)
            ->withHeaders(['Prefer' => 'return=representation'])
            ->patch($this->supabaseUrl() . '/rest/v1/nasabah?id_nasabah=eq.' . (int) $user['id_nasabah'], [
                'google_sub' => $googleSub,
            ]);

        if ($response->successful()) {
            return $this->firstRow($response->json()) ?? $user;
        }

        if ($this->googleSubColumnIsMissing($response->body())) {
            return $user;
        }

        Log::warning('Gagal menautkan google_sub ke akun nasabah.', [
            'id_nasabah' => $user['id_nasabah'] ?? null,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return $user;
    }

    private function createNasabahFromGoogle(array $googleProfile): array
    {
        $newUser = [
            'nama_lengkap' => $googleProfile['name'],
            'user_name' => $this->generateUniqueUsername($googleProfile['email']),
            'email' => $googleProfile['email'],
            'password' => password_hash(Str::random(40), PASSWORD_BCRYPT),
            'alamat' => '',
            'no_hp' => '',
            'created_at' => now()->toDateTimeString(),
            'status' => 'aktif',
            'saldo' => 0,
            'google_sub' => $googleProfile['sub'],
        ];

        $response = $this->insertNasabah($newUser);

        if (!$response->successful() && $this->googleSubColumnIsMissing($response->body())) {
            unset($newUser['google_sub']);
            $response = $this->insertNasabah($newUser);
        }

        if (!$response->successful()) {
            Log::warning('Gagal membuat akun nasabah dari Google.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Login Google berhasil, tetapi akun nasabah baru gagal dibuat.');
        }

        $user = $this->firstRow($response->json());

        if (!$user) {
            throw new RuntimeException('Akun nasabah baru tidak berhasil dibaca setelah dibuat.');
        }

        return $user;
    }

    private function insertNasabah(array $payload)
    {
        return $this->supabaseRequest(true)
            ->withHeaders(['Prefer' => 'return=representation'])
            ->post($this->supabaseUrl() . '/rest/v1/nasabah', $payload);
    }

    private function generateUniqueUsername(string $email): string
    {
        $base = Str::of(Str::before($email, '@'))
            ->lower()
            ->replaceMatches('/[^a-z0-9_]/', '')
            ->substr(0, 40)
            ->toString();

        $base = $base !== '' ? $base : 'nasabah';
        $candidate = $base;
        $suffix = 1;

        while ($this->usernameExists($candidate)) {
            $suffixText = (string) $suffix;
            $candidate = substr($base, 0, 50 - strlen($suffixText)) . $suffixText;
            $suffix++;
        }

        return $candidate;
    }

    private function usernameExists(string $username): bool
    {
        $response = $this->supabaseRequest()->get($this->supabaseUrl() . '/rest/v1/nasabah', [
            'select' => 'id_nasabah',
            'user_name' => 'eq.' . $username,
            'limit' => 1,
        ]);

        if (!$response->successful()) {
            throw new RuntimeException('Gagal memeriksa username nasabah.');
        }

        return $this->firstRow($response->json()) !== null;
    }

    private function supabaseRequest(bool $useServiceRole = false): PendingRequest
    {
        $key = $useServiceRole
            ? (env('SUPABASE_SERVICE_ROLE_KEY') ?: env('SUPABASE_KEY'))
            : env('SUPABASE_KEY');

        if (!$this->supabaseUrl() || !$key) {
            throw new RuntimeException('Konfigurasi Supabase belum lengkap.');
        }

        return Http::acceptJson()->withHeaders([
            'apikey' => $key,
            'Authorization' => 'Bearer ' . $key,
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

    private function googleSubColumnIsMissing(string $body): bool
    {
        return str_contains(strtolower($body), 'google_sub');
    }
}
