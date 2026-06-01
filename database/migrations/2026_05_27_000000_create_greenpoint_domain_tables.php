<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->upgradeExistingTables();

        if (! Schema::hasTable('admin')) {
            Schema::create('admin', function (Blueprint $table) {
            $table->id('id_admin');
            $table->string('user_name', 100)->unique();
            $table->string('nama_lengkap', 150);
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->string('salt')->nullable();
            $table->enum('role', ['operator', 'admin', 'superadmin'])->default('admin');
            $table->string('no_hp', 30)->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->string('alamat')->nullable();
            $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('nasabah')) {
            Schema::create('nasabah', function (Blueprint $table) {
            $table->id('id_nasabah');
            $table->string('user_name', 100)->nullable()->unique();
            $table->string('nama_lengkap', 150);
            $table->string('email', 150)->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('salt')->nullable();
            $table->string('no_hp', 30)->nullable();
            $table->enum('status', ['aktif', 'menunggu', 'nonaktif'])->default('aktif');
            $table->decimal('saldo', 14, 2)->default(0);
            $table->string('alamat')->nullable();
            $table->string('google_sub')->nullable()->unique();
            $table->string('google_id')->nullable();
            $table->string('photo_url', 2048)->nullable();
            $table->string('provider', 50)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->text('email_verification_token_hash')->nullable();
            $table->timestamp('email_verification_expires_at')->nullable();
            $table->timestamp('email_verification_sent_at')->nullable();
            });
        }

        if (! Schema::hasTable('jenis_sampah')) {
            Schema::create('jenis_sampah', function (Blueprint $table) {
            $table->id('id_jenis_sampah');
            $table->foreignId('id_admin')->nullable()
                ->constrained('admin', 'id_admin')
                ->nullOnDelete();
            $table->string('nama_jenis', 100);
            $table->decimal('harga_per_kg', 14, 2)->default(0);
            $table->decimal('stok', 10, 2)->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            });
        }

        if (! Schema::hasTable('transaksi_setor')) {
            Schema::create('transaksi_setor', function (Blueprint $table) {
            $table->id('id_transaksi_setor');
            $table->foreignId('id_nasabah')->constrained('nasabah', 'id_nasabah')->cascadeOnDelete();
            $table->foreignId('id_admin')->nullable()
                ->constrained('admin', 'id_admin')
                ->nullOnDelete();
            $table->decimal('total_nilai', 14, 2)->default(0);
            $table->date('tanggal_setor')->nullable();
            $table->date('tanggal_proses')->nullable();
            $table->string('status', 30)->default('menunggu');
            });
        }

        if (! Schema::hasTable('detail_setor')) {
            Schema::create('detail_setor', function (Blueprint $table) {
            $table->id('id_detail_setor');
            $table->foreignId('id_transaksi_setor')
                ->constrained('transaksi_setor', 'id_transaksi_setor')
                ->cascadeOnDelete();
            $table->foreignId('id_jenis')
                ->constrained('jenis_sampah', 'id_jenis_sampah')
                ->restrictOnDelete();
            $table->decimal('berat_kg', 10, 2)->default(0);
            $table->decimal('harga_kg', 14, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->string('status_item', 30)->default('pending');
            $table->text('catatan_admin')->nullable();
            });
        }

        if (! Schema::hasTable('foto_setor')) {
            Schema::create('foto_setor', function (Blueprint $table) {
            $table->id('id_foto_setor');
            $table->foreignId('id_transaksi_setor')
                ->constrained('transaksi_setor', 'id_transaksi_setor')
                ->cascadeOnDelete();
            $table->foreignId('id_detail_setor')->nullable()
                ->constrained('detail_setor', 'id_detail_setor')
                ->cascadeOnDelete();
            $table->foreignId('id_jenis')->nullable()
                ->constrained('jenis_sampah', 'id_jenis_sampah')
                ->nullOnDelete();
            $table->text('foto_url');
            $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('penarikan_saldo')) {
            Schema::create('penarikan_saldo', function (Blueprint $table) {
            $table->id('id_penarikan');
            $table->foreignId('id_nasabah')->constrained('nasabah', 'id_nasabah')->cascadeOnDelete();
            $table->foreignId('id_admin')->nullable()
                ->constrained('admin', 'id_admin')
                ->nullOnDelete();
            $table->string('jenis_penukaran', 50)->default('Penarikan');
            $table->decimal('nominal', 14, 2)->default(0);
            $table->string('status', 30)->default('menunggu');
            $table->date('tanggal_pengajuan')->nullable();
            $table->date('tanggal_proses')->nullable();
            $table->text('deskripsi')->nullable();
            });
        }

        if (! Schema::hasTable('topup_saldo')) {
            Schema::create('topup_saldo', function (Blueprint $table) {
            $table->id('id_topup');
            $table->foreignId('id_nasabah')->constrained('nasabah', 'id_nasabah')->cascadeOnDelete();
            $table->string('order_id')->unique();
            $table->decimal('gross_amount', 14, 2)->default(0);
            $table->string('status', 50)->default('pending');
            $table->string('transaction_status', 50)->default('pending');
            $table->string('payment_type', 80)->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('fraud_status')->nullable();
            $table->text('snap_token')->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->json('raw_notification')->nullable();
            $table->timestamp('transaction_time')->nullable();
            $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('topup_saldo');
        Schema::dropIfExists('penarikan_saldo');
        Schema::dropIfExists('foto_setor');
        Schema::dropIfExists('detail_setor');
        Schema::dropIfExists('transaksi_setor');
        Schema::dropIfExists('jenis_sampah');
        Schema::dropIfExists('nasabah');
        Schema::dropIfExists('admin');
    }

    private function upgradeExistingTables(): void
    {
        if (Schema::hasTable('nasabah')) {
            $this->addColumnIfMissing('nasabah', 'google_sub', fn (Blueprint $table) => $table->string('google_sub')->nullable());
            $this->addColumnIfMissing('nasabah', 'google_id', fn (Blueprint $table) => $table->string('google_id')->nullable());
            $this->addColumnIfMissing('nasabah', 'photo_url', fn (Blueprint $table) => $table->string('photo_url', 2048)->nullable());
            $this->addColumnIfMissing('nasabah', 'provider', fn (Blueprint $table) => $table->string('provider', 50)->nullable());
            $this->addColumnIfMissing('nasabah', 'email_verified_at', fn (Blueprint $table) => $table->timestamp('email_verified_at')->nullable());
            $this->addColumnIfMissing('nasabah', 'email_verification_token_hash', fn (Blueprint $table) => $table->text('email_verification_token_hash')->nullable());
            $this->addColumnIfMissing('nasabah', 'email_verification_expires_at', fn (Blueprint $table) => $table->timestamp('email_verification_expires_at')->nullable());
            $this->addColumnIfMissing('nasabah', 'email_verification_sent_at', fn (Blueprint $table) => $table->timestamp('email_verification_sent_at')->nullable());
        }

        if (Schema::hasTable('transaksi_setor')) {
            $this->addColumnIfMissing('transaksi_setor', 'id_admin', fn (Blueprint $table) => $table->unsignedBigInteger('id_admin')->nullable());
        }

        if (Schema::hasTable('detail_setor')) {
            $this->addColumnIfMissing('detail_setor', 'status_item', fn (Blueprint $table) => $table->string('status_item', 30)->default('pending'));
            $this->addColumnIfMissing('detail_setor', 'catatan_admin', fn (Blueprint $table) => $table->text('catatan_admin')->nullable());
        }

        if (Schema::hasTable('foto_setor')) {
            $this->addColumnIfMissing('foto_setor', 'id_detail_setor', fn (Blueprint $table) => $table->unsignedBigInteger('id_detail_setor')->nullable());
            $this->addColumnIfMissing('foto_setor', 'id_jenis', fn (Blueprint $table) => $table->unsignedBigInteger('id_jenis')->nullable());
            $this->addColumnIfMissing('foto_setor', 'created_at', fn (Blueprint $table) => $table->timestamp('created_at')->nullable());
        }

        if (Schema::hasTable('penarikan_saldo')) {
            $this->addColumnIfMissing('penarikan_saldo', 'id_admin', fn (Blueprint $table) => $table->unsignedBigInteger('id_admin')->nullable());
        }

        if (Schema::hasTable('topup_saldo')) {
            $this->addColumnIfMissing('topup_saldo', 'payment_type', fn (Blueprint $table) => $table->string('payment_type', 80)->nullable());
            $this->addColumnIfMissing('topup_saldo', 'transaction_id', fn (Blueprint $table) => $table->string('transaction_id')->nullable());
            $this->addColumnIfMissing('topup_saldo', 'fraud_status', fn (Blueprint $table) => $table->string('fraud_status')->nullable());
            $this->addColumnIfMissing('topup_saldo', 'snap_token', fn (Blueprint $table) => $table->text('snap_token')->nullable());
            $this->addColumnIfMissing('topup_saldo', 'snap_redirect_url', fn (Blueprint $table) => $table->text('snap_redirect_url')->nullable());
            $this->addColumnIfMissing('topup_saldo', 'raw_notification', fn (Blueprint $table) => $table->json('raw_notification')->nullable());
            $this->addColumnIfMissing('topup_saldo', 'transaction_time', fn (Blueprint $table) => $table->timestamp('transaction_time')->nullable());
        }
    }

    private function addColumnIfMissing(string $tableName, string $columnName, callable $callback): void
    {
        if (Schema::hasColumn($tableName, $columnName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($callback): void {
            $callback($table);
        });
    }
};
