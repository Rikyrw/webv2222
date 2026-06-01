<?php

namespace Tests\Feature;

use App\Models\FotoSetor;
use App\Models\Nasabah;
use App\Models\Sampah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NasabahSetorPhotoStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_setor_photo_is_stored_as_public_file_path(): void
    {
        Storage::fake('public');

        $nasabah = Nasabah::create([
            'nama_lengkap' => 'Nasabah Foto',
            'user_name' => 'nasabahfoto',
            'email' => 'nasabahfoto@example.test',
            'password' => password_hash('rahasia123', PASSWORD_BCRYPT),
            'alamat' => 'Jl. Foto',
            'no_hp' => '081234567890',
            'status' => 'aktif',
            'saldo' => 0,
            'created_at' => now(),
        ]);

        $sampah = Sampah::create([
            'nama_jenis' => 'Plastik',
            'harga_per_kg' => 2500,
            'stok' => 0,
            'status' => 'aktif',
        ]);

        $photo = $this->validPngDataUrl();

        $this->withSession([
            'id_nasabah' => $nasabah->id_nasabah,
            'validated_waste_photos' => [
                hash('sha256', $photo) => [
                    'id_jenis' => $sampah->id_jenis_sampah,
                    'nama_jenis' => $sampah->nama_jenis,
                    'checked_at' => time(),
                ],
            ],
        ])->post(route('nasabah.setor.post'), [
            'submit_transaction' => '1',
            'waste_items' => [
                [
                    'id_jenis' => $sampah->id_jenis_sampah,
                    'berat' => '1.25',
                ],
            ],
            'waste_photos' => [
                $photo,
            ],
        ])->assertOk()
            ->assertSee('Transaksi setor sampah berhasil diajukan', false);

        $foto = FotoSetor::query()->firstOrFail();

        $this->assertStringStartsWith('storage/setor-sampah/'.$nasabah->id_nasabah.'/', $foto->foto_url);
        $this->assertFalse(str_starts_with($foto->foto_url, 'data:image/'));

        Storage::disk('public')->assertExists(substr($foto->foto_url, strlen('storage/')));
    }

    private function validPngDataUrl(): string
    {
        return 'data:image/png;base64,'.base64_encode(base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII='
        ));
    }
}
