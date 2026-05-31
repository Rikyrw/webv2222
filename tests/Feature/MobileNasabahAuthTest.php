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
            'password' => 'Rahasia1!',
            'konfirmasi_password' => 'Rahasia1!',
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

    public function test_mobile_registration_rejects_password_that_does_not_match_policy(): void
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
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);

        $this->assertDatabaseMissing('nasabah', [
            'email' => 'mobile@example.test',
        ]);

        Mail::assertNothingSent();
    }

    public function test_mobile_registration_rejects_username_with_spaces(): void
    {
        Mail::fake();

        $this->postJson('/api/mobile/nasabah/register', [
            'nama' => 'Nasabah Mobile',
            'username' => 'nasabah mobile',
            'email' => 'mobile@example.test',
            'password' => 'Rahasia1!',
            'konfirmasi_password' => 'Rahasia1!',
            'alamat' => 'Jalan Mobile',
            'no_hp' => '08123456789',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['username']);

        $this->assertDatabaseMissing('nasabah', [
            'email' => 'mobile@example.test',
        ]);

        Mail::assertNothingSent();
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
            ->assertJsonPath('message', 'Link reset password sudah dikirim. Cek inbox atau folder spam email Anda.');

        Mail::assertNotSent(NasabahPasswordResetLinkMail::class);

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'POST'
                && str_contains($request->url(), 'accounts:sendOobCode?key=test-api-key')
                && $request['requestType'] === 'PASSWORD_RESET'
                && $request['email'] === 'mobile@example.test'
                && !isset($request['returnOobLink']);
        });
    }

    public function test_mobile_password_reset_returns_not_found_when_identifier_is_unknown(): void
    {
        $this->postJson('/api/mobile/nasabah/password-reset', [
            'identifier' => 'tidakada@example.test',
        ])->assertNotFound()
            ->assertJsonPath('message', 'Email atau username tidak ditemukan. Periksa kembali atau daftar akun baru.');
    }

    public function test_mobile_password_reset_requires_verified_email(): void
    {
        Mail::fake();

        $this->createNasabah([
            'password' => 'firebase-auth:uid-mobile',
            'email_verified_at' => null,
        ]);

        $this->postJson('/api/mobile/nasabah/password-reset', [
            'identifier' => 'nasabahmobile',
        ])->assertForbidden()
            ->assertJsonPath('message', 'Email akun belum diverifikasi. Verifikasi email dulu sebelum reset password.')
            ->assertJsonPath('email', 'mobile@example.test');

        Mail::assertNotSent(NasabahPasswordResetLinkMail::class);
    }

    public function test_mobile_password_reset_directs_google_account_to_google_login(): void
    {
        Mail::fake();
        $this->createNasabah([
            'password' => 'firebase-auth:google',
            'google_id' => 'google-user-id',
            'google_sub' => 'google-sub-id',
        ]);

        $this->postJson('/api/mobile/nasabah/password-reset', [
            'identifier' => 'mobile@example.test',
        ])->assertUnprocessable()
            ->assertJsonPath('message', 'Akun ini terdaftar lewat Google. Silakan masuk menggunakan tombol Masuk dengan Google.');

        Mail::assertNotSent(NasabahPasswordResetLinkMail::class);
    }

    public function test_mobile_password_reset_allows_legacy_google_id_without_google_sub(): void
    {
        Mail::fake();
        $this->createNasabah([
            'password' => 'firebase-auth:uid-mobile',
            'google_id' => 'legacy-firebase-id',
            'google_sub' => null,
        ]);

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
            'identifier' => 'mobile@example.test',
        ])->assertOk()
            ->assertJsonPath('message', 'Link reset password sudah dikirim. Cek inbox atau folder spam email Anda.');

        Mail::assertSent(NasabahPasswordResetLinkMail::class);
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
            ->assertJsonPath('requires_password_change', true)
            ->assertJsonPath('user.email', 'mobile@example.test')
            ->assertJsonPath('user.nama_lengkap', 'Nasabah Mobile')
            ->assertJsonStructure(['access_token', 'token_type', 'expires_at']);
    }

    public function test_mobile_verified_manual_login_matches_username_case_insensitively(): void
    {
        $this->createNasabah([
            'user_name' => 'NasabahMobile',
            'password' => password_hash('Rahasia1!', PASSWORD_BCRYPT),
            'email_verified_at' => now(),
        ]);

        $this->postJson('/api/mobile/nasabah/verify-login', [
            'identifier' => 'nasabahmobile',
            'password' => 'Rahasia1!',
        ])->assertOk()
            ->assertJsonPath('requires_password_change', false)
            ->assertJsonPath('password_warning', null)
            ->assertJsonPath('user.user_name', 'NasabahMobile')
            ->assertJsonStructure(['access_token', 'token_type', 'expires_at']);
    }

    public function test_mobile_verified_manual_login_accepts_username_without_existing_spaces(): void
    {
        $this->createNasabah([
            'user_name' => 'Kira Wijaya',
            'password' => password_hash('Rahasia1!', PASSWORD_BCRYPT),
            'email_verified_at' => now(),
        ]);

        $this->postJson('/api/mobile/nasabah/verify-login', [
            'identifier' => 'kirawijaya',
            'password' => 'Rahasia1!',
        ])->assertOk()
            ->assertJsonPath('requires_password_change', false)
            ->assertJsonPath('password_warning', null)
            ->assertJsonPath('user.user_name', 'Kira Wijaya')
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

    public function test_mobile_profile_update_rejects_username_with_spaces(): void
    {
        $this->createNasabah([
            'password' => password_hash('Rahasia1!', PASSWORD_BCRYPT),
            'email_verified_at' => now(),
        ]);

        $token = $this->postJson('/api/mobile/nasabah/verify-login', [
            'identifier' => 'mobile@example.test',
            'password' => 'Rahasia1!',
        ])->assertOk()->json('access_token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/mobile/nasabah/profile', [
                'nama_lengkap' => 'Nasabah Mobile',
                'user_name' => 'nasabah mobile',
                'email' => 'mobile@example.test',
                'alamat' => 'Jalan Mobile',
                'no_hp' => '08123456789',
            ])->assertUnprocessable()
            ->assertJsonValidationErrors(['user_name'])
            ->assertJsonPath('errors.user_name.0', 'Username hanya boleh berisi huruf, angka, dan underscore tanpa spasi.');
    }

    public function test_mobile_profile_email_change_marks_new_email_unverified_and_sends_verification_link(): void
    {
        Mail::fake();

        $this->createNasabah([
            'password' => password_hash('Rahasia1!', PASSWORD_BCRYPT),
            'email_verified_at' => now(),
        ]);

        $token = $this->postJson('/api/mobile/nasabah/verify-login', [
            'identifier' => 'mobile@example.test',
            'password' => 'Rahasia1!',
        ])->assertOk()->json('access_token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/mobile/nasabah/profile', [
                'nama_lengkap' => 'Nasabah Mobile',
                'user_name' => 'nasabahmobile',
                'email' => 'mobile-baru@example.test',
                'alamat' => 'Jalan Mobile',
                'no_hp' => '08123456789',
            ])->assertOk()
            ->assertJsonPath('email_verification_required', true)
            ->assertJsonPath('verification_email_sent', true)
            ->assertJsonPath('data.email', 'mobile-baru@example.test')
            ->assertJsonPath('data.email_verified_at', null);

        $user = Nasabah::where('email', 'mobile-baru@example.test')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        $this->assertSame(64, strlen((string) $user->email_verification_token_hash));
        $this->assertNotNull($user->email_verification_expires_at);

        Mail::assertSent(NasabahVerificationLinkMail::class, function (NasabahVerificationLinkMail $mail): bool {
            return $mail->hasTo('mobile-baru@example.test')
                && str_contains($mail->verificationUrl, '/nasabah/verifikasi-email/');
        });
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

    public function test_mobile_mirror_profile_rejects_username_with_spaces(): void
    {
        $this->postJson('/api/mobile/nasabah/mirror-profile', [
            'firebase_uid' => 'new-uid',
            'email' => 'mobile-new@example.test',
            'user_name' => 'nasabah mobile',
            'nama_lengkap' => 'Nasabah Mobile',
            'provider' => 'firebase',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['user_name'])
            ->assertJsonPath('errors.user_name.0', 'Username hanya boleh berisi huruf, angka, dan underscore tanpa spasi.');

        $this->assertDatabaseMissing('nasabah', [
            'email' => 'mobile-new@example.test',
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
            'email_verified_at' => now(),
            ...$overrides,
        ]);
    }
}
