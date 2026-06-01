<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('jenis_sampah') || ! Schema::hasColumn('jenis_sampah', 'stok')) {
            return;
        }

        Schema::table('jenis_sampah', function (Blueprint $table): void {
            $table->decimal('stok', 10, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('jenis_sampah') || ! Schema::hasColumn('jenis_sampah', 'stok')) {
            return;
        }

        Schema::table('jenis_sampah', function (Blueprint $table): void {
            $table->integer('stok')->default(0)->change();
        });
    }
};
