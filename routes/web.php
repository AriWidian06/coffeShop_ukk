<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\LaporanController; // <-- Controller untuk Ekspor
use App\Http\Controllers\MejaController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiController;
use App\Models\Meja;
use App\Models\KategoriProduk; 
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. PUBLIC ROUTES (Tanpa Login)
// ==========================================
Route::get('/', function (Request $request) {
    $mejas = Meja::where('status_aktif', true)->orderBy('nomor_meja')->get();
    $mejaId = $request->query('meja_id');
    $mejaAktif = $mejaId ? $mejas->firstWhere('id', $mejaId) : null;

    $kategoriProduks = KategoriProduk::orderBy('nama_kategori')->get();

    $produks = Produk::with(['kategoriProduk', 'opsis'])
        ->where('tipe', 'jual')
        ->where('status_aktif', true)
        ->orderBy('nama_produk')
        ->get();

    return view('welcome', compact('mejaAktif', 'mejas', 'kategoriProduks', 'produks'));
})->name('welcome'); 

Route::get('/health', [HealthController::class, 'check']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 2. AUTHENTICATED ROUTES (Wajib Login)
// ==========================================
Route::middleware('auth:karyawan')->group(function () {
    
    // Dashboard berdasarkan Role (Admin / Manager / Kasir)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Panel (CRUD Utama - Hanya Admin)
    Route::middleware('can:access-admin-panel')->group(function () {
        Route::resource('karyawans', KaryawanController::class)->except('show');
        
        Route::resource('kategori-produks', KategoriProdukController::class)
            ->parameters(['kategori-produks' => 'kategoriProduk'])
            ->except('show');
            
        Route::resource('mejas', MejaController::class)->except('show');
        Route::resource('produks', ProdukController::class)->except('show');
        Route::resource('suppliers', SupplierController::class)->except('show');
    });

    // Transaksi & POS (Kasir & Admin)
    Route::middleware('can:access-pos')->group(function () {
        Route::get('/transaksis/create', [TransaksiController::class, 'create'])->name('transaksis.create');
        Route::post('/transaksis', [TransaksiController::class, 'store'])->name('transaksis.store');
        Route::delete('/transaksis/{transaksi}', [TransaksiController::class, 'destroy'])->name('transaksis.destroy');
        Route::patch('/transaksis/{transaksi}/status', [TransaksiController::class, 'updateStatus'])->name('transaksis.status');
        
        Route::post('/pembayarans', [PembayaranController::class, 'store'])->name('pembayarans.store');
        Route::get('/transaksis/{transaksi}/receipt', [PembayaranController::class, 'printReceipt'])->name('transaksis.receipt');
    });

    // Laporan & Ekspor (Manager & Admin)
    Route::middleware('can:view-transactions')->group(function () {
        // Lihat daftar transaksi
        Route::get('/transaksis', [TransaksiController::class, 'index'])->name('transaksis.index');
        Route::get('/transaksis/{transaksi}', [TransaksiController::class, 'show'])->name('transaksis.show');
        
        // FITUR EKSPOR LAPORAN CSV BARU
        Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
    });
});