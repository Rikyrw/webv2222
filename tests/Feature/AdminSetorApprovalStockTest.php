<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\DetailSetor;
use App\Models\Nasabah;
use App\Models\Sampah;
use App\Models\TransaksiSetor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSetorApprovalStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_setor_items_add_to_sampah_stock_once(): void
    {
        $admin = $this->createAdmin();
        $nasabah = $this->createNasabah(['saldo' => 1000]);
        $plastik = $this->createSampah(['nama_jenis' => 'Plastik', 'stok' => 3.50, 'harga_per_kg' => 2000]);
        $kertas = $this->createSampah(['nama_jenis' => 'Kertas', 'stok' => 4.00, 'harga_per_kg' => 1500]);
        $transaksi = $this->createTransaksiSetor($nasabah);

        $approvedDetail = DetailSetor::create([
            'id_transaksi_setor' => $transaksi->id_transaksi_setor,
            'id_jenis' => $plastik->id_jenis_sampah,
            'berat_kg' => 2.25,
            'harga_kg' => 2000,
            'subtotal' => 4500,
            'status_item' => 'pending',
        ]);
        $rejectedDetail = DetailSetor::create([
            'id_transaksi_setor' => $transaksi->id_transaksi_setor,
            'id_jenis' => $kertas->id_jenis_sampah,
            'berat_kg' => 1.50,
            'harga_kg' => 1500,
            'subtotal' => 2250,
            'status_item' => 'pending',
        ]);

        $this->withSession(['admin_logged_in' => true, 'admin_id' => $admin->id_admin])
            ->post(route('admin.transaksi.setor.update', $transaksi->id_transaksi_setor), [
                'decisions' => [
                    $approvedDetail->id_detail_setor => 'approve',
                    $rejectedDetail->id_detail_setor => 'reject',
                ],
                'notes' => [
                    $rejectedDetail->id_detail_setor => 'Foto kurang jelas',
                ],
            ])
            ->assertRedirect(route('admin.transaksi', ['tab' => 'setor']));

        $this->assertSame(5.75, (float) $plastik->fresh()->stok);
        $this->assertSame(4.00, (float) $kertas->fresh()->stok);
        $this->assertSame(5500.00, (float) $nasabah->fresh()->saldo);
        $this->assertSame('sebagian', $transaksi->fresh()->status);
        $this->assertSame(4500.00, (float) $transaksi->fresh()->total_nilai);
        $this->assertSame('approved', $approvedDetail->fresh()->status_item);
        $this->assertSame('rejected', $rejectedDetail->fresh()->status_item);

        $this->withSession(['admin_logged_in' => true, 'admin_id' => $admin->id_admin])
            ->post(route('admin.transaksi.setor.update', $transaksi->id_transaksi_setor), [
                'decisions' => [
                    $approvedDetail->id_detail_setor => 'approve',
                    $rejectedDetail->id_detail_setor => 'reject',
                ],
            ])
            ->assertSessionHas('flash_setor_detail', 'Transaksi setor sudah diproses.');

        $this->assertSame(5.75, (float) $plastik->fresh()->stok);
        $this->assertSame(4.00, (float) $kertas->fresh()->stok);
        $this->assertSame(5500.00, (float) $nasabah->fresh()->saldo);
    }

    private function createAdmin(array $overrides = []): AdminUser
    {
        return AdminUser::create([
            'user_name' => 'adminstock',
            'nama_lengkap' => 'Admin Stock',
            'email' => 'adminstock@example.test',
            'password' => password_hash('rahasia123', PASSWORD_BCRYPT),
            'role' => 'admin',
            'status' => 'aktif',
            'created_at' => now(),
            ...$overrides,
        ]);
    }

    private function createNasabah(array $overrides = []): Nasabah
    {
        return Nasabah::create([
            'nama_lengkap' => 'Nasabah Stock',
            'user_name' => 'nasabahstock',
            'email' => 'nasabahstock@example.test',
            'password' => password_hash('rahasia123', PASSWORD_BCRYPT),
            'status' => 'aktif',
            'saldo' => 0,
            'created_at' => now(),
            ...$overrides,
        ]);
    }

    private function createSampah(array $overrides = []): Sampah
    {
        return Sampah::create([
            'nama_jenis' => 'Sampah Test',
            'harga_per_kg' => 1000,
            'stok' => 0,
            'status' => 'aktif',
            ...$overrides,
        ]);
    }

    private function createTransaksiSetor(Nasabah $nasabah, array $overrides = []): TransaksiSetor
    {
        return TransaksiSetor::create([
            'id_nasabah' => $nasabah->id_nasabah,
            'total_nilai' => 0,
            'tanggal_setor' => now()->toDateString(),
            'status' => 'menunggu',
            ...$overrides,
        ]);
    }
}
