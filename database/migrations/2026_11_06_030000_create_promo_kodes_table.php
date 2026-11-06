<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_kodes', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama_promo')->nullable();
            $table->decimal('nilai_diskon', 5, 2);
            $table->decimal('min_belanja', 12, 2)->default(0);
            $table->decimal('maks_diskon', 12, 2)->nullable();
            $table->dateTime('tanggal_mulai')->nullable();
            $table->dateTime('tanggal_berakhir')->nullable();
            $table->unsignedInteger('kuota')->nullable();
            $table->unsignedInteger('kuota_terpakai')->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_kodes');
    }
};