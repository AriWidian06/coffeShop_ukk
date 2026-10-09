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
use App\Models\Meja;
use App\Models\KategoriProduk; 
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route Welcome (Halaman Publik / Menu)
Route::get('/', function (Request $request) {
    // 1. Ambil meja aktif
    $mejas = Meja::where('status_aktif', true)->orderBy('nomor_meja')->get();
    $mejaId = $request->query('meja_id');
    $mejaAktif = $mejaId ? $mejas->firstWhere('id', $mejaId) : null;

    // 2. Ambil kategori produk
    $kategoriProduks = KategoriProduk::orderBy('nama_kategori')->get();

    // 3. Ambil produk: HANYA yang tipe 'jual' DAN status_aktif = true
    $produks = Produk::with('kategoriProduk')
        ->where('tipe', 'jual')
        ->where('status_aktif', true)
        ->orderBy('nama_produk')
        ->get();

    return view('welcome', compact('mejaAktif', 'mejas', 'kategoriProduks', 'produks'));
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
            $transaksiTerbaru = Transaksi::with(['meja', 'karyawan', 'detail_transaksis.produk'])
                ->where(function ($query) {
                    $query->where('karyawan_id', auth('karyawan')->id())
                        ->orWhere('sumber_pesanan', 'web');
                })
                ->latest('waktu_transaksi')
                ->limit(5)
                ->get();

            return view('dashboard.kasir', compact('transaksiTerbaru'));
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

    Route::get('/transaksis', [TransaksiController::class, 'index'])
        ->middleware('can:view-transactions')
        ->name('transaksis.index');

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

    Route::get('/transaksis/{transaksi}', [TransaksiController::class, 'show'])
        ->middleware('can:view-transactions')
        ->name('transaksis.show');
});
