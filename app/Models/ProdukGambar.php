<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukGambar extends Model
{
    protected $table = 'produk_gambars';

    protected $fillable = [
        'produk_id',
        'mime',
        'data',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
