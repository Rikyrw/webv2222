<?php

namespace Tests\Feature;

use App\Mail\NasabahVerificationLinkMail;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NasabahEmailVerificationTest extends TestCase
{
    public function test_verification_email_renders_visible_verification_url(): void
    {
        $verificationUrl = 'https://greenpoint.test/nasabah/verifikasi-email/17/token?signature=baru';

        $html = (new NasabahVerificationLinkMail(
            'Nasabah Manual',
            $verificationUrl,
            60,
        ))->render();

        $this->assertStringContainsString($verificationUrl, $html);
        $this->assertStringContainsString('Jika tombol tidak muncul, buka link verifikasi berikut:', $html);
    }

    public function test_manual_registration_creates_unverified_nasabah_and_sends_verification_link(): void
    {
        Mail::fake();

        Http::fakeSequence()
            ->push([], 200)
            ->push([[
                'id_nasabah' => 17,
                'email' => 'manual@example.test',
                'nama_lengkap' => 'Nasabah Manual',
                'email_verified_at' => null,
            ]], 201);

        $response = $this->post(route('nasabah.store'), $this->registrationPayload());

        $response->assertRedirect(route('nasabah.verification.notice'));
        $response->assertSessionHas('success', 'Akun dibuat. Link verifikasi sudah dikirim ke email Anda.');

        Mail::assertSent(NasabahVerificationLinkMail::class, function (NasabahVerificationLinkMail $mail): bool {
            return $mail->hasTo('manual@example.test')
                && str_contains($mail->verificationUrl, '/nasabah/verifikasi-email/17/');
        });

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'POST'
                && str_contains($request->url(), '/rest/v1/nasabah')
                && $request['email'] === 'manual@example.test'
                && $request['email_verified_at'] === null
                && strlen((string) $request['email_verification_token_hash']) === 64
                && ! empty($request['email_verification_expires_at']);
        });
    }

    public function test_verification_link_marks_email_verified_and_consumes_token(): void
    {
        Mail::fake();

        $user = null;
        $verificationPatch = null;

        Http::fake(function (Request $request) use (&$user, &$verificationPatch) {
            if ($request->method() === 'GET' && str_contains($request->url(), 'select=id_nasabah')) {
                return Http::response([], 200);
            }

            if ($request->method() === 'POST' && str_contains($request->url(), '/rest/v1/nasabah')) {
                $user = [
                    'id_nasabah' => 17,
                    'email' => $request['email'],
                    'nama_lengkap' => $request['nama_lengkap'],
                    'email_verified_at' => null,
                    'email_verification_token_hash' => $request['email_verification_token_hash'],
                    'email_verification_expires_at' => $request['email_verification_expires_at'],
                ];

                return Http::response([$user], 201);
            }

            if ($request->method() === 'GET' && str_contains($request->url(), 'id_nasabah=eq.17')) {
                return Http::response([$user], 200);
            }

            if ($request->method() === 'PATCH' && str_contains($request->url(), 'id_nasabah=eq.17')) {
                $verificationPatch = json_decode($request->body(), true);

                return Http::response([], 204);
            }

            return Http::response([], 500);
        });

        $this->post(route('nasabah.store'), $this->registrationPayload())
            ->assertRedirect(route('nasabah.verification.notice'));

        $verificationUrl = null;

        Mail::assertSent(NasabahVerificationLinkMail::class, function (NasabahVerificationLinkMail $mail) use (&$verificationUrl): bool {
            $verificationUrl = $mail->verificationUrl;

            return true;
        });

        $this->get($verificationUrl)
            ->assertRedirect(route('nasabah.login'))
            ->assertSessionHas('success', 'Email berhasil diverifikasi. Silakan login.');

        $this->assertNotNull($verificationPatch['email_verified_at'] ?? null);
        $this->assertArrayHasKey('email_verification_token_hash', $verificationPatch);
        $this->assertArrayHasKey('email_verification_expires_at', $verificationPatch);
        $this->assertNull($verificationPatch['email_verification_token_hash']);
        $this->assertNull($verificationPatch['email_verification_expires_at']);
    }

    public function test_unverified_manual_login_is_redirected_to_verification_notice(): void
    {
        Http::fake([
            '*' => Http::response([[
                'id_nasabah' => 17,
                'nama_lengkap' => 'Nasabah Manual',
                'user_name' => 'nasabahmanual',
                'email' => 'manual@example.test',
                'password' => password_hash('rahasia1', PASSWORD_BCRYPT),
                'status' => 'aktif',
                'saldo' => 0,
                'email_verified_at' => null,
            ]], 200),
        ]);

        $response = $this->post(route('nasabah.authenticate'), [
            'username' => '  MANUAL@EXAMPLE.TEST  ',
            'password' => 'rahasia1',
        ]);

        $response->assertRedirect(route('nasabah.verification.notice'));
        $response->assertSessionHas('error', 'Email belum diverifikasi.');
        $response->assertSessionMissing('id_nasabah');
    }

    public function test_pending_manual_login_does_not_show_wrong_credentials_before_email_is_verified(): void
    {
        Http::fake([
            '*' => Http::response([[
                'id_nasabah' => 17,
                'nama_lengkap' => 'Nasabah Manual',
                'user_name' => 'nasabahmanual',
                'email' => 'manual@example.test',
                'password' => password_hash('rahasia1', PASSWORD_BCRYPT),
                'status' => 'aktif',
                'saldo' => 0,
                'email_verified_at' => null,
            ]], 200),
        ]);

        $response = $this->post(route('nasabah.authenticate'), [
            'username' => 'manual@example.test',
            'password' => 'password-yang-belum-dipakai',
        ]);

        $response->assertRedirect(route('nasabah.verification.notice'));
        $response->assertSessionHas('error', 'Email belum diverifikasi.');
        $response->assertSessionMissing('id_nasabah');
    }

    public function test_invalid_email_like_login_identifier_shows_format_error_before_authentication(): void
    {
        Http::fake();

        $response = $this->post(route('nasabah.authenticate'), [
            'username' => 'manual@example,test',
            'password' => 'rahasia1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'username' => 'Format email login tidak valid. Periksa kembali penulisan email Anda.',
        ]);

        Http::assertNothingSent();
    }

    private function registrationPayload(): array
    {
        return [
            'nama' => 'Nasabah Manual',
            'username' => 'nasabahmanual',
            'email' => 'manual@example.test',
            'password' => 'rahasia1',
            'konfirmasi_password' => 'rahasia1',
            'alamat' => 'Jalan Hijau',
            'no_hp' => '08123456789',
        ];
    }
}
