<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    /**
     * Membuat pesanan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'meja_id' => 'required|exists:mejas,id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'tipe_pesanan' => 'required|in:dine-in,take-away',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $transaksi = DB::transaction(function () use ($validated) {
            $totalHarga = 0;
            $detailItems = [];

            foreach ($validated['items'] as $item) {
                $produk = Produk::where('status_aktif', true)
                    ->where('tipe', 'jual')
                    ->lockForUpdate()
                    ->find($item['produk_id']);

                if (! $produk) {
                    throw ValidationException::withMessages([
                        'items' => 'Produk tidak aktif atau tidak dapat dipesan.',
                    ]);
                }

                if ($produk->stock < $item['qty']) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$produk->nama_produk} tidak mencukupi.",
                    ]);
                }

                $subtotal = $produk->harga_jual * $item['qty'];
                $totalHarga += $subtotal;

                $detailItems[] = [
                    'produk_id' => $produk->id,
                    'QTY' => $item['qty'],
                    'subtotal' => $subtotal,
                ];

                $produk->decrement('stock', $item['qty']);
            }

            $transaksi = Transaksi::create([
                'meja_id' => $validated['meja_id'],
                'karyawan_id' => $validated['karyawan_id'],
                'tipe_pesanan' => $validated['tipe_pesanan'],
                'total_harga' => $totalHarga,
                'status_pesanan' => 'pending',
                'waktu_transaksi' => now(),
            ]);

            foreach ($detailItems as $detail) {
                $detail['transaksi_id'] = $transaksi->id;
                DetailTransaksi::create($detail);
            }

            return $transaksi;
        });

        $transaksi->load(['meja', 'karyawan', 'detail_transaksis.produk']);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat.',
            'data' => $transaksi,
        ], 201);
    }
}
