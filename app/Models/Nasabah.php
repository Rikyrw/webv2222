<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Nasabah extends Authenticatable
{
    use HasApiTokens, HasFactory;

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
        'google_sub',
        'google_id',
        'photo_url',
        'provider',
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

    public function topups(): HasMany
    {
        return $this->hasMany(TopupSaldo::class, 'id_nasabah', 'id_nasabah');
    }
}
