<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique();
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        DB::table('kas_accounts')->insert([
            [
                'nama' => 'Kas Tunai',
                'kode' => 'KAS-TUNAI',
                'keterangan' => 'Uang tunai di laci kasir.',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Bank / QRIS',
                'kode' => 'BANK-QRIS',
                'keterangan' => 'Saldo transfer dan pembayaran QRIS.',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_accounts');
    }
};
