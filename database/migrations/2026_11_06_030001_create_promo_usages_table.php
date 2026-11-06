<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_kode_id')->constrained('promo_kodes')->cascadeOnDelete();
            $table->foreignId('meja_id')->nullable()->constrained('mejas')->nullOnDelete();
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksis')->nullOnDelete();
            $table->string('session_id', 128)->nullable();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->string('status', 20)->default('active');
            $table->timestamp('used_at');
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->foreignId('promo_kode_id')->nullable()->constrained('promo_kodes')->nullOnDelete();
            $table->decimal('diskon', 12, 2)->default(0);
            $table->string('session_id', 128)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('promo_kode_id');
            $table->dropColumn(['diskon', 'session_id']);
        });
        Schema::dropIfExists('promo_usages');
    }
};