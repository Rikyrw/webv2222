<?php

namespace Tests\Feature;

use App\Http\Controllers\NasabahEmailVerificationController;
use App\Mail\NasabahVerificationLinkMail;
use App\Models\Nasabah;
use App\Services\FirebaseAuthUserManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
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

    public function test_manual_registration_rejects_password_that_does_not_match_policy(): void
    {
        Mail::fake();

        $this->post(route('nasabah.store'), [
            ...$this->registrationPayload(),
            'password' => 'rahasia1',
            'konfirmasi_password' => 'rahasia1',
        ])->assertSessionHasErrors(['password']);

        $this->assertDatabaseMissing('nasabah', [
            'email' => 'manual@example.test',
        ]);

        Mail::assertNothingSent();
    }

    public function test_manual_registration_rejects_username_with_spaces(): void
    {
        Mail::fake();

        $this->post(route('nasabah.store'), [
            ...$this->registrationPayload(),
            'username' => 'nasabah manual',
        ])->assertSessionHasErrors(['username']);

        $this->assertDatabaseMissing('nasabah', [
            'email' => 'manual@example.test',
        ]);

        Mail::assertNothingSent();
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

    public function test_verified_manual_login_with_legacy_weak_password_still_succeeds_with_warning(): void
    {
        $this->createNasabah([
            'email_verified_at' => now(),
        ]);

        $response = $this->post(route('nasabah.authenticate'), [
            'username' => 'manual@example.test',
            'password' => 'rahasia1',
        ]);

        $response->assertRedirect(route('nasabah.dashboard'));
        $response->assertSessionHas('id_nasabah');
        $response->assertSessionHas(
            \App\Support\PasswordPolicy::WARNING_SESSION_KEY,
            \App\Support\PasswordPolicy::WARNING_MESSAGE,
        );

        $this->get(route('nasabah.dashboard'))
            ->assertOk()
            ->assertSee(\App\Support\PasswordPolicy::WARNING_MESSAGE);
    }

    public function test_verified_manual_login_matches_username_case_insensitively(): void
    {
        $this->createNasabah([
            'user_name' => 'NasabahManual',
            'password' => password_hash('Rahasia1!', PASSWORD_BCRYPT),
            'email_verified_at' => now(),
        ]);

        $response = $this->post(route('nasabah.authenticate'), [
            'username' => 'nasabahmanual',
            'password' => 'Rahasia1!',
        ]);

        $response->assertRedirect(route('nasabah.dashboard'));
        $response->assertSessionHas('id_nasabah');
        $response->assertSessionMissing(\App\Support\PasswordPolicy::WARNING_SESSION_KEY);
    }

    public function test_verified_manual_login_accepts_username_without_existing_spaces(): void
    {
        $this->createNasabah([
            'user_name' => 'Kira Wijaya',
            'password' => password_hash('Rahasia1!', PASSWORD_BCRYPT),
            'email_verified_at' => now(),
        ]);

        $response = $this->post(route('nasabah.authenticate'), [
            'username' => 'kirawijaya',
            'password' => 'Rahasia1!',
        ]);

        $response->assertRedirect(route('nasabah.dashboard'));
        $response->assertSessionHas('id_nasabah');
    }

    public function test_profile_update_rejects_username_with_spaces(): void
    {
        $user = $this->createNasabah([
            'password' => password_hash('Rahasia1!', PASSWORD_BCRYPT),
            'email_verified_at' => now(),
        ]);

        $this->withSession(['id_nasabah' => $user->id_nasabah])
            ->post(route('nasabah.profil.update'), [
                'username' => 'nasabah manual',
                'nama_nasabah' => 'Nasabah Manual',
                'email' => 'manual@example.test',
                'no_hp' => '08123456789',
                'alamat' => 'Jalan Hijau',
            ])
            ->assertSessionHasErrors(['username']);
    }

    public function test_profile_email_change_marks_new_email_unverified_and_sends_verification_link(): void
    {
        Mail::fake();

        $user = $this->createNasabah([
            'password' => password_hash('Rahasia1!', PASSWORD_BCRYPT),
            'email_verified_at' => now(),
        ]);

        $response = $this->withSession(['id_nasabah' => $user->id_nasabah])
            ->post(route('nasabah.profil.update'), [
                'username' => 'nasabahmanual',
                'nama_nasabah' => 'Nasabah Manual',
                'email' => 'manual-baru@example.test',
                'no_hp' => '08123456789',
                'alamat' => 'Jalan Hijau',
            ]);

        $response->assertRedirect(route('nasabah.verification.notice'));
        $response->assertSessionHas('success', 'Email profil diperbarui. Link verifikasi sudah dikirim ke email baru. Silakan verifikasi sebelum login lagi.');
        $response->assertSessionMissing('id_nasabah');
        $response->assertSessionHas(NasabahEmailVerificationController::SESSION_KEY, [
            'id_nasabah' => (int) $user->id_nasabah,
            'email' => 'manual-baru@example.test',
        ]);

        $fresh = $user->fresh();
        $this->assertSame('manual-baru@example.test', $fresh->email);
        $this->assertNull($fresh->email_verified_at);
        $this->assertSame(64, strlen((string) $fresh->email_verification_token_hash));
        $this->assertNotNull($fresh->email_verification_expires_at);

        Mail::assertSent(NasabahVerificationLinkMail::class, function (NasabahVerificationLinkMail $mail): bool {
            return $mail->hasTo('manual-baru@example.test')
                && str_contains($mail->verificationUrl, '/nasabah/verifikasi-email/');
        });
    }

    public function test_profile_email_change_can_be_verified_then_login_with_new_email_for_local_password(): void
    {
        Mail::fake();

        $user = $this->createNasabah([
            'password' => password_hash('Rahasia1!', PASSWORD_BCRYPT),
            'email_verified_at' => now(),
        ]);

        $this->withSession(['id_nasabah' => $user->id_nasabah])
            ->post(route('nasabah.profil.update'), [
                'username' => 'nasabahmanual',
                'nama_nasabah' => 'Nasabah Manual',
                'email' => 'manual-baru@example.test',
                'no_hp' => '08123456789',
                'alamat' => 'Jalan Hijau',
            ])
            ->assertRedirect(route('nasabah.verification.notice'));

        $verificationUrl = null;
        Mail::assertSent(NasabahVerificationLinkMail::class, function (NasabahVerificationLinkMail $mail) use (&$verificationUrl): bool {
            $verificationUrl = $mail->verificationUrl;

            return $mail->hasTo('manual-baru@example.test');
        });

        $this->get($verificationUrl)
            ->assertRedirect(route('nasabah.login'))
            ->assertSessionHas('success', 'Email berhasil diverifikasi. Silakan login.');

        $this->post(route('nasabah.authenticate'), [
            'username' => 'manual-baru@example.test',
            'password' => 'Rahasia1!',
        ])->assertRedirect(route('nasabah.dashboard'))
            ->assertSessionHas('id_nasabah', $user->id_nasabah);
    }

    public function test_profile_email_change_does_not_change_existing_firebase_password_marker(): void
    {
        Mail::fake();
        config(['services.firebase.api_key' => 'test-api-key']);

        $firebaseUsers = new class extends FirebaseAuthUserManager
        {
            public array $updates = [];

            public function updateEmailByUid(string $firebaseUid, string $email, bool $emailVerified = true): bool
            {
                $this->updates[] = compact('firebaseUid', 'email', 'emailVerified');

                return true;
            }
        };

        $this->app->instance(FirebaseAuthUserManager::class, $firebaseUsers);

        $user = $this->createNasabah([
            'password' => 'firebase-auth:old-uid',
            'email_verified_at' => now(),
        ]);

        $this->withSession(['id_nasabah' => $user->id_nasabah])
            ->post(route('nasabah.profil.update'), [
                'username' => 'nasabahmanual',
                'nama_nasabah' => 'Nasabah Manual',
                'email' => 'manual-baru@example.test',
                'no_hp' => '08123456789',
                'alamat' => 'Jalan Hijau',
            ])
            ->assertRedirect(route('nasabah.verification.notice'));

        $this->assertSame('firebase-auth:old-uid', $user->fresh()->getAttribute('password'));

        $verificationUrl = null;
        Mail::assertSent(NasabahVerificationLinkMail::class, function (NasabahVerificationLinkMail $mail) use (&$verificationUrl): bool {
            $verificationUrl = $mail->verificationUrl;

            return $mail->hasTo('manual-baru@example.test');
        });

        $this->get($verificationUrl)
            ->assertRedirect(route('nasabah.login'))
            ->assertSessionHas('success', 'Email berhasil diverifikasi. Silakan login.');

        $fresh = $user->fresh();
        $this->assertNotNull($fresh->email_verified_at);
        $this->assertSame('firebase-auth:old-uid', $fresh->getAttribute('password'));
        $this->assertSame([
            [
                'firebaseUid' => 'old-uid',
                'email' => 'manual-baru@example.test',
                'emailVerified' => true,
            ],
        ], $firebaseUsers->updates);

        Http::fake([
            '*accounts:signInWithPassword*' => Http::response([
                'localId' => 'old-uid',
                'email' => 'manual-baru@example.test',
            ], 200),
        ]);

        $this->post(route('nasabah.authenticate'), [
            'username' => 'manual-baru@example.test',
            'password' => 'PasswordLama1!',
        ])->assertRedirect(route('nasabah.dashboard'))
            ->assertSessionHas('id_nasabah', $user->id_nasabah);

        Http::assertSent(function (Request $request): bool {
            return str_contains($request->url(), 'accounts:signInWithPassword?key=test-api-key')
                && $request['email'] === 'manual-baru@example.test'
                && $request['password'] === 'PasswordLama1!';
        });
    }

    public function test_login_repairs_firebase_email_sync_for_already_verified_changed_email(): void
    {
        config(['services.firebase.api_key' => 'test-api-key']);

        $firebaseUsers = new class extends FirebaseAuthUserManager
        {
            public array $updates = [];

            public function updateEmailByUid(string $firebaseUid, string $email, bool $emailVerified = true): bool
            {
                $this->updates[] = compact('firebaseUid', 'email', 'emailVerified');

                return true;
            }
        };

        $this->app->instance(FirebaseAuthUserManager::class, $firebaseUsers);

        $user = $this->createNasabah([
            'email' => 'manual-baru@example.test',
            'password' => 'firebase-auth:old-uid',
            'email_verified_at' => now(),
        ]);

        Http::fake([
            '*accounts:signInWithPassword*' => Http::sequence()
                ->push(['error' => ['message' => 'EMAIL_NOT_FOUND']], 400)
                ->push([
                    'localId' => 'old-uid',
                    'email' => 'manual-baru@example.test',
                ], 200),
        ]);

        $this->post(route('nasabah.authenticate'), [
            'username' => 'manual-baru@example.test',
            'password' => 'PasswordLama1!',
        ])->assertRedirect(route('nasabah.dashboard'))
            ->assertSessionHas('id_nasabah', $user->id_nasabah);

        $this->assertSame([
            [
                'firebaseUid' => 'old-uid',
                'email' => 'manual-baru@example.test',
                'emailVerified' => true,
            ],
        ], $firebaseUsers->updates);
    }

    public function test_verification_still_marks_email_verified_when_firebase_sync_fails(): void
    {
        Mail::fake();

        $firebaseUsers = new class extends FirebaseAuthUserManager
        {
            public function updateEmailByUid(string $firebaseUid, string $email, bool $emailVerified = true): bool
            {
                return false;
            }
        };

        $this->app->instance(FirebaseAuthUserManager::class, $firebaseUsers);

        $user = $this->createNasabah([
            'password' => 'firebase-auth:old-uid',
            'email_verified_at' => now(),
        ]);

        $this->withSession(['id_nasabah' => $user->id_nasabah])
            ->post(route('nasabah.profil.update'), [
                'username' => 'nasabahmanual',
                'nama_nasabah' => 'Nasabah Manual',
                'email' => 'manual-baru@example.test',
                'no_hp' => '08123456789',
                'alamat' => 'Jalan Hijau',
            ])
            ->assertRedirect(route('nasabah.verification.notice'));

        $verificationUrl = null;
        Mail::assertSent(NasabahVerificationLinkMail::class, function (NasabahVerificationLinkMail $mail) use (&$verificationUrl): bool {
            $verificationUrl = $mail->verificationUrl;

            return $mail->hasTo('manual-baru@example.test');
        });

        $this->get($verificationUrl)
            ->assertRedirect(route('nasabah.login'))
            ->assertSessionHas('success', 'Email berhasil diverifikasi. Silakan login.');

        $fresh = $user->fresh();
        $this->assertNotNull($fresh->email_verified_at);
        $this->assertNull($fresh->email_verification_token_hash);
        $this->assertNull($fresh->email_verification_expires_at);
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
            'password' => 'Rahasia1!',
            'konfirmasi_password' => 'Rahasia1!',
            'alamat' => 'Jalan Hijau',
            'no_hp' => '08123456789',
        ];
    }
}
