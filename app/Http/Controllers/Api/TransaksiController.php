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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'meja_id' => 'nullable|required_if:tipe_pesanan,dine-in|exists:mejas,id',
            'tipe_pesanan' => 'required|in:dine-in,take-away',
            'catatan' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|integer|distinct|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $result = DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $items = [];

            foreach ($validated['items'] as $item) {
                $produk = Produk::query()
                    ->where('status_aktif', true)
                    ->where('tipe', 'jual')
                    ->lockForUpdate()
                    ->find($item['produk_id']);

                if (!$produk) {
                    throw ValidationException::withMessages([
                        'items' => 'Salah satu menu sudah tidak tersedia.',
                    ]);
                }

                if ($produk->stock < $item['qty']) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$produk->nama_produk} tidak mencukupi.",
                    ]);
                }

                $lineTotal = round((float) $produk->harga_jual * $item['qty'], 2);
                $subtotal += $lineTotal;
                $items[] = [
                    'produk' => $produk,
                    'qty' => $item['qty'],
                    'subtotal' => $lineTotal,
                ];
            }

            $pajak = round($subtotal * 0.1, 2);
            $transaksi = Transaksi::create([
                'meja_id' => $validated['meja_id'] ?? null,
                'karyawan_id' => null,
                'sumber_pesanan' => 'web',
                'catatan' => $validated['catatan'] ?? null,
                'tipe_pesanan' => $validated['tipe_pesanan'],
                'total_harga' => $subtotal + $pajak,
                'status_pesanan' => 'pending',
                'waktu_transaksi' => now(),
            ]);

            foreach ($items as $item) {
                $item['produk']->decrement('stock', $item['qty']);
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk']->id,
                    'QTY' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return [
                'transaksi' => $transaksi,
                'subtotal' => $subtotal,
                'pajak' => $pajak,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dikirim ke kasir.',
            'data' => [
                'id' => $result['transaksi']->id,
                'waktu_transaksi' => $result['transaksi']->waktu_transaksi->toIso8601String(),
                'subtotal' => $result['subtotal'],
                'pajak' => $result['pajak'],
                'total_harga' => (float) $result['transaksi']->total_harga,
            ],
        ], 201);
    }
}
