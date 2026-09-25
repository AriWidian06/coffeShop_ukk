<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('kategori_produk_id')->constrained('kategori_produks')->onDelete('cascade');
            $table->string('nama_produk');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_jual', 10, 2);
            $table->decimal('harga_beli', 10, 2);
            $table->enum('tipe', ['jual', 'bahan baku']);
            $table->string('satuan')->default('pcs');
            $table->integer('stock')->default(0);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
