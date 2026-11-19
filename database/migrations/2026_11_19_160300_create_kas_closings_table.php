<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_closings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kas_account_id')->constrained('kas_accounts');
            $table->date('tanggal');
            $table->decimal('saldo_awal', 12, 2)->default(0);
            $table->decimal('total_masuk', 12, 2)->default(0);
            $table->decimal('total_keluar', 12, 2)->default(0);
            $table->decimal('saldo_buku', 12, 2)->default(0);
            $table->decimal('saldo_fisik', 12, 2)->default(0);
            $table->decimal('selisih', 12, 2)->default(0);
            $table->enum('status', ['draft', 'closed'])->default('draft');
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['kas_account_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_closings');
    }
};
