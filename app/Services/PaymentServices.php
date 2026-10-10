<?php

namespace App\Services;

use App\Models\Transaksi;
use App\Models\Pembayaran;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function confirmPayment(int $idTransaksi, array $paymentData)
    {
        DB::beginTransaction();

        try {
            $transaksi = Transaksi::with('detail_transaksi')->findOrFail($idTransaksi);


            Pembayaran::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'metode_pembayaran' => $paymentData['metode_pembayaran'],
                'jumlah_bayar' => $paymentData['jumlah_bayar'],
                'kembalian' => $paymentData['jumlah_bayar'] - $transaksi->total_harga,
                'status_pembayaran' => 'success'
            ]);


            foreach ($transaksi->detail_transaksi as $detail) {
                Produk::where('id_produk', $detail->id_produk)
                    ->decrement('stok', $detail->qty);
            }

            $transaksi->update(['status_pesanan' => 'processed']);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack(); 
            throw $e;
        }
    }
}