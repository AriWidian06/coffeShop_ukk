<?php

use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\V1\AuthController as V1AuthController;
use App\Http\Controllers\Api\V1\DashboardController as V1DashboardController;
use App\Http\Controllers\Api\V1\KaryawanController as V1KaryawanController;
use App\Http\Controllers\Api\V1\KategoriProdukController as V1KategoriProdukController;
use App\Http\Controllers\Api\V1\LaporanController as V1LaporanController;
use App\Http\Controllers\Api\V1\MejaController as V1MejaController;
use App\Http\Controllers\Api\V1\ProdukController as V1ProdukController;
use App\Http\Controllers\Api\V1\PromoKodeController as V1PromoKodeController;
use App\Http\Controllers\Api\V1\SupplierController as V1SupplierController;
use App\Http\Controllers\Api\V1\TransaksiController as V1TransaksiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Pelanggan (tanpa autentikasi, throttled)
|--------------------------------------------------------------------------
*/
Route::middleware('throttle:api')->group(function () {
    Route::post('/transaksis', [TransaksiController::class, 'store']);
    Route::get('/pesanan-saya', [TransaksiController::class, 'myOrder']);
    Route::post('/transaksis/{id}/check-status', [TransaksiController::class, 'checkStatus']);
    Route::post('/promo/validate', [PromoController::class, 'validatePromo']);
});

/*
|--------------------------------------------------------------------------
| API Staf v1 (Sanctum token)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('auth/login', [V1AuthController::class, 'login'])->middleware('throttle:login');

    Route::middleware(['auth:sanctum', 'throttle:api.staff'])->group(function () {
        Route::get('auth/me', [V1AuthController::class, 'me']);
        Route::post('auth/logout', [V1AuthController::class, 'logout']);

        // Katalog: semua staf boleh membaca untuk kebutuhan POS & dashboard.
        Route::get('produks', [V1ProdukController::class, 'index']);
        Route::get('produks/{produk}', [V1ProdukController::class, 'show']);
        Route::get('kategori-produks', [V1KategoriProdukController::class, 'index']);
        Route::get('kategori-produks/{kategoriProduk}', [V1KategoriProdukController::class, 'show']);
        Route::get('mejas/status', [V1MejaController::class, 'status']);
        Route::get('mejas', [V1MejaController::class, 'index']);
        Route::get('mejas/{meja}', [V1MejaController::class, 'show']);

        // Transaksi: kasir dibatasi oleh policy (hanya miliknya / pesanan web).
        Route::get('transaksis', [V1TransaksiController::class, 'index']);
        Route::get('transaksis/{transaksi}', [V1TransaksiController::class, 'show']);
        Route::get('transaksis/{transaksi}/receipt', [V1TransaksiController::class, 'receipt']);

        // Dashboard & laporan.
        Route::get('dashboard/summary', [V1DashboardController::class, 'summary']);
        Route::get('dashboard/sales-chart', [V1DashboardController::class, 'salesChart']);

        Route::middleware('api.role:admin,manager')->group(function () {
            Route::get('laporan/summary', [V1LaporanController::class, 'summary']);
        });

        // Operasional kasir/admin.
        Route::middleware('api.role:admin,kasir')->group(function () {
            Route::get('pos/active-orders', [V1TransaksiController::class, 'activeOrders']);
            Route::post('transaksis', [V1TransaksiController::class, 'store']);
            Route::patch('transaksis/{transaksi}/status', [V1TransaksiController::class, 'updateStatus']);
            Route::delete('transaksis/{transaksi}', [V1TransaksiController::class, 'destroy']);
            Route::post('transaksis/{transaksi}/payment', [V1TransaksiController::class, 'payment']);
            Route::post('transaksis/{transaksi}/qris/charge', [V1TransaksiController::class, 'qrisCharge']);
            Route::post('transaksis/{transaksi}/qris/check', [V1TransaksiController::class, 'qrisCheck']);
        });

        // Master data & akun: hanya admin.
        Route::middleware('api.role:admin')->group(function () {
            Route::post('produks', [V1ProdukController::class, 'store']);
            Route::match(['put', 'patch'], 'produks/{produk}', [V1ProdukController::class, 'update']);
            Route::delete('produks/{produk}', [V1ProdukController::class, 'destroy']);

            Route::post('kategori-produks', [V1KategoriProdukController::class, 'store']);
            Route::match(['put', 'patch'], 'kategori-produks/{kategoriProduk}', [V1KategoriProdukController::class, 'update']);
            Route::delete('kategori-produks/{kategoriProduk}', [V1KategoriProdukController::class, 'destroy']);

            Route::post('mejas', [V1MejaController::class, 'store']);
            Route::match(['put', 'patch'], 'mejas/{meja}', [V1MejaController::class, 'update']);
            Route::delete('mejas/{meja}', [V1MejaController::class, 'destroy']);

            // Nama route API diberi prefiks agar tidak menimpa nama route web
            // (mis. suppliers.index) yang dipakai form dan tautan admin.
            Route::apiResource('suppliers', V1SupplierController::class)
                ->names('api.v1.suppliers');
            Route::apiResource('karyawans', V1KaryawanController::class)
                ->names('api.v1.karyawans');
            Route::apiResource('promo-kodes', V1PromoKodeController::class)
                ->parameters(['promo-kodes' => 'promoKode'])
                ->names('api.v1.promo-kodes');
        });
    });
});
