<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = [
        'supplier_id',
        'kategori_produk_id',
        'nama_produk',
        'deskripsi',
        'harga_jual',
        'harga_beli',
        'tipe',
        'satuan',
        'stock',
        'status_aktif',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'harga_beli' => 'decimal:2',
        'stock' => 'integer',
        'status_aktif' => 'boolean',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function kategoriProduk()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }

    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function transaksis()
    {
        return $this->belongsToMany(Transaksi::class, 'detail_transaksis', 'produk_id', 'transaksi_id')
            ->withPivot('QTY', 'subtotal')
            ->withTimestamps();
    }
}
