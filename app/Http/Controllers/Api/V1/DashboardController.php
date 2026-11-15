<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Meja;
use App\Models\Pembayaran;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Support\ApiPresenter;
use App\Support\DashboardSalesChart;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ApiResponse;

    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $role = strtolower((string) $user->role);
        $tz = 'Asia/Jakarta';
        $now = Carbon::now($tz);
        $todayStart = $now->copy()->startOfDay()->utc();
        $todayEnd = $now->copy()->endOfDay()->utc();

        $data = match ($role) {
            'manager' => $this->managerSummary($user, $now, $todayStart, $todayEnd),
            'kasir' => $this->kasirSummary($user, $todayStart, $todayEnd),
            default => $this->adminSummary($now, $todayStart, $todayEnd),
        };

        return $this->ok([
            'role' => $role,
            'generated_at' => $now->toIso8601String(),
            'data' => $data,
        ], 'Ringkasan dashboard.');
    }

    public function salesChart(Request $request): JsonResponse
    {
        $request->validate([
            'periode' => ['nullable', 'in:harian,mingguan,bulanan'],
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $tz = 'Asia/Jakarta';
        $periode = $request->input('periode', 'bulanan');
        if (! in_array($periode, ['harian', 'mingguan', 'bulanan'], true)) {
            $periode = 'bulanan';
        }

        $selectedDate = $request->filled('tanggal')
            ? Carbon::parse($request->input('tanggal'), $tz)
            : Carbon::now($tz);

        [$start, $end] = match ($periode) {
            'harian' => [$selectedDate->copy()->startOfDay(), $selectedDate->copy()->endOfDay()],
            'mingguan' => [
                $selectedDate->copy()->startOfWeek(Carbon::MONDAY)->startOfDay(),
                $selectedDate->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay(),
            ],
            default => [
                $selectedDate->copy()->startOfMonth()->startOfDay(),
                $selectedDate->copy()->endOfMonth()->endOfDay(),
            ],
        };

        $chart = DashboardSalesChart::forRange($start, $end);
        $peakHour = $chart['peak_hour'];

        return $this->ok([
            'periode' => $periode,
            'start' => $start->toIso8601String(),
            'end' => $end->toIso8601String(),
            'sales' => $chart['sales'],
            'volume' => $chart['volume'],
            'peak_hour' => $peakHour,
            'peak_hour_label' => $peakHour !== null
                ? sprintf('%02d:00 - %02d:00 WIB', $peakHour, ($peakHour + 1) % 24)
                : 'Belum ada data',
            'updated_at' => $chart['updated_at'],
        ], 'Grafik penjualan per jam.');
    }

    private function adminSummary(Carbon $now, Carbon $todayStart, Carbon $todayEnd): array
    {
        $totalPenjualan = (float) Transaksi::whereBetween('waktu_transaksi', [$todayStart, $todayEnd])
            ->where('status_pesanan', '!=', 'cancelled')
            ->sum('total_harga');

        $totalPesanan = Transaksi::whereBetween('waktu_transaksi', [$todayStart, $todayEnd])
            ->where('status_pesanan', 'completed')
            ->count();

        $totalMeja = Meja::count();
        $mejaTerisi = Transaksi::whereIn('status_pesanan', ['pending', 'ready', 'prosesed'])
            ->whereNotNull('meja_id')
            ->distinct('meja_id')
            ->count('meja_id');
        $okupansi = $totalMeja > 0 ? round(($mejaTerisi / $totalMeja) * 100) : 0;

        $stokMenipis = Produk::where('stock', '<', 10)->count();

        $transaksiTerbaru = Transaksi::with(['meja', 'karyawan', 'detail_transaksis.produk', 'pembayaran', 'promo'])
            ->latest('waktu_transaksi')
            ->limit(5)
            ->get()
            ->map(fn (Transaksi $transaksi) => ApiPresenter::transaksi($transaksi))
            ->values();

        $topCategories = DetailTransaksi::join('produks', 'detail_transaksis.produk_id', '=', 'produks.id')
            ->join('kategori_produks', 'produks.kategori_produk_id', '=', 'kategori_produks.id')
            ->join('transaksis', 'detail_transaksis.transaksi_id', '=', 'transaksis.id')
            ->whereBetween('transaksis.waktu_transaksi', [$todayStart, $todayEnd])
            ->where('transaksis.status_pesanan', '!=', 'cancelled')
            ->selectRaw('kategori_produks.nama_kategori, SUM(detail_transaksis.QTY) as total_qty, SUM(detail_transaksis.subtotal) as total_revenue')
            ->groupBy('kategori_produks.nama_kategori')
            ->orderByDesc('total_qty')
            ->limit(4)
            ->get()
            ->map(fn ($row) => [
                'nama_kategori' => $row->nama_kategori,
                'total_qty' => (int) $row->total_qty,
                'total_revenue' => (float) $row->total_revenue,
            ])
            ->values();

        $metodeTerbanyak = Pembayaran::query()
            ->whereHas('transaksi', fn ($query) => $query
                ->whereBetween('waktu_transaksi', [$todayStart, $todayEnd])
                ->where('status_pesanan', '!=', 'cancelled'))
            ->selectRaw('metode_pembayaran, COUNT(*) as jumlah')
            ->groupBy('metode_pembayaran')
            ->orderByDesc('jumlah')
            ->value('metode_pembayaran') ?? 'Belum ada data';

        return [
            'penjualan_hari_ini' => $totalPenjualan,
            'pesanan_selesai_hari_ini' => $totalPesanan,
            'meja' => [
                'total' => $totalMeja,
                'terisi' => $mejaTerisi,
                'okupansi_persen' => $okupansi,
            ],
            'stok_menipis' => $stokMenipis,
            'metode_pembayaran_terbanyak' => $metodeTerbanyak,
            'transaksi_terbaru' => $transaksiTerbaru,
            'kategori_teratas' => $topCategories,
            'grafik_penjualan' => DashboardSalesChart::forDay($now),
        ];
    }

    private function managerSummary($user, Carbon $now, Carbon $todayStart, Carbon $todayEnd): array
    {
        $monthStart = $now->copy()->startOfMonth()->utc();
        $monthEnd = $now->copy()->endOfMonth()->utc();
        $prevStart = $now->copy()->subMonthNoOverflow()->startOfMonth()->utc();
        $prevEnd = $now->copy()->subMonthNoOverflow()->endOfMonth()->utc();

        $totalPendapatanHariIni = (float) Transaksi::whereBetween('waktu_transaksi', [$todayStart, $todayEnd])
            ->where('status_pesanan', '!=', 'cancelled')
            ->sum('total_harga');
        $totalTransaksiHariIni = Transaksi::whereBetween('waktu_transaksi', [$todayStart, $todayEnd])
            ->where('status_pesanan', '!=', 'cancelled')
            ->count();
        $totalItemHariIni = (int) DetailTransaksi::whereHas('transaksi', fn ($query) => $query
            ->whereBetween('waktu_transaksi', [$todayStart, $todayEnd])
            ->where('status_pesanan', '!=', 'cancelled'))
            ->sum('QTY');

        $totalPendapatanBulanIni = (float) Transaksi::whereBetween('waktu_transaksi', [$monthStart, $monthEnd])
            ->where('status_pesanan', '!=', 'cancelled')
            ->sum('total_harga');
        $totalTransaksiBulanIni = Transaksi::whereBetween('waktu_transaksi', [$monthStart, $monthEnd])
            ->where('status_pesanan', '!=', 'cancelled')
            ->count();
        $averageTransactionValue = $totalTransaksiBulanIni > 0
            ? round($totalPendapatanBulanIni / $totalTransaksiBulanIni)
            : 0;

        $pendapatanSebelumnya = (float) Transaksi::whereBetween('waktu_transaksi', [$prevStart, $prevEnd])
            ->where('status_pesanan', '!=', 'cancelled')
            ->sum('total_harga');
        $growth = $pendapatanSebelumnya > 0
            ? round((($totalPendapatanBulanIni - $pendapatanSebelumnya) / $pendapatanSebelumnya) * 100, 1)
            : null;

        $produkTerlaris = DetailTransaksi::query()
            ->with('produk.kategoriProduk')
            ->select('produk_id', DB::raw('SUM(QTY) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('transaksi', fn ($query) => $query
                ->whereBetween('waktu_transaksi', [$monthStart, $monthEnd])
                ->where('status_pesanan', '!=', 'cancelled'))
            ->groupBy('produk_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'produk_id' => $row->produk_id,
                'nama_produk' => $row->produk?->nama_produk ?? 'Produk dihapus',
                'kategori' => $row->produk?->kategoriProduk?->nama_kategori ?? 'N/A',
                'total_qty' => (int) $row->total_qty,
                'total_revenue' => (float) $row->total_revenue,
            ])
            ->values();

        $paymentStats = Pembayaran::query()
            ->whereHas('transaksi', fn ($query) => $query
                ->whereBetween('waktu_transaksi', [$monthStart, $monthEnd])
                ->where('status_pesanan', '!=', 'cancelled'))
            ->selectRaw('metode_pembayaran, COUNT(*) as jumlah, SUM(jumlah_bayar) as total')
            ->groupBy('metode_pembayaran')
            ->get()
            ->map(fn ($row) => [
                'metode' => $row->metode_pembayaran,
                'jumlah' => (int) $row->jumlah,
                'total' => (float) $row->total,
            ])
            ->values();

        return [
            'hari_ini' => [
                'pendapatan' => $totalPendapatanHariIni,
                'transaksi' => $totalTransaksiHariIni,
                'item' => $totalItemHariIni,
            ],
            'bulan_ini' => [
                'pendapatan' => $totalPendapatanBulanIni,
                'transaksi' => $totalTransaksiBulanIni,
                'rata_rata_transaksi' => $averageTransactionValue,
                'growth_persen' => $growth,
                'pendapatan_bulan_lalu' => $pendapatanSebelumnya,
            ],
            'produk_terlaris' => $produkTerlaris,
            'pembayaran' => $paymentStats,
            'grafik_penjualan' => DashboardSalesChart::forDay($now),
        ];
    }

    private function kasirSummary($user, Carbon $todayStart, Carbon $todayEnd): array
    {
        $activeOrders = Transaksi::with(['meja', 'karyawan', 'detail_transaksis.produk', 'pembayaran', 'promo'])
            ->whereIn('status_pesanan', ['pending', 'ready', 'prosesed'])
            ->orderBy('waktu_transaksi')
            ->orderBy('id')
            ->get();

        $transaksiTerbaru = Transaksi::with(['meja', 'karyawan', 'detail_transaksis.produk', 'pembayaran', 'promo'])
            ->where(function ($query) use ($user) {
                $query->where('karyawan_id', $user->id)
                    ->orWhere('sumber_pesanan', 'web');
            })
            ->latest('waktu_transaksi')
            ->limit(10)
            ->get()
            ->map(fn (Transaksi $transaksi) => ApiPresenter::transaksi($transaksi))
            ->values();

        $totalMeja = Meja::count();
        $mejaTerisi = $activeOrders->pluck('meja_id')->filter()->unique()->count();

        return [
            'antrean' => [
                'total' => $activeOrders->count(),
                'pending' => $activeOrders->where('status_pesanan', 'pending')->count(),
                'prosesed' => $activeOrders->where('status_pesanan', 'prosesed')->count(),
                'ready' => $activeOrders->where('status_pesanan', 'ready')->count(),
                'orders' => $activeOrders->map(fn (Transaksi $order) => ApiPresenter::transaksi($order))->values(),
            ],
            'meja' => [
                'total' => $totalMeja,
                'terisi' => $mejaTerisi,
                'tersedia' => max(0, $totalMeja - $mejaTerisi),
            ],
            'transaksi_terbaru' => $transaksiTerbaru,
            'penjualan_hari_ini' => (float) Transaksi::whereBetween('waktu_transaksi', [$todayStart, $todayEnd])
                ->where('status_pesanan', '!=', 'cancelled')
                ->where(function ($query) use ($user) {
                    $query->where('karyawan_id', $user->id)
                        ->orWhere('sumber_pesanan', 'web');
                })
                ->sum('total_harga'),
        ];
    }
}
