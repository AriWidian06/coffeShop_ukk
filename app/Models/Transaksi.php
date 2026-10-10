<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'meja_id',
        'karyawan_id',
        'sumber_pesanan',
        'catatan',
        'tipe_pesanan',
        'total_harga',
        'status_pesanan',
        'waktu_transaksi',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'waktu_transaksi' => 'datetime',
    ];

    public function meja()
    {
        return $this->belongsTo(Meja::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function detail_transaksis()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function produks()
    {
        return $this->belongsToMany(Produk::class, 'detail_transaksis', 'transaksi_id', 'produk_id')
            ->withPivot('QTY', 'subtotal')
            ->withTimestamps();
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}
