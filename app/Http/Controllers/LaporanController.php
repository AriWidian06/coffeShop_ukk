<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function export(Request $request)
    {
        // 1. Tentukan rentang tanggal (Default: Bulan Ini)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 2. Ambil data transaksi beserta relasinya
        $transaksis = Transaksi::with(['karyawan', 'meja', 'detail_transaksis.produk', 'pembayaran'])
            ->whereBetween('waktu_transaksi', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('waktu_transaksi', 'desc')
            ->get();

        // 3. Nama file dinamis
        $filename = 'Laporan_Transaksi_' . $startDate . '_sd_' . $endDate . '.csv';

        // 4. Header untuk memaksa browser mendownload file CSV
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        // 5. Proses streaming data ke CSV
        $callback = function() use ($transaksis) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan BOM agar Excel membaca karakter UTF-8 (seperti 'Rp') dengan benar
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header Kolom CSV
            fputcsv($file, [
                'ID Transaksi',
                'Tanggal & Waktu',
                'Tipe Pesanan',
                'Meja',
                'Kasir / Sumber',
                'Total Item',
                'Total Harga',
                'Status Pesanan',
                'Status Pembayaran'
            ]);

            // Isi Data Baris per Baris
            foreach ($transaksis as $transaksi) {
                // PERBAIKAN: Karena relasinya hasMany, QTY diakses langsung, BUKAN lewat ->pivot
                $totalItem = 0;
                foreach ($transaksi->detail_transaksis as $detail) {
                    // Gunakan ?? untuk fallback jika nama kolom di DB adalah 'qty' (huruf kecil) atau 'QTY' (huruf besar)
                    $totalItem += ($detail->QTY ?? $detail->qty ?? 0);
                }

                fputcsv($file, [
                    $transaksi->id,
                    $transaksi->waktu_transaksi ? $transaksi->waktu_transaksi->format('d/m/Y H:i') : '-',
                    $transaksi->tipe_pesanan === 'take-away' ? 'Bawa Pulang' : 'Makan di Tempat',
                    $transaksi->meja ? 'Meja ' . $transaksi->meja->nomor_meja : 'Tanpa Meja',
                    $transaksi->karyawan ? $transaksi->karyawan->nama_karyawan : ($transaksi->sumber_pesanan ?? 'Web Order'),
                    $totalItem,
                    'Rp ' . number_format($transaksi->total_harga, 0, ',', '.'),
                    ucfirst($transaksi->status_pesanan),
                    $transaksi->pembayaran ? 'Lunas' : 'Belum Lunas'
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}