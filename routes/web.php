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
    Route::get('/dashboard', function () {
        $role = auth('karyawan')->user()->role;

        if ($role === 'manager') {
            return view('dashboard.manager');
        }

        if ($role === 'kasir') {
            return view('dashboard.kasir');
        }

        return view('dashboard.admin');
    })->name('dashboard');

    Route::middleware('can:access-admin-panel')->group(function () {
        Route::resource('karyawans', KaryawanController::class)->except('show');
        Route::resource('kategori-produks', KategoriProdukController::class)
            ->parameters(['kategori-produks' => 'kategoriProduk'])
            ->except('show');
        Route::resource('mejas', MejaController::class)->except('show');
        Route::resource('produks', ProdukController::class)->except('show');
        Route::resource('suppliers', SupplierController::class)->except('show');
    });

    Route::middleware('can:view-sales-report')->group(function () {
        Route::get('/transaksis', [TransaksiController::class, 'index'])->name('transaksis.index');
        Route::get('/transaksis/{transaksi}', [TransaksiController::class, 'show'])->name('transaksis.show');
    });

    Route::middleware('can:access-pos')->group(function () {
        Route::get('/transaksis/create', [TransaksiController::class, 'create'])->name('transaksis.create');
        Route::post('/transaksis', [TransaksiController::class, 'store'])->name('transaksis.store');
        Route::delete('/transaksis/{transaksi}', [TransaksiController::class, 'destroy'])->name('transaksis.destroy');
        Route::patch('/transaksis/{transaksi}/status', [TransaksiController::class, 'updateStatus'])
            ->name('transaksis.status');
        Route::post('/pembayarans', [PembayaranController::class, 'store'])->name('pembayarans.store');
        Route::get('/transaksis/{transaksi}/receipt', [PembayaranController::class, 'printReceipt'])
            ->name('transaksis.receipt');
    });
});
