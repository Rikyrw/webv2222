<?php

namespace Tests\Feature;

use App\Mail\NasabahPasswordResetLinkMail;
use App\Mail\NasabahVerificationLinkMail;
use App\Services\FirebasePasswordResetLinkGenerator;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MobileNasabahAuthTest extends TestCase
{
    public function test_mobile_registration_creates_unverified_account_and_sends_verification_email(): void
    {
        Mail::fake();

        Http::fakeSequence()
            ->push([], 200)
            ->push([], 200)
            ->push([[
                'id_nasabah' => 41,
                'email' => 'mobile@example.test',
                'nama_lengkap' => 'Nasabah Mobile',
                'email_verified_at' => null,
            ]], 201);

        $this->postJson('/api/mobile/nasabah/register', [
            'nama' => 'Nasabah Mobile',
            'username' => 'nasabahmobile',
            'email' => 'mobile@example.test',
            'password' => 'rahasia123',
            'konfirmasi_password' => 'rahasia123',
            'alamat' => 'Jalan Mobile',
            'no_hp' => '08123456789',
        ])->assertCreated()
            ->assertJsonPath('email', 'mobile@example.test');

        Mail::assertSent(NasabahVerificationLinkMail::class, function (NasabahVerificationLinkMail $mail): bool {
            return $mail->hasTo('mobile@example.test')
                && str_contains($mail->verificationUrl, '/nasabah/verifikasi-email/41/');
        });

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'POST'
                && str_contains($request->url(), '/rest/v1/nasabah')
                && $request['email'] === 'mobile@example.test'
                && $request['email_verified_at'] === null
                && strlen((string) $request['email_verification_token_hash']) === 64;
        });
    }

    public function test_mobile_password_reset_sends_greenpoint_mail_with_firebase_link(): void
    {
        Mail::fake();

        $resetUrl = 'https://greenpoint-d9611.firebaseapp.com/__/auth/action?mode=resetPassword&oobCode=token';

        $this->app->instance(FirebasePasswordResetLinkGenerator::class, new class($resetUrl) extends FirebasePasswordResetLinkGenerator
        {
            public function __construct(
                private string $resetUrl,
            ) {}

            public function generate(string $email, ?string $userIp = null): string
            {
                return $this->resetUrl;
            }
        });

        Http::fake([
            '*' => Http::response([[
                'id_nasabah' => 41,
                'email' => 'mobile@example.test',
                'nama_lengkap' => 'Nasabah Mobile',
                'password' => 'firebase-auth:uid-mobile',
                'google_id' => null,
                'google_sub' => null,
            ]], 200),
        ]);

        $this->postJson('/api/mobile/nasabah/password-reset', [
            'identifier' => 'nasabahmobile',
        ])->assertOk();

        Mail::assertSent(NasabahPasswordResetLinkMail::class, function (NasabahPasswordResetLinkMail $mail) use ($resetUrl): bool {
            return $mail->hasTo('mobile@example.test')
                && $mail->recipientName === 'Nasabah Mobile'
                && $mail->resetUrl === $resetUrl;
        });
    }

    public function test_mobile_verified_manual_login_can_be_verified_by_laravel(): void
    {
        Http::fake([
            '*' => Http::response([[
                'id_nasabah' => 41,
                'email' => 'mobile@example.test',
                'nama_lengkap' => 'Nasabah Mobile',
                'user_name' => 'nasabahmobile',
                'password' => password_hash('rahasia123', PASSWORD_BCRYPT),
                'email_verified_at' => now()->toIso8601String(),
                'google_id' => null,
                'google_sub' => null,
                'saldo' => 0,
            ]], 200),
        ]);

        $this->postJson('/api/mobile/nasabah/verify-login', [
            'identifier' => 'mobile@example.test',
            'password' => 'rahasia123',
        ])->assertOk()
            ->assertJsonPath('user.email', 'mobile@example.test')
            ->assertJsonPath('user.nama_lengkap', 'Nasabah Mobile');
    }

    public function test_mobile_unverified_manual_login_returns_email_not_verified(): void
    {
        Http::fake([
            '*' => Http::response([[
                'id_nasabah' => 41,
                'email' => 'mobile@example.test',
                'nama_lengkap' => 'Nasabah Mobile',
                'password' => password_hash('rahasia123', PASSWORD_BCRYPT),
                'email_verified_at' => null,
                'google_id' => null,
                'google_sub' => null,
            ]], 200),
        ]);

        $this->postJson('/api/mobile/nasabah/verify-login', [
            'identifier' => 'mobile@example.test',
            'password' => 'rahasia123',
        ])->assertForbidden()
            ->assertJsonPath('message', 'Email belum diverifikasi.')
            ->assertJsonPath('email', 'mobile@example.test');
    }
}
