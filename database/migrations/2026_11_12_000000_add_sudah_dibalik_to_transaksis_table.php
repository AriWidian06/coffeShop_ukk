<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah penanda pembalikan (reversal) pada transaksi.
     *
     * Stok produk & kuota promo dikurangi saat pesanan dibuat, lalu dikembalikan
     * saat pesanan dibatalkan (mis. QRIS expired) ATAU saat transaksi dihapus.
     * Karena kedua jalur itu bisa terjadi berurutan, stok sempat terbalik dua
     * kali sehingga jumlah stok produk naik melebihi jumlah sebenarnya
     * (silent data corruption).
     *
     * Kolom ini menjadi sumber kebenaran "sudah dibalik atau belum" sehingga
     * pembalikan hanya bisa terjadi satu kali per transaksi.
     */
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->boolean('sudah_dibalik')->default(false)->after('status_pesanan');
        });

        // Transaksi yang sudah berstatus 'cancelled' berarti stok & promo-nya
        // sudah dikembalikan sebelumnya, tandai agar tidak dibalik lagi.
        DB::table('transaksis')
            ->where('status_pesanan', 'cancelled')
            ->update(['sudah_dibalik' => true]);
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn('sudah_dibalik');
        });
    }
};
