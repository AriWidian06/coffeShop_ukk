<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->foreignId('meja_id')->nullable()->change();
            $table->foreignId('karyawan_id')->nullable()->change();
            $table->string('sumber_pesanan', 20)->default('kasir')->after('karyawan_id');
            $table->text('catatan')->nullable()->after('sumber_pesanan');
        });
    }

    public function down(): void
    {
        $hasUnassignedTransactions = DB::table('transaksis')
            ->whereNull('meja_id')
            ->orWhereNull('karyawan_id')
            ->exists();

        if ($hasUnassignedTransactions) {
            throw new RuntimeException('Web transactions without a table or cashier must be handled before rolling back this migration.');
        }

        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn(['sumber_pesanan', 'catatan']);
            $table->foreignId('meja_id')->nullable(false)->change();
            $table->foreignId('karyawan_id')->nullable(false)->change();
        });
    }
};