<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailSetor extends Model
{
    use HasFactory;

    protected $table = 'detail_setor';
    protected $primaryKey = 'id_detail_setor';
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_transaksi_setor',
        'id_jenis',
        'berat_kg',
        'harga_kg',
        'subtotal'
    ];

    protected $casts = [
        'berat_kg' => 'decimal:2',
        'harga_kg' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function transaksiSetor(): BelongsTo
    {
        return $this->belongsTo(TransaksiSetor::class, 'id_transaksi_setor', 'id_transaksi_setor');
    }

    public function sampah(): BelongsTo
    {
        return $this->belongsTo(Sampah::class, 'id_jenis', 'id_jenis_sampah');
    }
}
