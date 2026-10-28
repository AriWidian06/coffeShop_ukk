<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Services\PaymentService;
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
            'metode_pembayaran' => 'nullable|in:QRIS,tunai',
            'catatan' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|integer|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.customization' => 'nullable|string|max:250',
        ]);

        $result = DB::transaction(function () use ($validated) {
            // Tolak meja yang sudah punya pesanan aktif (diproses kasir)
            if (!empty($validated['meja_id'])) {
                $mejaTerisi = Transaksi::query()
                    ->where('meja_id', $validated['meja_id'])
                    ->whereIn('status_pesanan', ['pending', 'ready', 'prosesed'])
                    ->lockForUpdate()
                    ->first() !== null;

                if ($mejaTerisi) {
                    throw ValidationException::withMessages([
                        'meja_id' => 'Meja ini sudah memiliki pesanan aktif. Silakan pilih meja lain.',
                    ]);
                }
            }

            $subtotal = 0;
            $items = [];
            $productsById = [];
            $quantitiesByProduct = [];

            foreach ($validated['items'] as $item) {
                $produkId = $item['produk_id'];
                $produk = $productsById[$produkId] ??= Produk::query()
                    ->where('status_aktif', true)
                    ->where('tipe', 'jual')
                    ->lockForUpdate()
                    ->find($produkId);

                if (!$produk) {
                    throw ValidationException::withMessages([
                        'items' => 'Salah satu menu sudah tidak tersedia.',
                    ]);
                }

                // Hitung harga tambahan kostumisasi (opsi) agar sama dengan keranjang
                $extraPrice = 0;
                if (!empty($item['customization'])) {
                    foreach (explode(', ', $item['customization']) as $optText) {
                        $optName = explode(' (+', $optText)[0];
                        $opsi = \App\Models\ProdukOpsi::where('produk_id', $produk->id)
                            ->where('nama_opsi', 'like', '%' . $optName . '%')
                            ->first();
                        if ($opsi) {
                            $extraPrice += $opsi->harga_tambahan;
                        }
                    }
                }

                $lineTotal = round(((float) $produk->harga_jual + $extraPrice) * $item['qty'], 2);
                $subtotal += $lineTotal;
                $quantitiesByProduct[$produkId] = ($quantitiesByProduct[$produkId] ?? 0) + $item['qty'];
                $items[] = [
                    'produk' => $produk,
                    'qty' => $item['qty'],
                    'subtotal' => $lineTotal,
                    'customization' => $item['customization'] ?? null,
                ];
            }

            foreach ($quantitiesByProduct as $produkId => $quantity) {
                $produk = $productsById[$produkId];
                if ($produk->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$produk->nama_produk} tidak mencukupi.",
                    ]);
                }
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

            foreach ($quantitiesByProduct as $produkId => $quantity) {
                $productsById[$produkId]->decrement('stock', $quantity);
            }

            foreach ($items as $item) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk']->id,
                    'QTY' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                    'customization' => $item['customization'] ?? null,
                ]);
            }

            return [
                'transaksi' => $transaksi,
                'subtotal' => $subtotal,
                'pajak' => $pajak,
            ];
        });

        $data = [
            'id' => $result['transaksi']->id,
            'waktu_transaksi' => $result['transaksi']->waktu_transaksi->toIso8601String(),
            'subtotal' => $result['subtotal'],
            'pajak' => $result['pajak'],
            'total_harga' => (float) $result['transaksi']->total_harga,
        ];

        if (($validated['metode_pembayaran'] ?? null) === 'QRIS') {
            try {
                $data['qris'] = (new PaymentService())->createQrisCharge($result['transaksi']);
            } catch (\Exception $e) {
                $data['qris_error'] = 'QRIS otomatis gagal dibuat. Silakan bayar di kasir.';
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dikirim ke kasir.',
            'data' => $data,
        ], 201);
    }

    public function checkStatus(Request $request, $id, PaymentService $paymentService)
    {
        $transaksi = Transaksi::findOrFail($id);

        $validated = $request->validate([
            'order_id' => 'required|string',
        ]);

        if (!str_starts_with($validated['order_id'], 'TRX-' . $transaksi->id . '-')) {
            return response()->json(['success' => false, 'message' => 'Order ID tidak valid.'], 422);
        }

        $status = $paymentService->syncStatus($transaksi, $validated['order_id']);

        return response()->json([
            'success' => true,
            'paid' => in_array($status, ['capture', 'settlement'], true),
            'status' => $status,
        ]);
    }
}
