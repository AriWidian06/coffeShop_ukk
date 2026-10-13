<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiController;
use App\Models\Meja;
use App\Models\Transaksi;
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

    // Meja yang sudah punya pesanan aktif (belum selesai) -> tidak bisa dipilih pelanggan
    $mejaTerisi = Transaksi::whereIn('status_pesanan', ['pending', 'ready', 'prosesed'])
        ->whereNotNull('meja_id')
        ->distinct()
        ->pluck('meja_id');

    $kategoriProduks = KategoriProduk::orderBy('nama_kategori')->get();

    $produks = Produk::with(['kategoriProduk', 'opsis'])
        ->where('tipe', 'jual')
        ->where('status_aktif', true)
        ->orderBy('nama_produk')
        ->get();

    return view('welcome', compact('mejaAktif', 'mejas', 'mejaTerisi', 'kategoriProduks', 'produks'));
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
    Route::get('/dashboard', function () {
        $user = auth('karyawan')->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $role = strtolower($user->role);

        if ($role === 'manager') {
            $today = now()->toDateString();
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();

            // 1. Snapshot Real-time Hari Ini
            $totalPendapatanHariIni = \App\Models\Transaksi::whereDate('waktu_transaksi', $today)->sum('total_harga');
            $totalTransaksiHariIni = \App\Models\Transaksi::whereDate('waktu_transaksi', $today)->count();

            // 2. Statistik Akumulasi Bulan Ini
            $totalPendapatanBulanIni = \App\Models\Transaksi::whereBetween('waktu_transaksi', [$startOfMonth, $endOfMonth])->sum('total_harga');
            $totalTransaksiBulanIni = \App\Models\Transaksi::whereBetween('waktu_transaksi', [$startOfMonth, $endOfMonth])->count();
            
            // 3. Nilai Rata-rata Transaksi (ATV)
            $averageTransactionValue = $totalTransaksiBulanIni > 0 ? round($totalPendapatanBulanIni / $totalTransaksiBulanIni) : 0;

            // 4. Kurva Transaksi per Jam Hari Ini
            $transaksiPerJam = \App\Models\Transaksi::whereDate('waktu_transaksi', $today)
                ->selectRaw('HOUR(waktu_transaksi) as jam, COUNT(*) as jumlah')
                ->groupBy('jam')
                ->orderBy('jam')
                ->get();

            // 5. Top 5 Produk Terlaris
            $produkTerlaris = \App\Models\DetailTransaksi::with('produk.kategoriProduk')
                ->selectRaw('produk_id, SUM(QTY) as total_qty')
                ->groupBy('produk_id')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->get();

            return view('dashboard.manager', compact(
                'totalPendapatanHariIni',
                'totalTransaksiHariIni',
                'totalPendapatanBulanIni',
                'totalTransaksiBulanIni',
                'averageTransactionValue',
                'transaksiPerJam',
                'produkTerlaris'
            ));
        }

        if ($role === 'kasir') {
            $mejas = \App\Models\Meja::where('status_aktif', true)->orderBy('nomor_meja')->get();
            $pesananAktif = \App\Models\Transaksi::with(['meja', 'karyawan', 'detail_transaksis.produk', 'pembayaran'])
                ->whereIn('status_pesanan', ['pending', 'ready', 'prosesed'])
                ->latest('waktu_transaksi')
                ->get();

            $activeOrders = $pesananAktif
                ->filter(fn ($order) => !empty($order->meja_id))
                ->keyBy('meja_id');

            $mejaStatus = $activeOrders->keys()->toArray();

            $transaksiTerbaru = \App\Models\Transaksi::with(['meja', 'karyawan', 'detail_transaksis.produk', 'pembayaran'])
                ->where(function ($query) {
                    $query->where('karyawan_id', auth('karyawan')->id())
                        ->orWhere('sumber_pesanan', 'web');
                })
                ->latest('waktu_transaksi')
                ->limit(10)
                ->get();

            $kategoriProduks = \App\Models\KategoriProduk::orderBy('nama_kategori')->get();
            $produks = \App\Models\Produk::with(['kategoriProduk', 'opsis'])
                ->where('tipe', 'jual')
                ->where('status_aktif', true)
                ->orderBy('nama_produk')
                ->get();

            return view('dashboard.kasir', compact('mejas', 'activeOrders', 'mejaStatus', 'pesananAktif', 'transaksiTerbaru', 'kategoriProduks', 'produks'));
        }

        if ($role === 'admin') {
            $today = now()->toDateString();

            $totalPenjualan = \App\Models\Transaksi::whereDate('waktu_transaksi', $today)->sum('total_harga');
            $totalPesanan = \App\Models\Transaksi::whereDate('waktu_transaksi', $today)
                                    ->where('status_pesanan', 'completed')->count();

            $totalMeja = \App\Models\Meja::count();
            $mejaTerisi = \App\Models\Transaksi::whereIn('status_pesanan', ['pending', 'ready', 'prosesed'])
                                    ->whereNotNull('meja_id')
                                    ->distinct('meja_id')
                                    ->count();
            $okupansi = $totalMeja > 0 ? round(($mejaTerisi / $totalMeja) * 100) : 0;

            $stokHabis = \App\Models\Produk::where('stock', '<', 10)->count();

            $transaksiTerbaru = \App\Models\Transaksi::with(['meja', 'karyawan', 'detail_transaksis.produk'])
                                    ->latest('waktu_transaksi')
                                    ->limit(5)
                                    ->get();

            $karyawans = \App\Models\Karyawan::all()->map(function($k) use ($today) {
                $k->completed_orders = \App\Models\Transaksi::where('karyawan_id', $k->id)
                    ->where('status_pesanan', 'completed')
                    ->count();
                $k->total_revenue = \App\Models\Transaksi::where('karyawan_id', $k->id)
                    ->where('status_pesanan', 'completed')
                    ->sum('total_harga');
                return $k;
            });

            $salesByHour = \App\Models\Transaksi::whereDate('waktu_transaksi', $today)
                ->selectRaw('HOUR(waktu_transaksi) as hour, SUM(total_harga) as total')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get()
                ->pluck('total', 'hour');

            $peakHour = $salesByHour->max()
                ? $salesByHour->sortDesc()->keys()->first()
                : null;

            $topCategories = \App\Models\DetailTransaksi::join('produks', 'detail_transaksis.produk_id', '=', 'produks.id')
                ->join('kategori_produks', 'produks.kategori_produk_id', '=', 'kategori_produks.id')
                ->selectRaw('kategori_produks.nama_kategori, SUM(detail_transaksis.QTY) as total_qty, SUM(detail_transaksis.subtotal) as total_revenue')
                ->groupBy('kategori_produks.nama_kategori')
                ->orderByDesc('total_qty')
                ->limit(4)
                ->get();

            return view('dashboard.admin', compact(
                'totalPenjualan',
                'totalPesanan',
                'totalMeja',
                'mejaTerisi',
                'okupansi',
                'stokHabis',
                'transaksiTerbaru',
                'karyawans',
                'salesByHour',
                'peakHour',
                'topCategories'
            ));
        }

        // Fallback jika role tidak terdaftar atau tidak cocok
        return redirect()->route('welcome')->with('error', 'Role pengguna tidak dikenali.');
    })->name('dashboard');

    // Halaman Index Denah Meja (Bisa diakses Kasir, Admin, & Manager)
    Route::get('/mejas', [MejaController::class, 'index'])->name('mejas.index');

    // Admin Panel (CRUD Utama - Hanya Admin)
    Route::middleware('can:access-admin-panel')->group(function () {
        Route::resource('karyawans', KaryawanController::class)->except('show');
        
        Route::resource('kategori-produks', KategoriProdukController::class)
            ->parameters(['kategori-produks' => 'kategoriProduk'])
            ->except('show');
            
        // CRUD Meja (Tambah, Edit, Hapus) khusus Admin
        Route::resource('mejas', MejaController::class)->except(['index', 'show']);
        Route::resource('produks', ProdukController::class)->except('show');
        Route::resource('suppliers', SupplierController::class)->except('show');
    });

    // Transaksi & POS (Kasir & Admin)
    Route::middleware('can:access-pos')->group(function () {
        Route::get('/transaksis/create', [TransaksiController::class, 'create'])->name('transaksis.create');
        Route::post('/transaksis', [TransaksiController::class, 'store'])->name('transaksis.store');
        Route::delete('/transaksis/{transaksi}', [TransaksiController::class, 'destroy'])->name('transaksis.destroy');
        Route::patch('/transaksis/{transaksi}/status', [TransaksiController::class, 'updateStatus'])->name('transaksis.status');
        Route::get('/pos/active-orders', [TransaksiController::class, 'activeOrdersJson'])->name('transaksis.active');
        
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