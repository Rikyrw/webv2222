<?php

namespace Tests\Feature;

use App\Models\Nasabah;
use App\Models\TransaksiSetor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminNasabahDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_delete_deactivates_nasabah_that_has_transaction_history(): void
    {
        $nasabah = $this->createNasabah();
        $nasabah->createToken('phone');

        TransaksiSetor::create([
            'id_nasabah' => $nasabah->id_nasabah,
            'total_nilai' => 10000,
            'tanggal_setor' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $this->withSession(['admin_logged_in' => true])
            ->delete(route('admin.nasabah.delete', $nasabah->id_nasabah))
            ->assertRedirect(route('admin.nasabah.daftar'))
            ->assertSessionHas('flash_nasabah_type', 'warning');

        $this->assertDatabaseHas('nasabah', [
            'id_nasabah' => $nasabah->id_nasabah,
            'status' => 'nonaktif',
        ]);
        $this->assertSame(0, DB::table('personal_access_tokens')->where('tokenable_id', $nasabah->id_nasabah)->count());
    }

    public function test_admin_delete_removes_nasabah_without_history(): void
    {
        $nasabah = $this->createNasabah([
            'email' => 'empty@example.test',
            'user_name' => 'emptyhistory',
        ]);
        $nasabah->createToken('phone');

        $this->withSession(['admin_logged_in' => true])
            ->delete(route('admin.nasabah.delete', $nasabah->id_nasabah))
            ->assertRedirect(route('admin.nasabah.daftar'))
            ->assertSessionHas('flash_nasabah_type', 'success');

        $this->assertDatabaseMissing('nasabah', [
            'id_nasabah' => $nasabah->id_nasabah,
        ]);
        $this->assertSame(0, DB::table('personal_access_tokens')->where('tokenable_id', $nasabah->id_nasabah)->count());
    }

    public function test_admin_nasabah_list_hides_delete_button_for_nasabah_with_history(): void
    {
        $withHistory = $this->createNasabah();
        $withoutHistory = $this->createNasabah([
            'email' => 'empty@example.test',
            'user_name' => 'emptyhistory',
            'created_at' => now()->addMinute(),
        ]);

        TransaksiSetor::create([
            'id_nasabah' => $withHistory->id_nasabah,
            'total_nilai' => 10000,
            'tanggal_setor' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.nasabah.daftar'))
            ->assertOk();

        $response->assertSee('action="'.route('admin.nasabah.delete', $withoutHistory->id_nasabah).'"', false);
        $response->assertDontSee('action="'.route('admin.nasabah.delete', $withHistory->id_nasabah).'"', false);
    }

    public function test_admin_nasabah_list_can_be_filtered_by_registration_date(): void
    {
        $this->createNasabah([
            'email' => 'inside@example.test',
            'user_name' => 'insideuser',
            'created_at' => '2026-05-10 09:00:00',
        ]);
        $this->createNasabah([
            'email' => 'outside@example.test',
            'user_name' => 'outsideuser',
            'created_at' => '2026-05-12 09:00:00',
        ]);

        $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.nasabah.daftar', [
                'tanggal_daftar' => '2026-05-10',
            ]))
            ->assertOk()
            ->assertSee('inside@example.test')
            ->assertDontSee('outside@example.test')
            ->assertSee('value="2026-05-10"', false);
    }

    private function createNasabah(array $overrides = []): Nasabah
    {
        return Nasabah::create([
            'nama_lengkap' => 'Nasabah Delete',
            'user_name' => 'nasabahdelete',
            'email' => 'delete@example.test',
            'password' => password_hash('rahasia123', PASSWORD_BCRYPT),
            'alamat' => 'Jalan Delete',
            'no_hp' => '08123456789',
            'status' => 'aktif',
            'saldo' => 0,
            'created_at' => now(),
            ...$overrides,
        ]);
    }
}
