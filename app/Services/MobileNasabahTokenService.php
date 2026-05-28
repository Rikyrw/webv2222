<?php

namespace App\Services;

use App\Models\Nasabah;

class MobileNasabahTokenService
{
    public function payload(Nasabah $nasabah, ?string $deviceName = null): array
    {
        $tokenName = $this->tokenName($deviceName);
        $expiresAt = now()->addDays((int) config('sanctum.mobile_expiration_days', 30));
        $token = $nasabah->createToken($tokenName, ['nasabah:mobile'], $expiresAt);

        return [
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toIso8601String(),
        ];
    }

    private function tokenName(?string $deviceName): string
    {
        $name = trim((string) $deviceName);

        return $name === '' ? 'greenpoint-mobile' : mb_substr($name, 0, 120);
    }
}
