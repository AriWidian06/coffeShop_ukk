<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasAccount extends Model
{
    protected $fillable = [
        'nama',
        'kode',
        'keterangan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function movements()
    {
        return $this->hasMany(KasMovement::class);
    }

    public function closings()
    {
        return $this->hasMany(KasClosing::class);
    }

    public static function tunai(): self
    {
        return static::where('kode', 'KAS-TUNAI')->firstOrFail();
    }

    public static function bank(): self
    {
        return static::where('kode', 'BANK-QRIS')->firstOrFail();
    }
}
