<?php

namespace Tests\Feature;

use App\Http\Controllers\NasabahEmailVerificationController;
use App\Mail\NasabahPasswordResetLinkMail;
use App\Models\Nasabah;
use App\Services\FirebasePasswordResetLinkGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NasabahPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_email_renders_visible_firebase_reset_url(): void
    {
        $resetUrl = 'https://greenpoint-d9611.firebaseapp.com/__/auth/action?mode=resetPassword&oobCode=token';

        $html = (new NasabahPasswordResetLinkMail(
            'Nasabah Manual',
            $resetUrl,
        ))->render();

        $this->assertStringContainsString(e($resetUrl), $html);
        $this->assertStringContainsString('Jika tombol tidak muncul, buka link reset berikut:', $html);
    }

    public function test_password_reset_sends_greenpoint_mail_with_firebase_reset_link(): void
    {
        Mail::fake();
        $this->createNasabah(['password' => 'firebase-auth:uid-manual']);

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

        $this->post(route('nasabah.password.email'), [
            'email' => ' MANUAL@EXAMPLE.TEST ',
        ])->assertRedirect()
            ->assertSessionHas('success', 'Jika email terdaftar sebagai akun manual, link reset password sudah dikirim.');

        $this->assertSame('firebase-auth-pending', Nasabah::where('email', 'manual@example.test')->firstOrFail()->getAttribute('password'));

        Mail::assertSent(NasabahPasswordResetLinkMail::class, function (NasabahPasswordResetLinkMail $mail) use ($resetUrl): bool {
            return $mail->hasTo('manual@example.test')
                && $mail->recipientName === 'Nasabah Manual'
                && $mail->resetUrl === $resetUrl;
        });
    }

    public function test_password_reset_falls_back_to_firebase_email_when_custom_link_is_unavailable(): void
    {
        Mail::fake();
        $this->createNasabah(['password' => 'firebase-auth:uid-manual']);

        config([
            'services.firebase.api_key' => 'test-api-key',
            'services.firebase.service_account_path' => base_path('missing-firebase-service-account.json'),
        ]);

        Http::fake([
            '*accounts:signUp*' => Http::response([
                'error' => ['message' => 'EMAIL_EXISTS'],
            ], 400),
            '*accounts:sendOobCode*' => Http::response(['email' => 'manual@example.test'], 200),
        ]);

        $this->post(route('nasabah.password.email'), [
            'email' => 'manual@example.test',
        ])->assertRedirect()
            ->assertSessionHas('success', 'Jika email terdaftar sebagai akun manual, link reset password sudah dikirim.');

        Mail::assertNotSent(NasabahPasswordResetLinkMail::class);

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'POST'
                && str_contains($request->url(), 'accounts:sendOobCode?key=test-api-key')
                && $request['requestType'] === 'PASSWORD_RESET'
                && $request['email'] === 'manual@example.test'
                && !isset($request['returnOobLink']);
        });
    }

    public function test_password_reset_is_not_sent_when_email_is_unverified(): void
    {
        Mail::fake();

        $resetLinks = new class extends FirebasePasswordResetLinkGenerator
        {
            public bool $called = false;

            public function canGenerateCustomLink(): bool
            {
                $this->called = true;

                return true;
            }

            public function generate(string $email, ?string $userIp = null): string
            {
                $this->called = true;

                return 'https://greenpoint.test/reset';
            }

            public function sendPasswordResetEmail(string $email, ?string $userIp = null): void
            {
                $this->called = true;
            }
        };

        $this->app->instance(FirebasePasswordResetLinkGenerator::class, $resetLinks);
        $this->createNasabah([
            'password' => 'firebase-auth:uid-manual',
            'email_verified_at' => null,
        ]);

        $this->post(route('nasabah.password.email'), [
            'email' => 'manual@example.test',
        ])->assertRedirect(route('nasabah.verification.notice'))
            ->assertSessionHasErrors(['email' => 'Email belum diverifikasi. Verifikasi email dulu sebelum reset password.'])
            ->assertSessionHas(NasabahEmailVerificationController::SESSION_KEY, [
                'id_nasabah' => (int) Nasabah::where('email', 'manual@example.test')->firstOrFail()->id_nasabah,
                'email' => 'manual@example.test',
            ]);

        $this->assertFalse($resetLinks->called);
        Mail::assertNotSent(NasabahPasswordResetLinkMail::class);
    }

    public function test_password_reset_prepares_firebase_account_for_pending_email(): void
    {
        Mail::fake();

        config([
            'services.firebase.api_key' => 'test-api-key',
        ]);

        Http::fake([
            '*accounts:signUp*' => Http::response(['localId' => 'new-firebase-uid'], 200),
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

        $this->createNasabah([
            'email' => 'manual-baru@example.test',
            'password' => 'firebase-auth-pending',
            'email_verified_at' => now(),
        ]);

        $this->post(route('nasabah.password.email'), [
            'email' => 'manual-baru@example.test',
        ])->assertRedirect()
            ->assertSessionHas('success', 'Jika email terdaftar sebagai akun manual, link reset password sudah dikirim.');

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'POST'
                && str_contains($request->url(), 'accounts:signUp?key=test-api-key')
                && $request['email'] === 'manual-baru@example.test'
                && $request['returnSecureToken'] === true;
        });

        Mail::assertSent(NasabahPasswordResetLinkMail::class, function (NasabahPasswordResetLinkMail $mail) use ($resetUrl): bool {
            return $mail->hasTo('manual-baru@example.test')
                && $mail->resetUrl === $resetUrl;
        });
    }

    public function test_password_reset_requests_are_throttled(): void
    {
        Http::fake();

        for ($request = 0; $request < 3; $request++) {
            $this->post(route('nasabah.password.email'), [
                'email' => 'manual@example.test',
            ])->assertRedirect();
        }

        $this->post(route('nasabah.password.email'), [
            'email' => 'manual@example.test',
        ])->assertTooManyRequests();
    }

    private function createNasabah(array $overrides = []): Nasabah
    {
        return Nasabah::create([
            'nama_lengkap' => 'Nasabah Manual',
            'user_name' => 'nasabahmanual',
            'email' => 'manual@example.test',
            'password' => password_hash('rahasia1', PASSWORD_BCRYPT),
            'status' => 'aktif',
            'saldo' => 0,
            'created_at' => now(),
            'email_verified_at' => now(),
            ...$overrides,
        ]);
    }
}
