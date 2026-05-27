<?php

namespace Tests\Feature;

use App\Mail\NasabahPasswordResetLinkMail;
use App\Services\FirebasePasswordResetLinkGenerator;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NasabahPasswordResetTest extends TestCase
{
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

        Http::fake([
            '*' => Http::response([[
                'id_nasabah' => 17,
                'email' => 'manual@example.test',
                'nama_lengkap' => 'Nasabah Manual',
                'password' => 'firebase-auth:uid-manual',
                'google_sub' => null,
            ]], 200),
        ]);

        $this->post(route('nasabah.password.email'), [
            'email' => ' MANUAL@EXAMPLE.TEST ',
        ])->assertRedirect()
            ->assertSessionHas('success', 'Jika email terdaftar sebagai akun manual, link reset password sudah dikirim.');

        Mail::assertSent(NasabahPasswordResetLinkMail::class, function (NasabahPasswordResetLinkMail $mail) use ($resetUrl): bool {
            return $mail->hasTo('manual@example.test')
                && $mail->recipientName === 'Nasabah Manual'
                && $mail->resetUrl === $resetUrl;
        });
    }

    public function test_password_reset_falls_back_to_firebase_email_when_custom_link_is_unavailable(): void
    {
        Mail::fake();

        config([
            'services.firebase.api_key' => 'test-api-key',
            'services.firebase.service_account_path' => base_path('missing-firebase-service-account.json'),
        ]);

        Http::fake([
            '*/rest/v1/nasabah*' => Http::response([[
                'id_nasabah' => 17,
                'email' => 'manual@example.test',
                'nama_lengkap' => 'Nasabah Manual',
                'password' => 'firebase-auth:uid-manual',
                'google_sub' => null,
            ]], 200),
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
                && ! isset($request['returnOobLink']);
        });
    }

    public function test_password_reset_requests_are_throttled(): void
    {
        Http::fake([
            '*' => Http::response([], 200),
        ]);

        for ($request = 0; $request < 3; $request++) {
            $this->post(route('nasabah.password.email'), [
                'email' => 'manual@example.test',
            ])->assertRedirect();
        }

        $this->post(route('nasabah.password.email'), [
            'email' => 'manual@example.test',
        ])->assertTooManyRequests();
    }
}
