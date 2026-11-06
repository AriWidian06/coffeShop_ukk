<?php

use App\Http\Controllers\Api\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::post('/transaksis', [TransaksiController::class, 'store']);
Route::post('/transaksis/{id}/check-status', [TransaksiController::class, 'checkStatus']);
Route::post('/promo/validate', [\App\Http\Controllers\Api\PromoController::class, 'validatePromo']);
