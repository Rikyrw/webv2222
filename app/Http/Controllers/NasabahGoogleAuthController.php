<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class NasabahGoogleAuthController extends Controller
{
    private const DEACTIVATED_LOGIN_MESSAGE = 'Akun Anda sedang nonaktif. Silakan hubungi CS GreenPoint untuk bantuan lebih lanjut.';

    private const INACTIVE_LOGIN_MESSAGE = 'Akun Anda belum aktif. Silakan hubungi CS GreenPoint untuk bantuan lebih lanjut.';

    public function authenticate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'credential' => ['required', 'string'],
        ]);

        try {
            $googleProfile = $this->verifyGoogleCredential($validated['credential']);
            $user = $this->findOrCreateNasabah($googleProfile);

            session()->forget(NasabahEmailVerificationController::SESSION_KEY);
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

        if (! $clientId) {
            throw new RuntimeException('Google SSO belum dikonfigurasi.');
        }

        $response = Http::acceptJson()->get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $credential,
        ]);

        if (! $response->successful()) {
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

        if (! in_array($issuer, ['accounts.google.com', 'https://accounts.google.com'], true)) {
            throw new RuntimeException('Penerbit token Google tidak valid.');
        }

        if ($expiresAt <= time()) {
            throw new RuntimeException('Token Google sudah kedaluwarsa.');
        }

        if (! $emailVerified || empty($payload['email']) || empty($payload['sub'])) {
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

        if (! $user) {
            $user = $this->findNasabahByEmail($googleProfile['email']);
        }

        if ($user) {
            if ($statusMessage = $this->accountStatusMessage($user)) {
                throw new RuntimeException($statusMessage);
            }

            if (! empty($user['google_sub']) && $user['google_sub'] !== $googleProfile['sub']) {
                throw new RuntimeException('Akun ini sudah tertaut ke akun Google lain.');
            }

            return $this->linkGoogleSubIfSupported($user, $googleProfile['sub']);
        }

        return $this->createNasabahFromGoogle($googleProfile);
    }

    private function findNasabahByGoogleSub(string $googleSub): ?array
    {
        return Nasabah::where('google_sub', $googleSub)->first()?->getAttributes();
    }

    private function findNasabahByEmail(string $email): ?array
    {
        return Nasabah::whereEmailInsensitive($email)->first()?->getAttributes();
    }

    private function linkGoogleSubIfSupported(array $user, string $googleSub): array
    {
        $payload = $this->verifiedGooglePayload();

        if (($user['google_sub'] ?? null) !== $googleSub) {
            $payload['google_sub'] = $googleSub;
        }

        Nasabah::where('id_nasabah', (int) $user['id_nasabah'])->update($payload);

        return array_merge($user, $payload);
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
            ...$this->verifiedGooglePayload(),
        ];

        try {
            return Nasabah::create($newUser)->getAttributes();
        } catch (\Throwable $exception) {
            Log::warning('Gagal membuat akun nasabah dari Google.', [
                'message' => $exception->getMessage(),
            ]);
            throw new RuntimeException('Login Google berhasil, tetapi akun nasabah baru gagal dibuat.');
        }
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
            $candidate = substr($base, 0, 50 - strlen($suffixText)).$suffixText;
            $suffix++;
        }

        return $candidate;
    }

    private function usernameExists(string $username): bool
    {
        return Nasabah::whereUsernameInsensitive($username)->exists();
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

    private function verifiedGooglePayload(): array
    {
        return [
            'email_verified_at' => now(),
            'email_verification_token_hash' => null,
            'email_verification_expires_at' => null,
        ];
    }
}
