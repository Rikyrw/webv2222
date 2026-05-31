<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopupSaldo extends Model
{
    use HasFactory;

    protected $table = 'topup_saldo';
    protected $primaryKey = 'id_topup';
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_nasabah',
        'order_id',
        'gross_amount',
        'status',
        'transaction_status',
        'payment_type',
        'transaction_id',
        'fraud_status',
        'snap_token',
        'snap_redirect_url',
        'raw_notification',
        'transaction_time',
        'created_at',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'raw_notification' => 'array',
        'transaction_time' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function nasabah(): BelongsTo
    {
        return $this->belongsTo(Nasabah::class, 'id_nasabah', 'id_nasabah');
    }
}
