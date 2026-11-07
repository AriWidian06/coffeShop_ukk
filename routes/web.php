<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\PromoKodeController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiController;
use App\Models\DetailTransaksi;
use App\Models\Meja;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use App\Models\KategoriProduk; 
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::post('/midtrans/notification', [TransaksiController::class, 'midtransNotification'])->name('midtrans.notification');

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
    Route::get('/dashboard', function (Request $request) {
        $user = auth('karyawan')->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $role = strtolower($user->role);

        if ($role === 'manager') {
            $tz = 'Asia/Jakarta';
            Carbon::setLocale('id');
            $nowWib = Carbon::now($tz);

            $periode = $request->query('periode', 'bulanan');
            if (! in_array($periode, ['harian', 'mingguan', 'bulanan'], true)) {
                $periode = 'bulanan';
            }

            $selectedDate = $nowWib->copy();
            $tanggal = $request->query('tanggal');
            if (is_string($tanggal) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
                try {
                    $selectedDate = Carbon::parse($tanggal, $tz);
                } catch (\Throwable $e) {
                    $selectedDate = $nowWib->copy();
                }
            }

            if ($periode === 'harian') {
                $start = $selectedDate->copy()->startOfDay();
                $end = $selectedDate->copy()->endOfDay();
                $prevReference = $selectedDate->copy()->subDay();
                $nextReference = $selectedDate->copy()->addDay();
                $periodeLabel = $start->translatedFormat('d F Y');
            } elseif ($periode === 'mingguan') {
                $start = $selectedDate->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
                $end = $selectedDate->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                $prevReference = $selectedDate->copy()->subWeek();
                $nextReference = $selectedDate->copy()->addWeek();
                $periodeLabel = $start->translatedFormat('d M') . ' - ' . $end->translatedFormat('d M Y');
            } else {
                $start = $selectedDate->copy()->startOfMonth()->startOfDay();
                $end = $selectedDate->copy()->endOfMonth()->endOfDay();
                $prevReference = $selectedDate->copy()->subMonthNoOverflow();
                $nextReference = $selectedDate->copy()->addMonthNoOverflow();
                $periodeLabel = $start->translatedFormat('F Y');
            }

            $startUtc = $start->copy()->utc();
            $endUtc = $end->copy()->utc();
            $todayStartUtc = $nowWib->copy()->startOfDay()->utc();
            $todayEndUtc = $nowWib->copy()->endOfDay()->utc();

            // Snapshot real-time hari ini (WIB)
            $totalPendapatanHariIni = Transaksi::whereBetween('waktu_transaksi', [$todayStartUtc, $todayEndUtc])->sum('total_harga');
            $totalTransaksiHariIni = Transaksi::whereBetween('waktu_transaksi', [$todayStartUtc, $todayEndUtc])->count();
            $totalItemHariIni = DetailTransaksi::whereHas('transaksi', fn ($q) => $q->whereBetween('waktu_transaksi', [$todayStartUtc, $todayEndUtc]))->sum('QTY');

            // Statistik periode terpilih
            $totalPendapatanPeriode = Transaksi::whereBetween('waktu_transaksi', [$startUtc, $endUtc])->sum('total_harga');
            $totalTransaksiPeriode = Transaksi::whereBetween('waktu_transaksi', [$startUtc, $endUtc])->count();
            $averageTransactionValue = $totalTransaksiPeriode > 0 ? round($totalPendapatanPeriode / $totalTransaksiPeriode) : 0;
            $totalItemPeriode = DetailTransaksi::whereHas('transaksi', fn ($q) => $q->whereBetween('waktu_transaksi', [$startUtc, $endUtc]))->sum('QTY');

            // Pembanding periode sebelumnya
            $prevStart = match ($periode) {
                'harian' => $start->copy()->subDay(),
                'mingguan' => $start->copy()->subWeek(),
                default => $start->copy()->subMonthNoOverflow(),
            };
            $prevEnd = $start->copy()->subSecond();
            $pendapatanSebelumnya = Transaksi::whereBetween('waktu_transaksi', [$prevStart->copy()->utc(), $prevEnd->copy()->utc()])->sum('total_harga');
            $pendapatanGrowth = $pendapatanSebelumnya > 0
                ? round((($totalPendapatanPeriode - $pendapatanSebelumnya) / $pendapatanSebelumnya) * 100, 1)
                : null;

            // Omzet & jumlah transaksi per jam (WIB)
            $hourlyRows = Transaksi::whereBetween('waktu_transaksi', [$startUtc, $endUtc])
                ->selectRaw('HOUR(waktu_transaksi) as jam, COUNT(*) as jumlah, SUM(total_harga) as total')
                ->groupBy('jam')
                ->orderBy('jam')
                ->get()
                ->map(function ($row) {
                    $row->jam = ((int) $row->jam + 7) % 24;
                    $row->total = (float) $row->total;
                    $row->jumlah = (int) $row->jumlah;
                    return $row;
                })
                ->sortBy('jam')
                ->values();

            $transaksiPerJam = $hourlyRows;
            $salesByHour = $hourlyRows->mapWithKeys(fn ($row) => [(int) $row->jam => (float) $row->total])->sortKeys();
            $peakRow = $hourlyRows->sortByDesc('total')->first();
            $peakHour = $peakRow ? (int) $peakRow->jam : null;
            $peakHourLabel = $peakHour !== null
                ? sprintf('%02d:00 - %02d:00 WIB', $peakHour, ($peakHour + 1) % 24)
                : 'Belum ada data';

            // Top 5 produk sesuai periode
            $produkTerlaris = DetailTransaksi::query()
                ->with('produk.kategoriProduk')
                ->select('produk_id', DB::raw('SUM(QTY) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
                ->whereHas('transaksi', fn ($q) => $q->whereBetween('waktu_transaksi', [$startUtc, $endUtc]))
                ->groupBy('produk_id')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->get();

            // Distribusi metode pembayaran
            $paymentStats = Pembayaran::query()
                ->whereHas('transaksi', fn ($q) => $q->whereBetween('waktu_transaksi', [$startUtc, $endUtc]))
                ->selectRaw('metode_pembayaran, COUNT(*) as jumlah, SUM(jumlah_bayar) as total')
                ->groupBy('metode_pembayaran')
                ->get();

            $shift = match (true) {
                $nowWib->hour >= 6 && $nowWib->hour < 14 => 'Pagi (06:00 - 14:00)',
                $nowWib->hour >= 14 && $nowWib->hour < 22 => 'Siang/Sore (14:00 - 22:00)',
                default => 'Malam (22:00 - 06:00)',
            };

            return view('dashboard.manager', compact(
                'totalPendapatanHariIni', 'totalTransaksiHariIni', 'totalItemHariIni',
                'totalPendapatanPeriode', 'totalTransaksiPeriode', 'totalItemPeriode',
                'averageTransactionValue', 'pendapatanGrowth', 'pendapatanSebelumnya',
                'transaksiPerJam', 'salesByHour', 'peakHour', 'peakHourLabel',
                'produkTerlaris', 'paymentStats',
                'periode', 'periodeLabel', 'selectedDate', 'prevReference', 'nextReference', 'shift', 'nowWib',
                'start', 'end'
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
            $tz = 'Asia/Jakarta';
            Carbon::setLocale('id');
            $nowWib = Carbon::now($tz);
            $todayStartUtc = $nowWib->copy()->startOfDay()->utc();
            $todayEndUtc = $nowWib->copy()->endOfDay()->utc();

            $totalPenjualan = Transaksi::whereBetween('waktu_transaksi', [$todayStartUtc, $todayEndUtc])->sum('total_harga');
            $totalPesanan = Transaksi::whereBetween('waktu_transaksi', [$todayStartUtc, $todayEndUtc])
                                    ->where('status_pesanan', 'completed')->count();

            $totalMeja = Meja::count();
            $mejaTerisi = Transaksi::whereIn('status_pesanan', ['pending', 'ready', 'prosesed'])
                                    ->whereNotNull('meja_id')
                                    ->distinct('meja_id')
                                    ->count();
            $okupansi = $totalMeja > 0 ? round(($mejaTerisi / $totalMeja) * 100) : 0;

            $stokHabis = Produk::where('stock', '<', 10)->count();

            $transaksiTerbaru = Transaksi::with(['meja', 'karyawan', 'detail_transaksis.produk'])
                                    ->latest('waktu_transaksi')
                                    ->limit(5)
                                    ->get();

            $karyawans = \App\Models\Karyawan::all()->map(function ($k) use ($todayStartUtc, $todayEndUtc) {
                $k->completed_orders = Transaksi::where('karyawan_id', $k->id)
                    ->where('status_pesanan', 'completed')
                    ->whereBetween('waktu_transaksi', [$todayStartUtc, $todayEndUtc])
                    ->count();
                $k->total_revenue = Transaksi::where('karyawan_id', $k->id)
                    ->where('status_pesanan', 'completed')
                    ->whereBetween('waktu_transaksi', [$todayStartUtc, $todayEndUtc])
                    ->sum('total_harga');
                return $k;
            });

            $salesByHour = Transaksi::whereBetween('waktu_transaksi', [$todayStartUtc, $todayEndUtc])
                ->selectRaw('HOUR(waktu_transaksi) as hour, SUM(total_harga) as total')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get()
                ->mapWithKeys(function ($row) {
                    $hour = ((int) $row->hour + 7) % 24;
                    return [$hour => (float) $row->total];
                })
                ->sortKeys();

            $peakHour = $salesByHour->isNotEmpty()
                ? (int) $salesByHour->sortDesc()->keys()->first()
                : null;
            $peakHourLabel = $peakHour !== null
                ? sprintf('%02d:00 - %02d:00 WIB', $peakHour, ($peakHour + 1) % 24)
                : 'Belum ada data';

            $metodeTerbanyak = Pembayaran::query()
                ->whereHas('transaksi', fn ($q) => $q->whereBetween('waktu_transaksi', [$todayStartUtc, $todayEndUtc]))
                ->selectRaw('metode_pembayaran, COUNT(*) as jumlah')
                ->groupBy('metode_pembayaran')
                ->orderByDesc('jumlah')
                ->value('metode_pembayaran') ?? 'Belum ada data';

            $topCategories = DetailTransaksi::join('produks', 'detail_transaksis.produk_id', '=', 'produks.id')
                ->join('kategori_produks', 'produks.kategori_produk_id', '=', 'kategori_produks.id')
                ->join('transaksis', 'detail_transaksis.transaksi_id', '=', 'transaksis.id')
                ->whereBetween('transaksis.waktu_transaksi', [$todayStartUtc, $todayEndUtc])
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
                'peakHourLabel',
                'metodeTerbanyak',
                'topCategories',
                'nowWib'
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
        Route::resource('promo-kodes', PromoKodeController::class)->except('show');
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

    // Rute khusus Kasir untuk pembayaran QRIS
    Route::middleware(['auth:karyawan'])->prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/transaksi/{id}/qris', [TransaksiController::class, 'showQrisPayment'])->name('payment.qris');
        Route::post('/transaksi/{id}/qris/check', [TransaksiController::class, 'checkQrisPayment'])->name('payment.qris.check');
    });
});
