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
        'promo_kode_id',
        'diskon',
        'session_id',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'diskon' => 'decimal:2',
        'waktu_transaksi' => 'datetime',
        'sudah_dibalik' => 'boolean',
    ];

    public function promo()
    {
        return $this->belongsTo(PromoKode::class, 'promo_kode_id');
    }

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

    /**
     * Kembalikan stok produk ke semula.
     * Dipanggil saat transaksi dibatalkan/dihapus karena saat dibuat stok sudah dikurangi.
     */
    public function restoreStock(): void
    {
        $this->loadMissing('detail_transaksis');

        foreach ($this->detail_transaksis as $detail) {
            if ($detail->produk_id) {
                Produk::where('id', $detail->produk_id)->increment('stock', (int) $detail->QTY);
            }
        }
    }

    /**
     * Lepaskan pemakaian kode promo (kuota + kunci meja/sesi) saat pesanan dibatalkan.
     */
    public function releasePromoUsage(): void
    {
        $usage = PromoUsage::where('transaksi_id', $this->id)->first();

        if (! $usage) {
            return;
        }

        if ($usage->promo_kode_id) {
            PromoKode::where('id', $usage->promo_kode_id)
                ->where('kuota_terpakai', '>', 0)
                ->decrement('kuota_terpakai');
        }

        $usage->update([
            'status' => 'released',
            'released_at' => now(),
        ]);
    }

    /**
     * Kembalikan stok produk & lepaskan kuota promo dalam satu kali saja.
     *
     * Pemotongan stok terjadi saat pesanan dibuat. Sebaliknya stok dikembalikan
     * ketika pesanan dibatalkan (mis. notifikasi QRIS expired/deny/cancel) dan
     * ketika transaksi dihapus kasir. Kedua jalur tersebut dapat terjadi
     * berurutan pada transaksi yang sama sehingga stok sempat terbalik dua kali
     * dan jumlah stok produk naik melebihi jumlah sebenarnya.
     *
     * Kolom `sudah_dibalik` menjadi penanda tunggal supaya pembalikan bersifat
     * idempoten dan aman dipanggil dari mana saja.
     */
    public function reverseStockAndPromo(): void
    {
        if ($this->sudah_dibalik) {
            return;
        }

        $this->restoreStock();
        $this->releasePromoUsage();

        $this->forceFill(['sudah_dibalik' => true])->saveQuietly();
    }
}
