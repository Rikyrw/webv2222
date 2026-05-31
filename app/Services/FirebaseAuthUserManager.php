<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FirebaseAuthUserManager
{
    private const OAUTH_SCOPE = 'https://www.googleapis.com/auth/identitytoolkit';

    private const OAUTH_TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private const UPDATE_ACCOUNT_URL = 'https://identitytoolkit.googleapis.com/v1/accounts:update';

    public function updateEmailByUid(string $firebaseUid, string $email, bool $emailVerified = true): bool
    {
        if (trim($firebaseUid) === '' || trim($email) === '') {
            return false;
        }

        try {
            $serviceAccount = $this->serviceAccount();
            $payload = [
                'localId' => $firebaseUid,
                'email' => $email,
                'emailVerified' => $emailVerified,
            ];

            if (! empty($serviceAccount['project_id'])) {
                $payload['targetProjectId'] = $serviceAccount['project_id'];
            }

            $response = Http::acceptJson()
                ->withToken($this->accessToken($serviceAccount))
                ->post(self::UPDATE_ACCOUNT_URL, $payload);

            if ($response->successful()) {
                return true;
            }

            Log::warning('Firebase user email update failed.', [
                'firebase_uid' => $firebaseUid,
                'email' => $email,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Firebase user email update failed unexpectedly.', [
                'firebase_uid' => $firebaseUid,
                'email' => $email,
                'message' => $exception->getMessage(),
            ]);
        }

        return false;
    }

    private function accessToken(array $serviceAccount): string
    {
        $now = time();
        $jwt = $this->encodeJson([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]).'.'.$this->encodeJson([
            'iss' => $serviceAccount['client_email'],
            'scope' => self::OAUTH_SCOPE,
            'aud' => self::OAUTH_TOKEN_URL,
            'iat' => $now,
            'exp' => $now + 3600,
        ]);

        $signature = '';

        if (! openssl_sign($jwt, $signature, $serviceAccount['private_key'], OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Private key Firebase tidak dapat menandatangani OAuth assertion.');
        }

        $response = Http::asForm()->acceptJson()->post(self::OAUTH_TOKEN_URL, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt.'.'.$this->base64UrlEncode($signature),
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('OAuth Firebase gagal dibuat (HTTP '.$response->status().').');
        }

        $token = data_get($response->json(), 'access_token');

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('OAuth Firebase tidak mengembalikan access token.');
        }

        return $token;
    }

    private function serviceAccount(): array
    {
        $path = trim((string) config('services.firebase.service_account_path'));

        if ($path === '' || ! is_readable($path)) {
            throw new RuntimeException('FIREBASE_SERVICE_ACCOUNT_PATH belum menunjuk file service account yang bisa dibaca.');
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        if (! is_array($decoded) || empty($decoded['client_email']) || empty($decoded['private_key'])) {
            throw new RuntimeException('File service account Firebase tidak memuat client_email dan private_key.');
        }

        return $decoded;
    }

    private function encodeJson(array $payload): string
    {
        return $this->base64UrlEncode((string) json_encode($payload, JSON_UNESCAPED_SLASHES));
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
