<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProdukOpsi extends Model
{
    protected $fillable = ['produk_id', 'nama_opsi', 'harga_tambahan'];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}
