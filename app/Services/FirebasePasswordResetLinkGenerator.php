<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class FirebasePasswordResetLinkGenerator
{
    private const OAUTH_SCOPE = 'https://www.googleapis.com/auth/identitytoolkit';

    private const OAUTH_TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private const SEND_OOB_CODE_URL = 'https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode';

    public function generate(string $email, ?string $userIp = null): string
    {
        $payload = [
            'requestType' => 'PASSWORD_RESET',
            'email' => $email,
            'returnOobLink' => true,
        ];

        if (is_string($userIp) && $userIp !== '') {
            $payload['userIp'] = $userIp;
        }

        $response = Http::acceptJson()
            ->withToken($this->accessToken())
            ->post(self::SEND_OOB_CODE_URL, $payload);

        if (! $response->successful()) {
            throw new RuntimeException('Firebase gagal membuat link reset password (HTTP '.$response->status().').');
        }

        $resetUrl = data_get($response->json(), 'oobLink');

        if (! is_string($resetUrl) || $resetUrl === '') {
            throw new RuntimeException('Firebase tidak mengembalikan link reset password.');
        }

        return $resetUrl;
    }

    private function accessToken(): string
    {
        $serviceAccount = $this->serviceAccount();
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
