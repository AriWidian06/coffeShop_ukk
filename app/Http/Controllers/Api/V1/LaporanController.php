<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    use ApiResponse;

    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'periode' => ['nullable', 'in:harian,mingguan,bulanan'],
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        [$start, $end, $periode, $label] = $this->resolvePeriod($request);

        $transaksis = Transaksi::with([
            'karyawan',
            'meja',
            'detail_transaksis.produk.kategoriProduk',
            'pembayaran',
        ])
            ->whereBetween('waktu_transaksi', [$start->copy()->utc(), $end->copy()->utc()])
            ->where('status_pesanan', '!=', 'cancelled')
            ->orderByDesc('waktu_transaksi')
            ->get();

        $totalPendapatan = (float) $transaksis->sum('total_harga');
        $totalTransaksi = $transaksis->count();
        $totalDiskon = (float) $transaksis->sum('diskon');
        $totalItem = (int) $transaksis->flatMap->detail_transaksis->sum('QTY');
        $averageTransactionValue = $totalTransaksi > 0 ? round($totalPendapatan / $totalTransaksi) : 0;

        $paymentStats = $transaksis
            ->groupBy(fn (Transaksi $transaksi) => $transaksi->pembayaran?->metode_pembayaran ?: 'Belum Lunas')
            ->map(fn ($group, $metode) => [
                'metode' => $metode,
                'jumlah' => $group->count(),
                'total' => (float) $group->sum('total_harga'),
            ])
            ->sortByDesc('total')
            ->values();

        $topProducts = $transaksis
            ->flatMap->detail_transaksis
            ->groupBy('produk_id')
            ->map(fn ($details) => [
                'produk_id' => $details->first()->produk_id,
                'nama_produk' => $details->first()->produk?->nama_produk ?? 'Produk dihapus',
                'kategori' => $details->first()->produk?->kategoriProduk?->nama_kategori ?? 'N/A',
                'total_qty' => (int) $details->sum('QTY'),
                'total_revenue' => (float) $details->sum('subtotal'),
            ])
            ->sortByDesc('total_qty')
            ->take(10)
            ->values();

        $hourlySales = array_fill(0, 24, 0.0);
        foreach ($transaksis as $transaksi) {
            if ($transaksi->waktu_transaksi) {
                $hour = (int) $transaksi->waktu_transaksi->copy()->timezone('Asia/Jakarta')->format('G');
                $hourlySales[$hour] += (float) $transaksi->total_harga;
            }
        }

        $peakHour = null;
        if (max($hourlySales) > 0) {
            $peakHour = (int) array_search(max($hourlySales), $hourlySales, true);
        }

        return $this->ok([
            'periode' => [
                'tipe' => $periode,
                'label' => $label,
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
                'timezone' => 'Asia/Jakarta',
            ],
            'ringkasan' => [
                'total_pendapatan' => $totalPendapatan,
                'total_transaksi' => $totalTransaksi,
                'total_item' => $totalItem,
                'total_diskon' => $totalDiskon,
                'rata_rata_transaksi' => $averageTransactionValue,
            ],
            'pembayaran' => $paymentStats,
            'produk_terlaris' => $topProducts,
            'penjualan_per_jam' => $hourlySales,
            'jam_teramai' => $peakHour,
        ], 'Ringkasan laporan penjualan.');
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string, 3: string}
     */
    private function resolvePeriod(Request $request): array
    {
        $tz = 'Asia/Jakarta';
        Carbon::setLocale('id');
        $now = Carbon::now($tz);

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $start = Carbon::parse($request->input('start_date', $now->copy()->startOfMonth()->toDateString()), $tz)->startOfDay();
            $end = Carbon::parse($request->input('end_date', $now->copy()->endOfMonth()->toDateString()), $tz)->endOfDay();

            if ($end->lt($start)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }

            return [$start, $end, 'kustom', $start->translatedFormat('d M Y').' - '.$end->translatedFormat('d M Y')];
        }

        $periode = $request->input('periode', 'bulanan');
        if (! in_array($periode, ['harian', 'mingguan', 'bulanan'], true)) {
            $periode = 'bulanan';
        }

        $selectedDate = $now->copy();
        if ($request->filled('tanggal')) {
            $selectedDate = Carbon::parse($request->input('tanggal'), $tz);
        }

        if ($periode === 'harian') {
            $start = $selectedDate->copy()->startOfDay();
            $end = $selectedDate->copy()->endOfDay();
            $label = $start->translatedFormat('d F Y');
        } elseif ($periode === 'mingguan') {
            $start = $selectedDate->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
            $end = $selectedDate->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
            $label = $start->translatedFormat('d M').' - '.$end->translatedFormat('d M Y');
        } else {
            $start = $selectedDate->copy()->startOfMonth()->startOfDay();
            $end = $selectedDate->copy()->endOfMonth()->endOfDay();
            $label = $start->translatedFormat('F Y');
        }

        return [$start, $end, $periode, $label];
    }
}
