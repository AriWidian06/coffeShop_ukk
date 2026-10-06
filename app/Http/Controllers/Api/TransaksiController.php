<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function store(StoreTransactionRequest $validated)
    {
        DB::beginTransaction();

        try {
            foreach ($validated->items as $item) {
                $produk = Produk::findOrFail($item['id_produk']);
                if ($produk->stok < $item['qty']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok produk {$produk->nama_produk} tidak mencukupi."
                    ], 422);
                }
            }

            $transaksi = Transaksi::create([
                'id_meja' => $validated->id_meja,
                'id_karyawan' => auth()->id() ?? null,
                'tipe_pesanan' => $validated->tipe_pesanan,
                'total_harga' => $validated->total_harga,
                'status_pesanan' => 'pending'
            ]);

            foreach ($validated->items as $item) {
                $produk = Produk::findOrFail($item['id_produk']);
                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_produk' => $item['id_produk'],
                    'qty' => $item['qty'],
                    'subtotal' => $produk->harga * $item['qty']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat.',
                'data' => $transaksi->load('detail_transaksi')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses transaksi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
