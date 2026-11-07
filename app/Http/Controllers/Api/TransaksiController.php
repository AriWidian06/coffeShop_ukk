<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\PromoKode;
use App\Models\PromoUsage;
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
            'session_id' => 'nullable|string|max:128',
            'kode_promo' => 'nullable|string|max:50',
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

            $promo = null;
            if (!empty($validated['kode_promo'])) {
                $promo = PromoKode::query()
                    ->where('kode', PromoKode::normalized($validated['kode_promo']))
                    ->lockForUpdate()
                    ->first();

                if (!$promo) {
                    throw ValidationException::withMessages([
                        'kode_promo' => 'Kode promo tidak ditemukan.',
                    ]);
                }
            }

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

            $mejaId = !empty($validated['meja_id']) ? (int) $validated['meja_id'] : null;
            $sessionId = trim((string) ($validated['session_id'] ?? ''));
            if ($sessionId === '') {
                $sessionId = null;
            }

            $diskon = 0;
            if ($promo) {
                $eligibility = $promo->eligibleFor($subtotal, $mejaId, $sessionId);
                if (!$eligibility['ok']) {
                    throw ValidationException::withMessages([
                        'kode_promo' => $eligibility['message'],
                    ]);
                }
                $diskon = $eligibility['diskon'];
            }

            $pajak = round($subtotal * 0.1, 2);
            $totalHarga = max(0, round($subtotal + $pajak - $diskon, 2));

            $transaksi = Transaksi::create([
                'meja_id' => $mejaId,
                'karyawan_id' => null,
                'sumber_pesanan' => 'web',
                'catatan' => $validated['catatan'] ?? null,
                'tipe_pesanan' => $validated['tipe_pesanan'],
                'total_harga' => $totalHarga,
                'status_pesanan' => 'pending',
                'waktu_transaksi' => now(),
                'promo_kode_id' => $promo?->id,
                'diskon' => $diskon,
                'session_id' => $sessionId,
            ]);

            if ($promo) {
                $promo->increment('kuota_terpakai');
                PromoUsage::create([
                    'promo_kode_id' => $promo->id,
                    'meja_id' => $mejaId,
                    'transaksi_id' => $transaksi->id,
                    'session_id' => $sessionId,
                    'discount_amount' => $diskon,
                    'status' => 'active',
                    'used_at' => now(),
                ]);
            }

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
                'diskon' => $diskon,
            ];
        });

        $data = [
            'id' => $result['transaksi']->id,
            'waktu_transaksi' => $result['transaksi']->waktu_transaksi->toIso8601String(),
            'subtotal' => $result['subtotal'],
            'pajak' => $result['pajak'],
            'diskon' => $result['diskon'],
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

    public function myOrder(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'nullable|string|max:128',
            'meja_id' => 'nullable|integer',
        ]);

        $sessionId = trim((string) ($validated['session_id'] ?? ''));
        $mejaId = !empty($validated['meja_id']) ? (int) $validated['meja_id'] : null;

        $query = Transaksi::with(['meja', 'detail_transaksis.produk', 'pembayaran', 'promo'])
            ->latest('waktu_transaksi');

        $transaksi = null;

        if ($sessionId !== '') {
            $transaksi = (clone $query)->where('session_id', $sessionId)->first();
        }

        if (! $transaksi && $mejaId) {
            $transaksi = (clone $query)
                ->where('meja_id', $mejaId)
                ->whereIn('status_pesanan', ['pending', 'prosesed', 'ready'])
                ->first();
        }

        if (! $transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada pesanan aktif.',
            ], 404);
        }

        $statusMeta = [
            'pending' => ['label' => 'Menunggu Konfirmasi', 'step' => 1],
            'prosesed' => ['label' => 'Sedang Dibuat', 'step' => 2],
            'ready' => ['label' => 'Siap Diantar', 'step' => 3],
            'completed' => ['label' => 'Selesai', 'step' => 4],
        ];
        $meta = $statusMeta[$transaksi->status_pesanan] ?? ['label' => 'Menunggu Konfirmasi', 'step' => 1];
        $waktuWib = $transaksi->waktu_transaksi?->copy()->timezone('Asia/Jakarta');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $transaksi->id,
                'status_pesanan' => $transaksi->status_pesanan,
                'status_label' => $meta['label'],
                'status_step' => $meta['step'],
                'tipe_pesanan' => $transaksi->tipe_pesanan,
                'nomor_meja' => $transaksi->meja?->nomor_meja,
                'waktu_transaksi' => $waktuWib?->toIso8601String(),
                'waktu_transaksi_label' => $waktuWib?->translatedFormat('d M Y, H:i') . ' WIB',
                'total_harga' => (float) $transaksi->total_harga,
                'diskon' => (float) $transaksi->diskon,
                'kode_promo' => $transaksi->promo?->kode,
                'catatan' => $transaksi->catatan,
                'items' => $transaksi->detail_transaksis->map(fn ($detail) => [
                    'nama_produk' => $detail->produk?->nama_produk ?? 'Menu',
                    'qty' => $detail->QTY,
                    'customization' => $detail->customization,
                    'subtotal' => (float) $detail->subtotal,
                    'gambar' => $detail->produk?->gambar ? asset('storage/' . $detail->produk->gambar) : null,
                ])->values(),
                'pembayaran' => $transaksi->pembayaran ? [
                    'metode_pembayaran' => $transaksi->pembayaran->metode_pembayaran,
                    'status_pembayaran' => $transaksi->pembayaran->status_pembayaran,
                    'jumlah_bayar' => (float) $transaksi->pembayaran->jumlah_bayar,
                ] : null,
            ],
        ]);
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
