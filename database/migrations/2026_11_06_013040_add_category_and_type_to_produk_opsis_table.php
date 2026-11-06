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
        Schema::table('produk_opsis', function (Blueprint $table) {
            $table->string('kategori')->nullable()->after('produk_id');
            $table->enum('tipe', ['single', 'multiple'])->default('multiple')->after('kategori');
            $table->boolean('is_required')->default(false)->after('tipe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_opsis', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'tipe', 'is_required']);
        });
    }
};
