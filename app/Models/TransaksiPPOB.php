<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPPOB extends Model
{
    use HasFactory;

    protected $table = 'transaksi_ppob';

    protected $fillable = [
        'nasabah_id',
        'tipe_ppob',
        'provider',
        'nomor_tujuan',
        'nominal',
        'status',
        'ref_id',
        'catatan'
    ];

    protected $casts = [
        'nominal' => 'decimal:2'
    ];

    public function nasabah(): BelongsTo
    {
        return $this->belongsTo(Nasabah::class);
    }
}
