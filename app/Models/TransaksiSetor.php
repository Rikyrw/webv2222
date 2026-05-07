<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiSetor extends Model
{
    use HasFactory;

    protected $table = 'transaksi_setor';
    protected $primaryKey = 'id_transaksi_setor';
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_nasabah',
        'total_nilai',
        'tanggal_setor',
        'tanggal_proses',
        'status'
    ];

    protected $casts = [
        'total_nilai' => 'decimal:2',
        'tanggal_setor' => 'date',
        'tanggal_proses' => 'date'
    ];

    public function nasabah(): BelongsTo
    {
        return $this->belongsTo(Nasabah::class, 'id_nasabah', 'id_nasabah');
    }

    public function detailSetor(): HasMany
    {
        return $this->hasMany(DetailSetor::class, 'id_transaksi_setor', 'id_transaksi_setor');
    }
}
