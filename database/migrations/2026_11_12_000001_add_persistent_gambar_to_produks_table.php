<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->boolean('has_gambar_data')->default(false)->after('gambar');
        });

        Schema::create('produk_gambars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->unique()->constrained('produks')->cascadeOnDelete();
            $table->string('mime')->default('image/jpeg');
            $table->longText('data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_gambars');

        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn('has_gambar_data');
        });
    }
};
