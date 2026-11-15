<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Pembayaran;
use App\Models\Produk;
use App\Models\ProdukOpsi;
use App\Models\PromoUsage;
use App\Models\Transaksi;
use App\Services\PaymentService;
use App\Support\ApiPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:40'],
            'status' => ['nullable', 'in:pending,ready,prosesed,completed,cancelled'],
            'tipe_pesanan' => ['nullable', 'in:dine-in,take-away'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Transaksi::with(['meja', 'karyawan', 'pembayaran', 'detail_transaksis.produk', 'promo'])
            ->latest('waktu_transaksi')
            ->latest('id');

        if (strtolower((string) $request->user()->role) === 'kasir') {
            $query->where(function ($query) use ($request) {
                $query->where('karyawan_id', $request->user()->id)
                    ->orWhere('sumber_pesanan', 'web');
            });
        }

        if (! empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function ($query) use ($search) {
                if (ctype_digit($search)) {
                    $query->where('id', (int) $search)
                        ->orWhereHas('meja', fn ($mejaQuery) => $mejaQuery->where('nomor_meja', 'like', "%{$search}%"));
                } else {
                    $query->whereHas('meja', fn ($mejaQuery) => $mejaQuery->where('nomor_meja', 'like', "%{$search}%"));
                }
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status_pesanan', $filters['status']);
        }

        if (! empty($filters['tipe_pesanan'])) {
            $query->where('tipe_pesanan', $filters['tipe_pesanan']);
        }

        $transaksis = $query
            ->paginate($request->integer('per_page', 15))
            ->through(fn (Transaksi $transaksi) => ApiPresenter::transaksi($transaksi));

        return $this->paginated($transaksis, 'Daftar transaksi.');
    }

    public function show(Request $request, Transaksi $transaksi): JsonResponse
    {
        if (! $request->user()->can('view', $transaksi)) {
            return $this->fail('Akses ditolak: Anda tidak berwenang melihat transaksi ini.', 403);
        }

        return $this->ok(ApiPresenter::transaksi($transaksi), 'Detail transaksi.');
    }

    public function store(Request $request, PaymentService $paymentService): JsonResponse
    {
        $validated = $request->validate([
            'meja_id' => ['nullable', 'required_if:tipe_pesanan,dine-in', 'exists:mejas,id'],
            'tipe_pesanan' => ['required', 'in:dine-in,take-away'],
            'metode_pembayaran' => ['required', 'in:cash,QRIS'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'session_id' => ['nullable', 'string', 'max:128'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.produk_id' => ['required', 'integer', 'exists:produks,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.customization' => ['nullable', 'string', 'max:250'],
        ]);

        $transaksi = DB::transaction(function () use ($validated, $request) {
            if (! empty($validated['meja_id'])) {
                $mejaTerisi = Transaksi::query()
                    ->where('meja_id', $validated['meja_id'])
                    ->whereIn('status_pesanan', ['pending', 'ready', 'prosesed'])
                    ->lockForUpdate()
                    ->exists();

                if ($mejaTerisi) {
                    throw ValidationException::withMessages([
                        'meja_id' => 'Meja ini sudah memiliki pesanan aktif. Silakan pilih meja lain.',
                    ]);
                }
            }

            $subtotal = 0.0;
            $items = [];
            $productsById = [];
            $quantitiesByProduct = [];

            foreach ($validated['items'] as $item) {
                $produkId = (int) $item['produk_id'];
                $produk = $productsById[$produkId] ??= Produk::query()
                    ->where('status_aktif', true)
                    ->where('tipe', 'jual')
                    ->lockForUpdate()
                    ->find($produkId);

                if (! $produk) {
                    throw ValidationException::withMessages([
                        'items' => 'Salah satu menu sudah tidak tersedia.',
                    ]);
                }

                $extraPrice = $this->extraPrice($produk, $item['customization'] ?? null);
                $lineTotal = round(((float) $produk->harga_jual + $extraPrice) * $item['qty'], 2);

                $subtotal += $lineTotal;
                $quantitiesByProduct[$produkId] = ($quantitiesByProduct[$produkId] ?? 0) + $item['qty'];
                $items[] = [
                    'produk' => $produk,
                    'qty' => (int) $item['qty'],
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
            $totalHarga = round($subtotal + $pajak, 2);

            $transaksi = Transaksi::create([
                'meja_id' => $validated['meja_id'] ?? null,
                'karyawan_id' => $request->user()->id,
                'sumber_pesanan' => 'kasir',
                'catatan' => $validated['catatan'] ?? null,
                'tipe_pesanan' => $validated['tipe_pesanan'],
                'total_harga' => $totalHarga,
                'status_pesanan' => 'pending',
                'waktu_transaksi' => now(),
                'session_id' => $validated['session_id'] ?? null,
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
                    'customization' => $item['customization'],
                ]);
            }

            return $transaksi;
        });

        $transaksi->load(['meja', 'karyawan', 'detail_transaksis.produk', 'pembayaran', 'promo']);

        $data = [
            'transaksi' => ApiPresenter::transaksi($transaksi),
            'subtotal' => round((float) $transaksi->detail_transaksis->sum('subtotal'), 2),
            'pajak' => round(max(0, (float) $transaksi->total_harga - (float) $transaksi->detail_transaksis->sum('subtotal')), 2),
            'total_harga' => (float) $transaksi->total_harga,
        ];

        if ($validated['metode_pembayaran'] === 'QRIS') {
            try {
                $data['qris'] = $paymentService->createQrisCharge($transaksi);
            } catch (\Throwable $e) {
                $data['qris_error'] = 'QRIS otomatis gagal dibuat. Silakan gunakan endpoint pembayaran atau bayar di kasir.';
            }
        }

        return $this->created($data, 'Transaksi berhasil dibuat.');
    }

    public function updateStatus(Request $request, Transaksi $transaksi): JsonResponse
    {
        if (! $request->user()->can('view', $transaksi)) {
            return $this->fail('Akses ditolak: Anda tidak berwenang mengubah transaksi ini.', 403);
        }

        $validated = $request->validate([
            'status_pesanan' => ['required', 'in:pending,ready,prosesed,completed,cancelled'],
        ]);

        $status = $validated['status_pesanan'];

        if ($transaksi->status_pesanan === 'cancelled' && $status !== 'cancelled') {
            return $this->fail('Transaksi yang sudah dibatalkan tidak dapat diaktifkan kembali.', 422);
        }

        DB::transaction(function () use ($transaksi, $status) {
            if ($status === 'cancelled') {
                $transaksi->reverseStockAndPromo();
            }

            $transaksi->update(['status_pesanan' => $status]);

            if ($status === 'completed') {
                PromoUsage::where('transaksi_id', $transaksi->id)
                    ->where('status', 'active')
                    ->update([
                        'status' => 'released',
                        'released_at' => now(),
                    ]);
            }
        });

        return $this->ok(ApiPresenter::transaksi($transaksi->fresh()), 'Status pesanan berhasil diperbarui.');
    }

    public function activeOrders(): JsonResponse
    {
        $orders = Transaksi::with(['meja', 'karyawan', 'detail_transaksis.produk', 'pembayaran', 'promo'])
            ->whereIn('status_pesanan', ['pending', 'ready', 'prosesed'])
            ->orderBy('waktu_transaksi')
            ->orderBy('id')
            ->get();

        return $this->ok([
            'orders' => $orders->map(fn (Transaksi $order) => ApiPresenter::transaksi($order))->values(),
            'counts' => [
                'total' => $orders->count(),
                'pending' => $orders->where('status_pesanan', 'pending')->count(),
                'prosesed' => $orders->where('status_pesanan', 'prosesed')->count(),
                'ready' => $orders->where('status_pesanan', 'ready')->count(),
            ],
        ], 'Antrean pesanan aktif.');
    }

    public function destroy(Request $request, Transaksi $transaksi): JsonResponse
    {
        if (! $request->user()->can('delete', $transaksi)) {
            return $this->fail('Akses ditolak: Anda tidak berwenang menghapus transaksi ini.', 403);
        }

        DB::transaction(function () use ($transaksi) {
            $transaksi->reverseStockAndPromo();
            $transaksi->delete();
        });

        return $this->ok(null, 'Transaksi berhasil dihapus.');
    }

    public function payment(Request $request, Transaksi $transaksi): JsonResponse
    {
        if (! $request->user()->can('view', $transaksi)) {
            return $this->fail('Akses ditolak: Anda tidak berwenang membayar transaksi ini.', 403);
        }

        $validated = $request->validate([
            'jumlah_bayar' => ['required', 'numeric', 'min:0'],
            'metode_pembayaran' => ['required', 'in:cash,QRIS'],
        ]);

        if ($transaksi->status_pesanan === 'cancelled') {
            return $this->fail('Transaksi yang sudah dibatalkan tidak dapat dibayar.', 422);
        }

        if ($transaksi->pembayaran()->exists()) {
            return $this->fail('Transaksi ini sudah memiliki pembayaran.', 422);
        }

        if ((float) $validated['jumlah_bayar'] < (float) $transaksi->total_harga) {
            return $this->fail('Jumlah pembayaran kurang dari total tagihan.', 422);
        }

        $kembalian = $validated['metode_pembayaran'] === 'cash'
            ? round((float) $validated['jumlah_bayar'] - (float) $transaksi->total_harga, 2)
            : 0;

        DB::transaction(function () use ($transaksi, $validated, $kembalian) {
            Pembayaran::create([
                'transaksi_id' => $transaksi->id,
                'jumlah_bayar' => $validated['jumlah_bayar'],
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'status_pembayaran' => 'paid',
                'kembalian' => $kembalian,
                'waktu_pembayaran' => now(),
            ]);

            $transaksi->update(['status_pesanan' => 'completed']);

            PromoUsage::where('transaksi_id', $transaksi->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'released',
                    'released_at' => now(),
                ]);
        });

        $transaksi->refresh()->load(['meja', 'karyawan', 'detail_transaksis.produk', 'pembayaran', 'promo']);

        return $this->ok([
            'transaksi' => ApiPresenter::transaksi($transaksi),
            'pembayaran' => ApiPresenter::pembayaran($transaksi->pembayaran),
            'kembalian' => $kembalian,
            'struk' => ApiPresenter::struk($transaksi),
        ], 'Pembayaran berhasil dikonfirmasi.');
    }

    public function receipt(Request $request, Transaksi $transaksi): JsonResponse
    {
        if (! $request->user()->can('view', $transaksi)) {
            return $this->fail('Akses ditolak: Anda tidak berwenang melihat struk transaksi ini.', 403);
        }

        return $this->ok(ApiPresenter::struk($transaksi), 'Struk transaksi.');
    }

    public function qrisCharge(Request $request, Transaksi $transaksi, PaymentService $paymentService): JsonResponse
    {
        if (! $request->user()->can('view', $transaksi)) {
            return $this->fail('Akses ditolak: Anda tidak berwenang membuat QRIS untuk transaksi ini.', 403);
        }

        if ($transaksi->pembayaran()->exists()) {
            return $this->fail('Transaksi ini sudah dibayar.', 422);
        }

        if ($transaksi->status_pesanan === 'cancelled') {
            return $this->fail('Transaksi yang sudah dibatalkan tidak dapat dibayar.', 422);
        }

        try {
            $qris = $paymentService->createQrisCharge($transaksi);
        } catch (\Throwable $e) {
            return $this->fail('Gagal membuat QRIS: '.$e->getMessage(), 502);
        }

        return $this->ok($qris, 'QRIS berhasil dibuat.');
    }

    public function qrisCheck(Request $request, Transaksi $transaksi, PaymentService $paymentService): JsonResponse
    {
        if (! $request->user()->can('view', $transaksi)) {
            return $this->fail('Akses ditolak: Anda tidak berwenang memeriksa pembayaran transaksi ini.', 403);
        }

        $validated = $request->validate([
            'order_id' => ['required', 'string', 'max:100'],
        ]);

        if (! str_starts_with($validated['order_id'], 'TRX-'.$transaksi->id.'-')) {
            return $this->fail('Order ID tidak valid.', 422);
        }

        $status = $paymentService->syncStatus($transaksi, $validated['order_id']);
        $transaksi->refresh();

        return $this->ok([
            'paid' => in_array($status, ['capture', 'settlement'], true),
            'status' => $status,
            'status_pesanan' => $transaksi->status_pesanan,
            'pembayaran' => ApiPresenter::pembayaran($transaksi->pembayaran),
        ], 'Status pembayaran QRIS.');
    }

    private function extraPrice(Produk $produk, ?string $customization): float
    {
        if (empty($customization)) {
            return 0.0;
        }

        $extra = 0.0;

        foreach (explode(', ', $customization) as $optText) {
            $optName = explode(' (+', $optText)[0];
            $opsi = ProdukOpsi::where('produk_id', $produk->id)
                ->where('nama_opsi', 'like', '%'.$optName.'%')
                ->first();

            if ($opsi) {
                $extra += (float) $opsi->harga_tambahan;
            }
        }

        return $extra;
    }
}
