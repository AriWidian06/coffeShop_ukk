<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mejas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nomor_meja')->unique();
            $table->unsignedInteger('kapasitas');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mejas');
    }
};