<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'transaksi_id' => 'required|exists:transaksis,id',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:cash,QRIS',
        ]);

        $transaksi = Transaksi::findOrFail($request->transaksi_id);

        if ($request->jumlah_bayar < $transaksi->total_harga) {
            return back()->withErrors(['jumlah_bayar' => 'Jumlah pembayaran kurang dari total harga!']);
        }

        $kembalian = $request->metode_pembayaran === 'cash' ? ($request->jumlah_bayar - $transaksi->total_harga) : 0;

        $pembayaran = Pembayaran::create([
            'transaksi_id' => $transaksi->id,
            'jumlah_bayar' => $request->jumlah_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status_pembayaran' => 'paid',
            'kembalian' => $kembalian,
            'waktu_pembayaran' => now(),
        ]);

        $transaksi->update(['status_pesanan' => 'completed']);

        return redirect()->route('transaksis.show', $transaksi->id)
            ->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function printReceipt(Transaksi $transaksi)
    {
        $transaksi->load(['meja', 'karyawan', 'detail_transaksis.produk', 'pembayaran']);
        return view('transaksis.receipt', compact('transaksi'));
    }
}
