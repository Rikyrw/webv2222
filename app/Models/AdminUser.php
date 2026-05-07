<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    use HasFactory;

    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'user_name',
        'nama_lengkap',
        'email',
        'password',
        'role',
        'no_hp',
        'status',
        'alamat',
        'created_at'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function transaksiSetor(): HasMany
    {
        return $this->hasMany(TransaksiSetor::class, 'id_admin', 'id_admin');
    }

    public function transaksiPenarikan(): HasMany
    {
        return $this->hasMany(TransaksiPenarikan::class, 'id_admin', 'id_admin');
    }
}
