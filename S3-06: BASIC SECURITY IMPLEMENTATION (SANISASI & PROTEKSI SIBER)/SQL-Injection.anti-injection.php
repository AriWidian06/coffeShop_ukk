<?php

/**
 * Excerpt dari: app/Http/Controllers/DashboardController.php
 * dan routes/web.php (agregat)
 * Fokus: query builder/Eloquent aman dan raw yang tetap statis.
 */

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth('karyawan')->user()->role;

        // AMAN: Eloquent Parameter Binding (input tidak dikonkatenasi).
        $totalPendapatanHariIni = Transaksi::whereDate('waktu_transaksi', Carbon::today())
            ->sum('total_harga');

        // AMAN: selectRaw hanya untuk ekspresi SQL statis, bukan input user.
        $transaksiPerJam = Transaksi::selectRaw('HOUR(waktu_transaksi) as jam, COUNT(*) as jumlah')
            ->whereDate('waktu_transaksi', Carbon::today())
            ->groupBy('jam')
            ->orderBy('jam')
            ->get();

        // AMAN: DB::raw hanya dipakai untuk aggregate statis.
        $produkTerlaris = DetailTransaksi::select('produk_id', DB::raw('SUM(QTY) as total_qty'))
            ->whereHas('transaksi', function ($query) {
                $query->whereDate('waktu_transaksi', Carbon::today());
            })
            ->groupBy('produk_id')
            ->orderByDesc('total_qty')
            ->with('produk')
            ->first();

        return view('dashboard.manager', compact(
            'totalPendapatanHariIni',
            'transaksiPerJam',
            'produkTerlaris'
        ));
    }
}

// Contoh aman binding manual jika memerlukan SQL raw:
// $user = DB::select('SELECT * FROM users WHERE email = :email AND status = :status', [
//     'email'  => $request->input('email'),
//     'status' => 'ACTIVE',
// ]);

// POLA BERBAHAYA (JANGAN DIPAKAI):
// $query = "SELECT * FROM users WHERE email = '" . $email . "'";
// $user = DB::select($query);
```