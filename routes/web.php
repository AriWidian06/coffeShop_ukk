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
use App\Support\DashboardSalesChart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
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

    // Ingat meja dari QR agar pelanggan yang mencoba membuka dashboard
    // tetap bisa dikembalikan ke link QR meja yang sama.
    if ($mejaAktif) {
        $request->session()->put([
            'meja_id' => $mejaAktif->id,
            'meja_nomor' => $mejaAktif->nomor_meja,
        ]);

        // Cookie cadangan agar link QR tetap dikenali meskipun sesi
        // pelanggan sudah kedaluwarsa.
        Cookie::queue('qr_meja_id', $mejaAktif->id, 60 * 24 * 7);
        Cookie::queue('qr_meja_nomor', $mejaAktif->nomor_meja, 60 * 24 * 7);
    }

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

// Gambar produk dari database (fallback saat file storage sementara hilang).
Route::get('/produk/{produk}/gambar', [ProdukController::class, 'gambar'])->name('produks.gambar');

Route::get('/health', [HealthController::class, 'check']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.store');
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
            // Transaksi berstatus 'cancelled' (batal, belum dibayar) tidak dihitung
            // sebagai pendapatan maupun jumlah transaksi.
            $totalPendapatanHariIni = Transaksi::whereBetween('waktu_transaksi', [$todayStartUtc, $todayEndUtc])->where('status_pesanan', '!=', 'cancelled')->sum('total_harga');
            $totalTransaksiHariIni = Transaksi::whereBetween('waktu_transaksi', [$todayStartUtc, $todayEndUtc])->where('status_pesanan', '!=', 'cancelled')->count();
            $totalItemHariIni = DetailTransaksi::whereHas('transaksi', fn ($q) => $q->whereBetween('waktu_transaksi', [$todayStartUtc, $todayEndUtc])->where('status_pesanan', '!=', 'cancelled'))->sum('QTY');

            // Statistik periode terpilih
            $totalPendapatanPeriode = Transaksi::whereBetween('waktu_transaksi', [$startUtc, $endUtc])->where('status_pesanan', '!=', 'cancelled')->sum('total_harga');
            $totalTransaksiPeriode = Transaksi::whereBetween('waktu_transaksi', [$startUtc, $endUtc])->where('status_pesanan', '!=', 'cancelled')->count();
            $averageTransactionValue = $totalTransaksiPeriode > 0 ? round($totalPendapatanPeriode / $totalTransaksiPeriode) : 0;
            $totalItemPeriode = DetailTransaksi::whereHas('transaksi', fn ($q) => $q->whereBetween('waktu_transaksi', [$startUtc, $endUtc])->where('status_pesanan', '!=', 'cancelled'))->sum('QTY');

            // Pembanding periode sebelumnya
            $prevStart = match ($periode) {
                'harian' => $start->copy()->subDay(),
                'mingguan' => $start->copy()->subWeek(),
                default => $start->copy()->subMonthNoOverflow(),
            };
            $prevEnd = $start->copy()->subSecond();
            $pendapatanSebelumnya = Transaksi::whereBetween('waktu_transaksi', [$prevStart->copy()->utc(), $prevEnd->copy()->utc()])->where('status_pesanan', '!=', 'cancelled')->sum('total_harga');
            $pendapatanGrowth = $pendapatanSebelumnya > 0
                ? round((($totalPendapatanPeriode - $pendapatanSebelumnya) / $pendapatanSebelumnya) * 100, 1)
                : null;

            // Omzet & jumlah transaksi per jam (WIB)
            $chartData = DashboardSalesChart::forRange($start, $end);
            $salesByHour = collect($chartData['sales']);
            $volumeByHour = $chartData['volume'];
            $peakHour = $chartData['peak_hour'];
            $peakHourLabel = $peakHour !== null
                ? sprintf('%02d:00 - %02d:00 WIB', $peakHour, ($peakHour + 1) % 24)
                : 'Belum ada data';
            $chartUpdatedAt = $chartData['updated_at'];

            // Top 5 produk sesuai periode
            $produkTerlaris = DetailTransaksi::query()
                ->with('produk.kategoriProduk')
                ->select('produk_id', DB::raw('SUM(QTY) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
                ->whereHas('transaksi', fn ($q) => $q->whereBetween('waktu_transaksi', [$startUtc, $endUtc])->where('status_pesanan', '!=', 'cancelled'))
                ->groupBy('produk_id')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->get();

            // Distribusi metode pembayaran
            $paymentStats = Pembayaran::query()
                ->whereHas('transaksi', fn ($q) => $q->whereBetween('waktu_transaksi', [$startUtc, $endUtc])->where('status_pesanan', '!=', 'cancelled'))
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
                'salesByHour', 'volumeByHour', 'peakHour', 'peakHourLabel', 'chartUpdatedAt',
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

            // Transaksi berstatus 'cancelled' (batal, belum dibayar) tidak dihitung
            // sebagai penjualan maupun omzet.
            $totalPenjualan = Transaksi::whereBetween('waktu_transaksi', [$todayStartUtc, $todayEndUtc])->where('status_pesanan', '!=', 'cancelled')->sum('total_harga');
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

            $chartData = DashboardSalesChart::forDay($nowWib);
            $salesByHour = collect($chartData['sales']);
            $volumeByHour = $chartData['volume'];
            $peakHour = $chartData['peak_hour'];
            $chartUpdatedAt = $chartData['updated_at'];
            $peakHourLabel = $peakHour !== null
                ? sprintf('%02d:00 - %02d:00 WIB', $peakHour, ($peakHour + 1) % 24)
                : 'Belum ada data';

            $metodeTerbanyak = Pembayaran::query()
                ->whereHas('transaksi', fn ($q) => $q->whereBetween('waktu_transaksi', [$todayStartUtc, $todayEndUtc])->where('status_pesanan', '!=', 'cancelled'))
                ->selectRaw('metode_pembayaran, COUNT(*) as jumlah')
                ->groupBy('metode_pembayaran')
                ->orderByDesc('jumlah')
                ->value('metode_pembayaran') ?? 'Belum ada data';

            $topCategories = DetailTransaksi::join('produks', 'detail_transaksis.produk_id', '=', 'produks.id')
                ->join('kategori_produks', 'produks.kategori_produk_id', '=', 'kategori_produks.id')
                ->join('transaksis', 'detail_transaksis.transaksi_id', '=', 'transaksis.id')
                ->whereBetween('transaksis.waktu_transaksi', [$todayStartUtc, $todayEndUtc])
                ->where('transaksis.status_pesanan', '!=', 'cancelled')
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
                'volumeByHour',
                'peakHour',
                'peakHourLabel',
                'chartUpdatedAt',
                'metodeTerbanyak',
                'topCategories',
                'nowWib'
            ));
        }

        // Fallback jika role tidak terdaftar atau tidak cocok
        return redirect()->route('welcome')->with('error', 'Role pengguna tidak dikenali.');
    })->name('dashboard');

    Route::get('/dashboard/manager-sales-chart', function (Request $request) {
        $tz = 'Asia/Jakarta';
        $periode = $request->query('periode', 'bulanan');
        if (! in_array($periode, ['harian', 'mingguan', 'bulanan'], true)) {
            $periode = 'bulanan';
        }

        $selectedDate = Carbon::now($tz);
        $tanggal = $request->query('tanggal');
        if (is_string($tanggal) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            $parsedDate = Carbon::createFromFormat('!Y-m-d', $tanggal, $tz);
            if ($parsedDate && $parsedDate->format('Y-m-d') === $tanggal) {
                $selectedDate = $parsedDate;
            }
        }

        [$start, $end] = match ($periode) {
            'harian' => [$selectedDate->copy()->startOfDay(), $selectedDate->copy()->endOfDay()],
            'mingguan' => [
                $selectedDate->copy()->startOfWeek(Carbon::MONDAY)->startOfDay(),
                $selectedDate->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay(),
            ],
            default => [$selectedDate->copy()->startOfMonth()->startOfDay(), $selectedDate->copy()->endOfMonth()->endOfDay()],
        };

        $data = DashboardSalesChart::forRange($start, $end);
        $peakHour = $data['peak_hour'];

        return response()->json([
            ...$data,
            'peak_hour_label' => $peakHour !== null
                ? sprintf('%02d:00 - %02d:00 WIB', $peakHour, ($peakHour + 1) % 24)
                : 'Belum ada data',
        ]);
    })->middleware('can:view-sales-report')->name('dashboard.manager-sales-chart');

    Route::get('/dashboard/sales-chart', function () {
        $data = DashboardSalesChart::forDay(Carbon::now('Asia/Jakarta'));
        $peakHour = $data['peak_hour'];

        return response()->json([
            ...$data,
            'peak_hour_label' => $peakHour !== null
                ? sprintf('%02d:00 - %02d:00 WIB', $peakHour, ($peakHour + 1) % 24)
                : 'Belum ada data',
        ]);
    })->middleware('can:access-admin-panel')->name('dashboard.sales-chart');

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
