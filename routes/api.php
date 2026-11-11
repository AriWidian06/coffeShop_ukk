<?php

use App\Http\Controllers\Api\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->group(function () {
    Route::post('/transaksis', [TransaksiController::class, 'store']);
    Route::get('/pesanan-saya', [TransaksiController::class, 'myOrder']);
    Route::post('/transaksis/{id}/check-status', [TransaksiController::class, 'checkStatus']);
    Route::post('/promo/validate', [\App\Http\Controllers\Api\PromoController::class, 'validatePromo']);
});
