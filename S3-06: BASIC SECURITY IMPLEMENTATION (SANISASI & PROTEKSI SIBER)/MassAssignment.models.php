<?php

/**
 * Excerpt dari: app/Models/Produk.php dan app/Models/Transaksi.php
 * Fokus: mass assignment aman dengan Fillable.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = [
        'supplier_id',
        'kategori_produk_id',
        'nama_produk',
        'deskripsi',
        'gambar',
        'harga_jual',
        'harga_beli',
        'tipe',
        'satuan',
        'stock',
        'status_aktif',
        'is_customizable',
    ];
}

class Transaksi extends Model
{
    protected $fillable = [
        'meja_id',
        'karyawan_id',
        'sumber_pesanan',
        'catatan',
        'tipe_pesanan',
        'total_harga',
        'status_pesanan',
        'waktu_transaksi',
        'promo_kode_id',
        'diskon',
        'session_id',
    ];
}

// Contoh aman:
// $produk = Produk::create($validated);
// $produk->update($validated);

// POLA BERBAHAYA (JANGAN DIPAKAI):
// $produk = Produk::create($request->all());
```