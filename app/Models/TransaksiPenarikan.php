<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPenarikan extends Model
{
    use HasFactory;

    protected $table = 'penarikan_saldo';
    protected $primaryKey = 'id_penarikan';
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_nasabah',
        'id_admin',
        'jenis_penukaran',
        'nominal',
        'status',
        'tanggal_pengajuan',
        'tanggal_proses',
        'deskripsi'
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_pengajuan' => 'date',
        'tanggal_proses' => 'date'
    ];

    public function nasabah(): BelongsTo
    {
        return $this->belongsTo(Nasabah::class, 'id_nasabah', 'id_nasabah');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'id_admin', 'id_admin');
    }
}
