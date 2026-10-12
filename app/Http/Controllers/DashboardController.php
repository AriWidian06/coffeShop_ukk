<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth('karyawan')->user()->role;

        // ==========================================
        // 1. DATA UNTUK MANAGER DASHBOARD
        // ==========================================
        if ($role === 'manager') {
            $today = Carbon::today();
            
            // Hitung pendapatan & transaksi hari ini
            $totalPendapatanHariIni = Transaksi::whereDate('waktu_transaksi', $today)->sum('total_harga');
            $totalTransaksiHariIni = Transaksi::whereDate('waktu_transaksi', $today)->count();

            // Produk terlaris hari ini (Menggunakan QTY huruf besar sesuai struktur DB kamu)
            $produkTerlaris = DetailTransaksi::select('produk_id', DB::raw('SUM(QTY) as total_qty'))
                ->whereHas('transaksi', function($query) use ($today) {
                    $query->whereDate('waktu_transaksi', $today);
                })
                ->groupBy('produk_id')
                ->orderByDesc('total_qty')
                ->with('produk')
                ->first();

            // Pendapatan & Transaksi Bulan Ini
            $totalPendapatanBulanIni = Transaksi::whereMonth('waktu_transaksi', $today->month)
                ->whereYear('waktu_transaksi', $today->year)
                ->sum('total_harga');

            $totalTransaksiBulanIni = Transaksi::whereMonth('waktu_transaksi', $today->month)
                ->whereYear('waktu_transaksi', $today->year)
                ->count();

            // Rata-rata Nilai Transaksi (Average Transaction Value)
            $averageTransactionValue = $totalTransaksiHariIni > 0 
                ? ($totalPendapatanHariIni / $totalTransaksiHariIni) 
                : 0;

            // Grafik Transaksi per Jam
            $transaksiPerJam = Transaksi::selectRaw('HOUR(waktu_transaksi) as jam, COUNT(*) as jumlah')
                ->whereDate('waktu_transaksi', $today)
                ->groupBy('jam')
                ->orderBy('jam')
                ->get();

            return view('dashboard.manager', compact(
                'totalPendapatanHariIni', 
                'totalTransaksiHariIni', 
                'produkTerlaris',
                'totalPendapatanBulanIni', 
                'totalTransaksiBulanIni', 
                'averageTransactionValue', 
                'transaksiPerJam'
            ));
        }

        // ==========================================
        // 2. DATA UNTUK KASIR DASHBOARD
        // ==========================================
        if ($role === 'kasir') {
            // PERBAIKAN: Menggunakan 'detail_transaksis' (snake_case) sesuai nama fungsi di Model Transaksi
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

        // ==========================================
        // 3. DATA UNTUK ADMIN DASHBOARD
        // ==========================================
        return view('dashboard.admin');
    }
}