<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\PromoKodeController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiController;
use App\Models\KasAccount;
use App\Models\Pengeluaran;
use App\Models\DetailTransaksi;
use App\Models\Meja;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use App\Models\KategoriProduk; 
use App\Models\Produk;
use App\Services\KasService;
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

    $kategoriProduks = KategoriProduk::untukKatalog()->orderBy('nama_kategori')->get();

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

            // Ringkasan arus kas & laba rugi pada periode terpilih.
            $kasService = app(KasService::class);
            $totalSaldoKas = (float) KasAccount::where('aktif', true)->get()
                ->sum(fn ($account) => $kasService->saldo($account));
            $pengeluaranPeriode = (float) Pengeluaran::approved()
                ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                ->sum('jumlah');
            $hppPeriode = (float) (DetailTransaksi::join('produks', 'detail_transaksis.produk_id', '=', 'produks.id')
                ->whereHas('transaksi', fn ($q) => $q->whereBetween('waktu_transaksi', [$startUtc, $endUtc])->where('status_pesanan', '!=', 'cancelled'))
                ->selectRaw('SUM(detail_transaksis.QTY * produks.harga_beli) as total')
                ->value('total') ?? 0);
            $labaKotorPeriode = round($totalPendapatanPeriode - $hppPeriode, 2);
            $labaBersihPeriode = round($labaKotorPeriode - $pengeluaranPeriode, 2);
            $pengeluaranPending = (float) Pengeluaran::pending()->sum('jumlah');

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
                'totalSaldoKas', 'pengeluaranPeriode', 'labaKotorPeriode', 'labaBersihPeriode', 'pengeluaranPending',
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

            // Arus kas & laba rugi bulan berjalan.
            $monthStartUtc = $nowWib->copy()->startOfMonth()->utc();
            $monthEndUtc = $nowWib->copy()->endOfMonth()->utc();
            $totalPenjualanBulan = (float) Transaksi::whereBetween('waktu_transaksi', [$monthStartUtc, $monthEndUtc])
                ->where('status_pesanan', '!=', 'cancelled')->sum('total_harga');
            $hppBulan = (float) (DetailTransaksi::join('produks', 'detail_transaksis.produk_id', '=', 'produks.id')
                ->whereHas('transaksi', fn ($q) => $q->whereBetween('waktu_transaksi', [$monthStartUtc, $monthEndUtc])->where('status_pesanan', '!=', 'cancelled'))
                ->selectRaw('SUM(detail_transaksis.QTY * produks.harga_beli) as total')
                ->value('total') ?? 0);
            $pengeluaranBulan = (float) Pengeluaran::approved()
                ->whereBetween('tanggal', [$nowWib->copy()->startOfMonth()->toDateString(), $nowWib->copy()->endOfMonth()->toDateString()])
                ->sum('jumlah');
            $labaBersihBulan = round($totalPenjualanBulan - $hppBulan - $pengeluaranBulan, 2);
            $kasServiceAdmin = app(KasService::class);
            $totalSaldoKas = (float) KasAccount::where('aktif', true)->get()
                ->sum(fn ($account) => $kasServiceAdmin->saldo($account));
            $pengeluaranPending = (float) Pengeluaran::pending()->sum('jumlah');

            return view('dashboard.admin', compact(
                'totalSaldoKas', 'totalPenjualanBulan', 'pengeluaranBulan', 'labaBersihBulan', 'pengeluaranPending',
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

    Route::get('/dashboard/sales-chart', function (Request $request) {
        $tz = 'Asia/Jakarta';
        $now = Carbon::now($tz);
        $periode = $request->query('periode', 'hari');
        if (! in_array($periode, ['hari', 'minggu', 'bulan', 'tahun'], true)) {
            $periode = 'hari';
        }

        $labels = [];
        $sales = [];
        $volume = [];
        $peakIndex = null;

        if ($periode === 'hari') {
            $data = DashboardSalesChart::forDay($now);
            $labels = array_map(fn ($h) => str_pad((string) $h, 2, '0', STR_PAD_LEFT), range(0, 23));
            $sales = array_values($data['sales']);
            $volume = array_values($data['volume']);
            $peakIndex = $data['peak_hour'];
        } else {
            if ($periode === 'minggu') {
                $start = $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
                $end = $now->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                $bucketLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
                $n = 7;
                $keyFn = fn ($dt) => ($dt->dayOfWeek + 6) % 7;
            } elseif ($periode === 'bulan') {
                $start = $now->copy()->startOfYear()->startOfDay();
                $end = $now->copy()->endOfYear()->endOfDay();
                $bucketLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $n = 12;
                $keyFn = fn ($dt) => $dt->month - 1;
            } else { // tahun
                $startYear = $now->year - 4;
                $endYear = $now->year;
                $start = Carbon::create($startYear, 1, 1, 0, 0, 0, $tz)->startOfDay();
                $end = Carbon::create($endYear, 12, 31, 23, 59, 59, $tz)->endOfDay();
                $bucketLabels = array_map('strval', range($startYear, $endYear));
                $n = count($bucketLabels);
                $yearIndex = array_flip($bucketLabels);
                $keyFn = fn ($dt) => $yearIndex[(string) $dt->year] ?? null;
            }

            $sales = array_fill(0, $n, 0.0);
            $volume = array_fill(0, $n, 0);
            $labels = $bucketLabels;

            $rows = Transaksi::query()
                ->whereBetween('waktu_transaksi', [$start->copy()->utc(), $end->copy()->utc()])
                ->where('status_pesanan', '!=', 'cancelled')
                ->get(['waktu_transaksi', 'total_harga']);

            foreach ($rows as $row) {
                $dt = Carbon::parse($row->waktu_transaksi, 'UTC')->setTimezone($tz);
                $idx = $keyFn($dt);
                if ($idx === null || $idx < 0 || $idx >= $n) continue;
                $sales[$idx] += (float) $row->total_harga;
                $volume[$idx] += 1;
            }

            $peakIndex = count($sales) ? (int) array_search(max($sales), $sales, true) : null;
            if (max($sales) <= 0) $peakIndex = null;
        }

        $peakLabel = 'Belum ada data';
        if ($peakIndex !== null && isset($labels[$peakIndex])) {
            if ($periode === 'hari') {
                $peakLabel = sprintf('%s:00 - %s:00 WIB', str_pad((string) $labels[$peakIndex], 2, '0', STR_PAD_LEFT), str_pad((string) (($labels[$peakIndex] + 1) % 24), 2, '0', STR_PAD_LEFT));
            } else {
                $peakLabel = (string) $labels[$peakIndex];
            }
        }

        return response()->json([
            'sales' => array_values($sales),
            'volume' => array_values($volume),
            'labels' => array_values($labels),
            'peak_hour' => $peakIndex,
            'peak_hour_label' => $peakLabel,
            'updated_at' => Carbon::now($tz)->format('H:i:s'),
            'periode' => $periode,
        ]);
    })->middleware('can:access-admin-panel')->name('dashboard.sales-chart');

    // Halaman Index Denah Meja (Bisa diakses Kasir, Admin, & Manager)
    Route::get('/mejas', [MejaController::class, 'index'])->name('mejas.index');
    Route::get('/mejas/{meja}/qr/download', [MejaController::class, 'downloadQr'])->name('mejas.qr-download');
    Route::get('/mejas/{meja}/qr/image', [MejaController::class, 'qrImage'])->name('mejas.qr-image');
    Route::get('/mejas/qr/download-all', [MejaController::class, 'downloadAllQr'])->name('mejas.qr-download-all');

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

    // ==========================================
    // Pengeluaran & Alur Kas
    // ==========================================
    Route::middleware('can:lihat-kas')->group(function () {
        Route::get('/kas', [KasController::class, 'index'])->name('kas.index');
        Route::get('/kas/mutasi', [KasController::class, 'mutasi'])->name('kas.mutasi');
        Route::get('/pengeluarans', [PengeluaranController::class, 'index'])->name('pengeluarans.index');
    });

    Route::middleware('can:input-pengeluaran')->group(function () {
        Route::get('/pengeluarans/create', [PengeluaranController::class, 'create'])->name('pengeluarans.create');
        Route::post('/pengeluarans', [PengeluaranController::class, 'store'])->name('pengeluarans.store');
        Route::get('/pengeluarans/{pengeluaran}/edit', [PengeluaranController::class, 'edit'])->name('pengeluarans.edit');
        Route::put('/pengeluarans/{pengeluaran}', [PengeluaranController::class, 'update'])->name('pengeluarans.update');
        Route::delete('/pengeluarans/{pengeluaran}', [PengeluaranController::class, 'destroy'])->name('pengeluarans.destroy');
    });

    Route::get('/pengeluarans/{pengeluaran}', [PengeluaranController::class, 'show'])
        ->middleware('can:lihat-kas')
        ->name('pengeluarans.show');

    Route::middleware('can:approve-pengeluaran')->group(function () {
        Route::post('/pengeluarans/{pengeluaran}/approve', [PengeluaranController::class, 'approve'])->name('pengeluarans.approve');
        Route::post('/pengeluarans/{pengeluaran}/reject', [PengeluaranController::class, 'reject'])->name('pengeluarans.reject');
    });

    Route::middleware('can:kelola-kas')->group(function () {
        Route::get('/kas/saldo-awal', [KasController::class, 'saldoAwal'])->name('kas.saldo-awal');
        Route::post('/kas/saldo-awal', [KasController::class, 'simpanSaldoAwal'])->name('kas.saldo-awal.store');
        Route::get('/kas/transfer', [KasController::class, 'transfer'])->name('kas.transfer');
        Route::post('/kas/transfer', [KasController::class, 'simpanTransfer'])->name('kas.transfer.store');
        Route::get('/kas/penyesuaian', [KasController::class, 'penyesuaian'])->name('kas.penyesuaian');
        Route::post('/kas/penyesuaian', [KasController::class, 'simpanPenyesuaian'])->name('kas.penyesuaian.store');
    });

    Route::middleware('can:tutup-kas')->group(function () {
        Route::get('/kas/tutup-hari', [KasController::class, 'tutupHari'])->name('kas.tutup-hari');
        Route::post('/kas/tutup-hari', [KasController::class, 'simpanTutupHari'])->name('kas.tutup-hari.store');
    });

    Route::post('/kas/closing/{closing}/verifikasi', [KasController::class, 'verifikasi'])
        ->middleware('can:verifikasi-kas')
        ->name('kas.verifikasi');

    Route::get('/laporan-kas', [KasController::class, 'laporan'])
        ->middleware('can:lihat-laporan-kas')
        ->name('kas.laporan');

    // Rute khusus Kasir untuk pembayaran QRIS
    Route::middleware(['auth:karyawan'])->prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/transaksi/{id}/qris', [TransaksiController::class, 'showQrisPayment'])->name('payment.qris');
        Route::post('/transaksi/{id}/qris/check', [TransaksiController::class, 'checkQrisPayment'])->name('payment.qris.check');
    });
});
