<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sampah extends Model
{
    use HasFactory;

    protected $table = 'jenis_sampah';
    protected $primaryKey = 'id_jenis_sampah';
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'nama_jenis',
        'harga_per_kg',
        'stok',
        'status'
    ];

    protected $casts = [
        'harga_per_kg' => 'decimal:2',
        'stok' => 'integer'
    ];

    public function detailSetor(): HasMany
    {
        return $this->hasMany(DetailSetor::class, 'id_jenis', 'id_jenis_sampah');
    }
}
