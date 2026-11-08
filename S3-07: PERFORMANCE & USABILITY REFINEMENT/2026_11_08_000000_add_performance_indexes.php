<?php

/**
 * Excerpt dari: database/migrations/...
 * Fokus: index pada kolom kunci transaction, detail, product, dan payment.
 *
 * Adaptasi untuk schema Bagi Kopi:
 * - reservations(room_id, status, check_in, check_out) => transaksis(meja_id, status_pesanan, waktu_transaksi)
 * - reservations(user_id) => transaksis(karyawan_id)
 * - rooms(price_per_night) => produks(status_aktif, stock)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->index(['karyawan_id', 'status_pesanan', 'waktu_transaksi'], 'idx_transaksis_karyawan_status_waktu');
            $table->index(['meja_id', 'status_pesanan', 'waktu_transaksi'], 'idx_transaksis_meja_status_waktu');
            $table->index(['status_pesanan', 'waktu_transaksi'], 'idx_transaksis_status_waktu');
            $table->index('sumber_pesanan', 'idx_transaksis_sumber');
        });

        Schema::table('detail_transaksis', function (Blueprint $table) {
            $table->index('transaksi_id', 'idx_detail_transaksis_transaksi');
            $table->index('produk_id', 'idx_detail_transaksis_produk');
        });

        Schema::table('produks', function (Blueprint $table) {
            $table->index(['kategori_produk_id', 'status_aktif'], 'idx_produks_kategori_status');
            $table->index(['status_aktif', 'stock'], 'idx_produks_status_stock');
        });

        Schema::table('produk_opsis', function (Blueprint $table) {
            $table->index('produk_id', 'idx_produk_opsis_produk');
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->index(['metode_pembayaran', 'status_pembayaran'], 'idx_pembayarans_metode_status');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropIndex('idx_transaksis_karyawan_status_waktu');
            $table->dropIndex('idx_transaksis_meja_status_waktu');
            $table->dropIndex('idx_transaksis_status_waktu');
            $table->dropIndex('idx_transaksis_sumber');
        });

        Schema::table('detail_transaksis', function (Blueprint $table) {
            $table->dropIndex('idx_detail_transaksis_transaksi');
            $table->dropIndex('idx_detail_transaksis_produk');
        });

        Schema::table('produks', function (Blueprint $table) {
            $table->dropIndex('idx_produks_kategori_status');
            $table->dropIndex('idx_produks_status_stock');
        });

        Schema::table('produk_opsis', function (Blueprint $table) {
            $table->dropIndex('idx_produk_opsis_produk');
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropIndex('idx_pembayarans_metode_status');
        });
    }
};
```