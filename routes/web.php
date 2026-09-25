<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', [HealthController::class, 'check']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth:karyawan')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::resource('karyawans', KaryawanController::class)->except('show');
    Route::resource('kategori-produks', KategoriProdukController::class)
        ->parameters(['kategori-produks' => 'kategoriProduk'])
        ->except('show');
    Route::resource('mejas', MejaController::class)->except('show');
    Route::resource('produks', ProdukController::class)->except('show');
    Route::resource('suppliers', SupplierController::class)->except('show');
    Route::resource('transaksis', TransaksiController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::patch('/transaksis/{transaksi}/status', [TransaksiController::class, 'updateStatus'])
        ->name('transaksis.status');
    Route::post('/pembayarans', [PembayaranController::class, 'store'])->name('pembayarans.store');
    Route::get('/transaksis/{transaksi}/receipt', [PembayaranController::class, 'printReceipt'])
        ->name('transaksis.receipt');
});
