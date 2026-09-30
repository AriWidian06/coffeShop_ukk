<?php

use App\Http\Controllers\Api\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::post('/transaksis', [TransaksiController::class, 'store']);
