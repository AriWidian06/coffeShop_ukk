<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoUsage extends Model
{
    protected $table = 'promo_usages';

    protected $fillable = [
        'promo_kode_id',
        'meja_id',
        'transaksi_id',
        'session_id',
        'discount_amount',
        'status',
        'used_at',
        'released_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'used_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function promo()
    {
        return $this->belongsTo(PromoKode::class, 'promo_kode_id');
    }

    public function meja()
    {
        return $this->belongsTo(Meja::class);
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}