<?php

namespace Tests\Feature;

use App\Mail\NasabahVerificationLinkMail;
use App\Models\Nasabah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NasabahEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

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

        $response = $this->post(route('nasabah.store'), $this->registrationPayload());

        $response->assertRedirect(route('nasabah.verification.notice'));
        $response->assertSessionHas('success', 'Akun dibuat. Link verifikasi sudah dikirim ke email Anda.');

        $user = Nasabah::where('email', 'manual@example.test')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        $this->assertSame(64, strlen((string) $user->email_verification_token_hash));
        $this->assertNotNull($user->email_verification_expires_at);

        Mail::assertSent(NasabahVerificationLinkMail::class, function (NasabahVerificationLinkMail $mail): bool {
            return $mail->hasTo('manual@example.test')
                && str_contains($mail->verificationUrl, '/nasabah/verifikasi-email/');
        });
    }

    public function test_verification_link_marks_email_verified_and_consumes_token(): void
    {
        Mail::fake();

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

        $user = Nasabah::where('email', 'manual@example.test')->firstOrFail();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->email_verification_token_hash);
        $this->assertNull($user->email_verification_expires_at);
    }

    public function test_unverified_manual_login_is_redirected_to_verification_notice(): void
    {
        $this->createNasabah(['email_verified_at' => null]);

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
        $this->createNasabah(['email_verified_at' => null]);

        $response = $this->post(route('nasabah.authenticate'), [
            'username' => 'manual@example.test',
            'password' => 'password-yang-belum-dipakai',
        ]);

        $response->assertRedirect(route('nasabah.verification.notice'));
        $response->assertSessionHas('error', 'Email belum diverifikasi.');
        $response->assertSessionMissing('id_nasabah');
    }

    public function test_deactivated_manual_login_shows_contact_cs_message(): void
    {
        $this->createNasabah([
            'status' => 'nonaktif',
            'email_verified_at' => now(),
        ]);

        $response = $this->post(route('nasabah.authenticate'), [
            'username' => 'manual@example.test',
            'password' => 'rahasia1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Akun Anda sedang nonaktif. Silakan hubungi CS GreenPoint untuk bantuan lebih lanjut.');
        $response->assertSessionMissing('id_nasabah');
    }

    public function test_deactivated_google_login_returns_contact_cs_message(): void
    {
        config(['services.google.client_id' => 'google-client.test']);
        $this->createNasabah([
            'email' => 'google@example.test',
            'status' => 'nonaktif',
            'email_verified_at' => now(),
        ]);

        Http::fake([
            'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
                'iss' => 'https://accounts.google.com',
                'aud' => 'google-client.test',
                'exp' => time() + 300,
                'email_verified' => true,
                'email' => 'google@example.test',
                'sub' => 'google-sub-1',
                'name' => 'Google Nasabah',
            ], 200),
        ]);

        $this->postJson(route('nasabah.google.authenticate'), [
            'credential' => 'valid-google-token',
        ])->assertUnprocessable()
            ->assertJsonPath('message', 'Akun Anda sedang nonaktif. Silakan hubungi CS GreenPoint untuk bantuan lebih lanjut.');

        $this->assertFalse(session()->has('id_nasabah'));
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
            ...$overrides,
        ]);
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
