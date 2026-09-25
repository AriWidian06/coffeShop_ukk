<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table ->foreignId('meja_id')->constrained('mejas')->onDelete('cascade');
            $table ->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table ->ENUM('tipe_pesanan', ['dine-in','take-away'])->default('dine-in');
            $table->decimal('total_harga', 10, 2);
            $table->enum('status_pesanan', ['pending', 'ready', 'prosesed','completed'])->default('pending');
            $table->timestamp('waktu_transaksi')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
