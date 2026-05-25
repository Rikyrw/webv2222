<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nasabah extends Model
{
    use HasFactory;

    protected $table = 'nasabah';

    protected $primaryKey = 'id_nasabah';

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'user_name',
        'nama_lengkap',
        'email',
        'password',
        'no_hp',
        'status',
        'saldo',
        'alamat',
        'created_at',
        'email_verified_at',
        'email_verification_token_hash',
        'email_verification_expires_at',
        'email_verification_sent_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'created_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'email_verification_expires_at' => 'datetime',
        'email_verification_sent_at' => 'datetime',
    ];

    public function transaksiSetor(): HasMany
    {
        return $this->hasMany(TransaksiSetor::class, 'id_nasabah', 'id_nasabah');
    }

    public function transaksiPenarikan(): HasMany
    {
        return $this->hasMany(TransaksiPenarikan::class, 'id_nasabah', 'id_nasabah');
    }
}
