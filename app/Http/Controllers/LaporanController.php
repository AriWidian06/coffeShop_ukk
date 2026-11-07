<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function export(Request $request)
    {
        $tz = 'Asia/Jakarta';
        Carbon::setLocale('id');

        $user = auth('karyawan')->user();
        $isManager = strtolower((string) ($user->role ?? '')) === 'manager';
        $nowWib = Carbon::now($tz);

        if ($isManager) {
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
                $periodeLabel = $start->translatedFormat('d F Y');
            } elseif ($periode === 'mingguan') {
                $start = $selectedDate->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
                $end = $selectedDate->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                $periodeLabel = $start->translatedFormat('d M') . ' - ' . $end->translatedFormat('d M Y');
            } else {
                $start = $selectedDate->copy()->startOfMonth()->startOfDay();
                $end = $selectedDate->copy()->endOfMonth()->endOfDay();
                $periodeLabel = $start->translatedFormat('F Y');
            }

            $fileTag = ucfirst($periode) . '_' . $selectedDate->toDateString();
        } else {
            // Admin: default bulan berjalan (WIB), tetap mendukung rentang kustom.
            if ($request->filled('start_date') || $request->filled('end_date')) {
                $startInput = $request->input('start_date', $nowWib->copy()->startOfMonth()->toDateString());
                $endInput = $request->input('end_date', $nowWib->copy()->endOfMonth()->toDateString());

                try {
                    $start = Carbon::parse($startInput, $tz)->startOfDay();
                    $end = Carbon::parse($endInput, $tz)->endOfDay();
                } catch (\Throwable $e) {
                    $start = $nowWib->copy()->startOfMonth()->startOfDay();
                    $end = $nowWib->copy()->endOfMonth()->endOfDay();
                }

                if ($end->lt($start)) {
                    [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
                }

                $periodeLabel = $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y');
            } else {
                $start = $nowWib->copy()->startOfMonth()->startOfDay();
                $end = $nowWib->copy()->endOfMonth()->endOfDay();
                $periodeLabel = $start->translatedFormat('F Y');
            }

            $fileTag = $start->format('Y-m-d') . '_sd_' . $end->format('Y-m-d');
        }

        $startUtc = $start->copy()->utc();
        $endUtc = $end->copy()->utc();

        $transaksis = Transaksi::with(['karyawan', 'meja', 'detail_transaksis.produk.kategoriProduk', 'pembayaran'])
            ->whereBetween('waktu_transaksi', [$startUtc, $endUtc])
            ->orderBy('waktu_transaksi', 'desc')
            ->get();

        $totalPendapatan = (float) $transaksis->sum('total_harga');
        $totalTransaksi = $transaksis->count();
        $totalDiskon = (float) $transaksis->sum('diskon');
        $totalItem = (int) $transaksis->flatMap->detail_transaksis->sum('QTY');
        $averageTransactionValue = $totalTransaksi > 0 ? round($totalPendapatan / $totalTransaksi) : 0;

        $paymentStats = $transaksis
            ->groupBy(fn ($transaksi) => $transaksi->pembayaran?->metode_pembayaran ?: 'Belum Lunas')
            ->map(fn ($group, $metode) => (object) [
                'metode' => $metode,
                'jumlah' => $group->count(),
                'total' => (float) $group->sum('total_harga'),
            ])
            ->sortByDesc('total')
            ->values();

        $topProducts = $transaksis
            ->flatMap->detail_transaksis
            ->groupBy('produk_id')
            ->map(fn ($details) => (object) [
                'nama_produk' => $details->first()->produk?->nama_produk ?? 'Produk dihapus',
                'kategori' => $details->first()->produk?->kategoriProduk?->nama_kategori ?? 'N/A',
                'total_qty' => (int) $details->sum('QTY'),
                'total_revenue' => (float) $details->sum('subtotal'),
            ])
            ->sortByDesc('total_qty')
            ->take(5)
            ->values();

        $hourlySales = array_fill(0, 24, 0.0);
        foreach ($transaksis as $transaksi) {
            if ($transaksi->waktu_transaksi) {
                $hour = (int) $transaksi->waktu_transaksi->copy()->timezone('Asia/Jakarta')->format('G');
                $hourlySales[$hour] += (float) $transaksi->total_harga;
            }
        }
        $maxHourlySales = max(1.0, ...$hourlySales);
        $totalPembayaran = (float) $paymentStats->sum('total');

        $logoPath = public_path('images/logo.jpeg');
        $logoData = is_file($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        $statusLabels = [
            'pending' => 'Menunggu',
            'prosesed' => 'Diproses',
            'ready' => 'Siap',
            'completed' => 'Selesai',
        ];

        $html = view('laporan.pdf', [
            'transaksis' => $transaksis,
            'totalPendapatan' => $totalPendapatan,
            'totalTransaksi' => $totalTransaksi,
            'totalDiskon' => $totalDiskon,
            'totalItem' => $totalItem,
            'averageTransactionValue' => $averageTransactionValue,
            'paymentStats' => $paymentStats,
            'hourlySales' => $hourlySales,
            'maxHourlySales' => $maxHourlySales,
            'totalPembayaran' => $totalPembayaran,
            'topProducts' => $topProducts,
            'periodeLabel' => $periodeLabel,
            'start' => $start,
            'end' => $end,
            'nowWib' => $nowWib,
            'logoData' => $logoData,
            'statusLabels' => $statusLabels,
            'dicetakOleh' => $user?->nama_karyawan ?? 'Sistem',
            'isManager' => $isManager,
        ])->render();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $pageFont = $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');
        $canvas->page_text(730, 575, 'Halaman {PAGE_NUM} dari {PAGE_COUNT}', $pageFont, 7, [0.45, 0.45, 0.45]);

        $filename = 'Laporan_BagiKopi_' . $fileTag . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }
}
