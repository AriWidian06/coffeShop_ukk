<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_transaksis', function (Blueprint $table) {
            $table->string('customization', 250)->nullable()->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('detail_transaksis', function (Blueprint $table) {
            $table->dropColumn('customization');
        });
    }
};