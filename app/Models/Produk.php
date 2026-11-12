<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Produk extends Model
{
    protected $fillable = [
        'supplier_id',
        'kategori_produk_id',
        'nama_produk',
        'deskripsi',
        'gambar',
        'has_gambar_data',
        'harga_jual',
        'harga_beli',
        'tipe',
        'satuan',
        'stock',
        'status_aktif',
        'is_customizable',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'harga_beli' => 'decimal:2',
        'stock' => 'integer',
        'status_aktif' => 'boolean',
        'is_customizable' => 'boolean',
        'has_gambar_data' => 'boolean',
    ];

    /**
     * URL gambar produk.
     *
     * File lokal dipakai lebih dulu agar penyajian tetap cepat. Jika file
     * sudah hilang (misalnya container Render restart), gambar diambil dari
     * data yang tersimpan di database.
     */
    public function getGambarUrlAttribute(): ?string
    {
        if ($this->gambar && Storage::disk('public')->exists($this->gambar)) {
            return asset('storage/'.$this->gambar);
        }

        if ($this->has_gambar_data) {
            return route('produks.gambar', [
                'produk' => $this,
                'v' => $this->updated_at?->timestamp,
            ]);
        }

        return null;
    }

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

    public function opsis()
    {
        return $this->hasMany(ProdukOpsi::class);
    }

    public function gambarData()
    {
        return $this->hasOne(ProdukGambar::class);
    }
}
