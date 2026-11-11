<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan status 'cancelled' agar pesanan QRIS yang kedaluwarsa/ditolak
     * bisa ditandai batal (bukan tetap 'pending' selamanya).
     */
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->enum('status_pesanan', ['pending', 'ready', 'prosesed', 'completed', 'cancelled'])
                ->default('pending')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->enum('status_pesanan', ['pending', 'ready', 'prosesed', 'completed'])
                ->default('pending')
                ->change();
        });
    }
};
