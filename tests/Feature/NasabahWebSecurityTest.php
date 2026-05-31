<?php

namespace Tests\Feature;

use App\Models\Nasabah;
use App\Models\TopupSaldo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NasabahWebSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_private_nasabah_pages_redirect_guests_to_login(): void
    {
        $privateRoutes = [
            'nasabah.dashboard',
            'nasabah.transaksi',
            'nasabah.riwayat-setor',
            'nasabah.profil',
            'nasabah.profil.edit',
            'nasabah.setor',
            'nasabah.topup.status',
            'nasabah.emoney',
            'nasabah.pulsa',
            'nasabah.pln',
        ];

        foreach ($privateRoutes as $routeName) {
            $this->get(route($routeName))
                ->assertRedirect(route('nasabah.login'));
        }
    }

    public function test_private_nasabah_actions_redirect_guests_to_login(): void
    {
        $privateRoutes = [
            'nasabah.profil.update',
            'nasabah.setor.detect-photo',
            'nasabah.setor.post',
            'nasabah.topup.create',
            'nasabah.emoney.store',
            'nasabah.pulsa.store',
            'nasabah.pln.store',
        ];

        foreach ($privateRoutes as $routeName) {
            $this->post(route($routeName))
                ->assertRedirect(route('nasabah.login'));
        }
    }

    public function test_deactivated_nasabah_session_is_rejected(): void
    {
        $nasabah = $this->createNasabah([
            'status' => 'nonaktif',
        ]);

        $this->withSession(['id_nasabah' => $nasabah->id_nasabah])
            ->get(route('nasabah.dashboard'))
            ->assertRedirect(route('nasabah.login'))
            ->assertSessionMissing('id_nasabah')
            ->assertSessionHas('error', 'Akun Anda sedang nonaktif. Silakan hubungi CS GreenPoint untuk bantuan lebih lanjut.');
    }

    public function test_topup_status_is_hidden_from_another_web_nasabah(): void
    {
        $owner = $this->createNasabah([
            'email' => 'owner@example.test',
            'user_name' => 'owner',
        ]);
        $other = $this->createNasabah([
            'email' => 'other@example.test',
            'user_name' => 'other',
        ]);
        $topup = $this->createTopup($owner, [
            'order_id' => 'TOPUP-OWNER-1',
        ]);

        $this->withSession(['id_nasabah' => $other->id_nasabah])
            ->getJson(route('nasabah.topup.status', ['order_id' => $topup->order_id]))
            ->assertOk()
            ->assertExactJson(['status' => 'not_found']);
    }

    public function test_duplicate_midtrans_notifications_credit_balance_only_once(): void
    {
        config(['services.midtrans.server_key' => 'midtrans-server-key']);

        $nasabah = $this->createNasabah(['saldo' => 1000]);
        $topup = $this->createTopup($nasabah, [
            'order_id' => 'TOPUP-IDEMPOTENT-1',
            'gross_amount' => 50000,
        ]);

        $payload = [
            'order_id' => $topup->order_id,
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'transaction_status' => 'settlement',
            'transaction_id' => 'transaction-1',
        ];
        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].'midtrans-server-key'
        );

        $this->postJson(route('midtrans.notification'), $payload)
            ->assertOk()
            ->assertExactJson(['message' => 'OK']);

        $this->postJson(route('midtrans.notification'), $payload)
            ->assertOk()
            ->assertExactJson(['message' => 'OK']);

        $this->assertSame('settlement', $topup->fresh()->status);
        $this->assertSame('51000.00', $nasabah->fresh()->saldo);
    }

    public function test_web_topup_uses_logged_in_nasabah_identity(): void
    {
        config([
            'services.midtrans.server_key' => 'midtrans-server-key',
            'services.midtrans.client_key' => 'midtrans-client-key',
            'services.midtrans.is_prod' => false,
        ]);

        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-token',
                'redirect_url' => 'https://example.test/snap',
            ]),
        ]);

        $nasabah = $this->createNasabah();
        $other = $this->createNasabah([
            'email' => 'other@example.test',
            'user_name' => 'other',
        ]);

        $this->withSession(['id_nasabah' => $nasabah->id_nasabah])
            ->postJson(route('nasabah.topup.create'), [
                'nominal' => 10000,
                'id_nasabah' => $other->id_nasabah,
            ])
            ->assertOk()
            ->assertJsonPath('token', 'snap-token');

        $this->assertDatabaseHas('topup_saldo', [
            'id_nasabah' => $nasabah->id_nasabah,
            'gross_amount' => 10000,
        ]);
        $this->assertDatabaseMissing('topup_saldo', [
            'id_nasabah' => $other->id_nasabah,
        ]);
    }

    private function createNasabah(array $overrides = []): Nasabah
    {
        return Nasabah::create([
            'nama_lengkap' => 'Nasabah Manual',
            'user_name' => 'nasabahmanual',
            'email' => 'manual@example.test',
            'password' => password_hash('Rahasia1!', PASSWORD_BCRYPT),
            'status' => 'aktif',
            'saldo' => 0,
            'created_at' => now(),
            ...$overrides,
        ]);
    }

    private function createTopup(Nasabah $nasabah, array $overrides = []): TopupSaldo
    {
        return TopupSaldo::create([
            'id_nasabah' => $nasabah->id_nasabah,
            'order_id' => 'TOPUP-TEST-1',
            'gross_amount' => 10000,
            'status' => 'pending',
            'transaction_status' => 'pending',
            'created_at' => now(),
            ...$overrides,
        ]);
    }
}
