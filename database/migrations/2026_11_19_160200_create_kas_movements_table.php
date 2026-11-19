<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kas_account_id')->constrained('kas_accounts');
            $table->date('tanggal');
            $table->enum('jenis', [
                'saldo_awal',
                'penjualan',
                'pengeluaran',
                'setoran',
                'tarik_tunai',
                'penyesuaian',
            ]);
            $table->string('reff_type')->nullable();
            $table->unsignedBigInteger('reff_id')->nullable();
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('kredit', 12, 2)->default(0);
            $table->decimal('saldo_sebelum', 12, 2)->default(0);
            $table->decimal('saldo_sesudah', 12, 2)->default(0);
            $table->string('keterangan');
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->timestamps();

            $table->index(['reff_type', 'reff_id']);
            $table->index(['kas_account_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_movements');
    }
};
