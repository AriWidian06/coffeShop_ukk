<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();
            $table->enum('kategori', ['bahan_baku', 'operasional', 'gaji', 'lainnya']);
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('deskripsi');
            $table->decimal('jumlah', 12, 2);
            $table->date('tanggal');
            $table->enum('metode', ['tunai', 'transfer'])->default('tunai');
            $table->foreignId('kas_account_id')->constrained('kas_accounts');
            $table->string('bukti')->nullable();
            $table->text('catatan')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan_approval')->nullable();

            $table->timestamps();
            $table->index(['status', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};
