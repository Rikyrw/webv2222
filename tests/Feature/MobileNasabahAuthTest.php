<?php

namespace Tests\Feature;

use App\Mail\NasabahPasswordResetLinkMail;
use App\Mail\NasabahVerificationLinkMail;
use App\Models\Nasabah;
use App\Services\FirebasePasswordResetLinkGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MobileNasabahAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_registration_creates_unverified_account_and_sends_verification_email(): void
    {
        Mail::fake();

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

        $user = Nasabah::where('email', 'mobile@example.test')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        $this->assertSame(64, strlen((string) $user->email_verification_token_hash));

        Mail::assertSent(NasabahVerificationLinkMail::class, function (NasabahVerificationLinkMail $mail): bool {
            return $mail->hasTo('mobile@example.test')
                && str_contains($mail->verificationUrl, '/nasabah/verifikasi-email/');
        });
    }

    public function test_mobile_password_reset_sends_greenpoint_mail_with_firebase_link(): void
    {
        Mail::fake();
        $this->createNasabah(['password' => 'firebase-auth:uid-mobile']);

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

            public function canGenerateCustomLink(): bool
            {
                return true;
            }
        });

        $this->postJson('/api/mobile/nasabah/password-reset', [
            'identifier' => 'nasabahmobile',
        ])->assertOk();

        Mail::assertSent(NasabahPasswordResetLinkMail::class, function (NasabahPasswordResetLinkMail $mail) use ($resetUrl): bool {
            return $mail->hasTo('mobile@example.test')
                && $mail->recipientName === 'Nasabah Mobile'
                && $mail->resetUrl === $resetUrl;
        });
    }

    public function test_mobile_password_reset_falls_back_to_firebase_email_when_custom_link_is_unavailable(): void
    {
        Mail::fake();
        $this->createNasabah(['password' => 'firebase-auth:uid-mobile']);

        config([
            'services.firebase.api_key' => 'test-api-key',
            'services.firebase.service_account_path' => base_path('missing-firebase-service-account.json'),
        ]);

        Http::fake([
            '*accounts:sendOobCode*' => Http::response(['email' => 'mobile@example.test'], 200),
        ]);

        $this->postJson('/api/mobile/nasabah/password-reset', [
            'identifier' => 'nasabahmobile',
        ])->assertOk()
            ->assertJsonPath('message', 'Jika akun ditemukan, link reset password sudah dikirim ke email Anda.');

        Mail::assertNotSent(NasabahPasswordResetLinkMail::class);

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'POST'
                && str_contains($request->url(), 'accounts:sendOobCode?key=test-api-key')
                && $request['requestType'] === 'PASSWORD_RESET'
                && $request['email'] === 'mobile@example.test'
                && !isset($request['returnOobLink']);
        });
    }

    public function test_mobile_verified_manual_login_can_be_verified_by_laravel(): void
    {
        $this->createNasabah([
            'password' => password_hash('rahasia123', PASSWORD_BCRYPT),
            'email_verified_at' => now(),
        ]);

        $this->postJson('/api/mobile/nasabah/verify-login', [
            'identifier' => 'mobile@example.test',
            'password' => 'rahasia123',
        ])->assertOk()
            ->assertJsonPath('user.email', 'mobile@example.test')
            ->assertJsonPath('user.nama_lengkap', 'Nasabah Mobile')
            ->assertJsonStructure(['access_token', 'token_type', 'expires_at']);
    }

    public function test_mobile_profile_requires_and_accepts_sanctum_token(): void
    {
        $this->createNasabah([
            'password' => password_hash('rahasia123', PASSWORD_BCRYPT),
            'email_verified_at' => now(),
        ]);

        $this->getJson('/api/mobile/nasabah/profile')
            ->assertUnauthorized();

        $token = $this->postJson('/api/mobile/nasabah/verify-login', [
            'identifier' => 'mobile@example.test',
            'password' => 'rahasia123',
        ])->assertOk()->json('access_token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/mobile/nasabah/profile')
            ->assertOk()
            ->assertJsonPath('data.email', 'mobile@example.test')
            ->assertJsonPath('data.nama_lengkap', 'Nasabah Mobile');
    }

    public function test_mobile_unverified_manual_login_returns_email_not_verified(): void
    {
        $this->createNasabah([
            'password' => password_hash('rahasia123', PASSWORD_BCRYPT),
            'email_verified_at' => null,
        ]);

        $this->postJson('/api/mobile/nasabah/verify-login', [
            'identifier' => 'mobile@example.test',
            'password' => 'rahasia123',
        ])->assertForbidden()
            ->assertJsonPath('message', 'Email belum diverifikasi.')
            ->assertJsonPath('email', 'mobile@example.test');
    }

    public function test_mobile_deactivated_manual_login_returns_contact_cs_message(): void
    {
        $this->createNasabah([
            'password' => password_hash('rahasia123', PASSWORD_BCRYPT),
            'email_verified_at' => now(),
            'status' => 'nonaktif',
        ]);

        $this->postJson('/api/mobile/nasabah/verify-login', [
            'identifier' => 'mobile@example.test',
            'password' => 'rahasia123',
        ])->assertForbidden()
            ->assertJsonPath('message', 'Akun Anda sedang nonaktif. Silakan hubungi CS GreenPoint untuk bantuan lebih lanjut.')
            ->assertJsonPath('account_status', 'nonaktif');
    }

    public function test_mobile_mirror_profile_does_not_reactivate_deactivated_account(): void
    {
        $this->createNasabah([
            'email_verified_at' => now(),
            'status' => 'nonaktif',
            'password' => 'firebase-auth:old-uid',
        ]);

        $this->postJson('/api/mobile/nasabah/mirror-profile', [
            'firebase_uid' => 'new-uid',
            'email' => 'mobile@example.test',
            'user_name' => 'nasabahmobile',
            'nama_lengkap' => 'Nasabah Mobile',
            'provider' => 'firebase',
        ])->assertForbidden()
            ->assertJsonPath('message', 'Akun Anda sedang nonaktif. Silakan hubungi CS GreenPoint untuk bantuan lebih lanjut.')
            ->assertJsonPath('account_status', 'nonaktif');

        $this->assertDatabaseHas('nasabah', [
            'email' => 'mobile@example.test',
            'status' => 'nonaktif',
        ]);
    }

    private function createNasabah(array $overrides = []): Nasabah
    {
        return Nasabah::create([
            'nama_lengkap' => 'Nasabah Mobile',
            'user_name' => 'nasabahmobile',
            'email' => 'mobile@example.test',
            'password' => password_hash('rahasia123', PASSWORD_BCRYPT),
            'alamat' => 'Jalan Mobile',
            'no_hp' => '08123456789',
            'status' => 'aktif',
            'saldo' => 0,
            'created_at' => now(),
            ...$overrides,
        ]);
    }
}
