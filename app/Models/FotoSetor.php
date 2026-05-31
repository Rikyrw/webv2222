<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoSetor extends Model
{
    use HasFactory;

    protected $table = 'foto_setor';
    protected $primaryKey = 'id_foto_setor';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_transaksi_setor',
        'id_detail_setor',
        'id_jenis',
        'foto_url',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function transaksiSetor(): BelongsTo
    {
        return $this->belongsTo(TransaksiSetor::class, 'id_transaksi_setor', 'id_transaksi_setor');
    }

    public function detailSetor(): BelongsTo
    {
        return $this->belongsTo(DetailSetor::class, 'id_detail_setor', 'id_detail_setor');
    }

    public function sampah(): BelongsTo
    {
        return $this->belongsTo(Sampah::class, 'id_jenis', 'id_jenis_sampah');
    }
}
