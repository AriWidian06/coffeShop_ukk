<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Meja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => 'nullable|string|max:40',
            'status' => 'nullable|in:pending,ready,prosesed,completed',
        ]);

        $query = Transaksi::with(['meja', 'karyawan', 'pembayaran', 'detail_transaksis.produk'])
            ->latest('waktu_transaksi');

        if (auth('karyawan')->user()->role === 'kasir') {
            $query->where(function ($query) {
                $query->where('karyawan_id', auth('karyawan')->id())
                    ->orWhere('sumber_pesanan', 'web');
            });
        }

        if (!empty($filters['q'])) {
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

        if (!empty($filters['status'])) {
            $query->where('status_pesanan', $filters['status']);
        }

        $transaksis = $query->paginate(10)->withQueryString();

        return view('transaksis.index', compact('transaksis'));
    }

    public function create(Request $request)
    {
        $mejas = Meja::where('status_aktif', true)->orderBy('nomor_meja')->get();
        $produks = Produk::with(['kategoriProduk', 'opsis'])
            ->where('status_aktif', true)
            ->where('tipe', 'jual')
            ->orderBy('nama_produk')
            ->get();

        $selectedMejaId = $request->query('meja_id');

        $activeOrders = Transaksi::with(['karyawan', 'detail_transaksis.produk'])
            ->whereIn('status_pesanan', ['pending', 'ready', 'prosesed'])
            ->whereNotNull('meja_id')
            ->latest('waktu_transaksi')
            ->get()
            ->keyBy('meja_id');

        $mejaStatus = $activeOrders->keys()->toArray();

        return view('transaksis.create', compact('mejas', 'produks', 'selectedMejaId', 'mejaStatus', 'activeOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'meja_id' => 'nullable|required_if:tipe_pesanan,dine-in|exists:mejas,id',
            'tipe_pesanan' => 'required|in:dine-in,take-away',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.customization' => 'nullable|string|max:250',
        ]);

        return DB::transaction(function () use ($request) {
            $subtotalHarga = 0;
            $detailItems = [];

            foreach ($request->items as $item) {
                $produk = Produk::where('status_aktif', true)
                    ->where('tipe', 'jual')
                    ->lockForUpdate()
                    ->findOrFail($item['produk_id']);

                if ($produk->stock < $item['qty']) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$produk->nama_produk} tidak mencukupi.",
                    ]);
                }

                // --- PERBAIKAN HITUNG HARGA KOSTUMISASI ---
                $extraPrice = 0;
                if (!empty($item['customization'])) {
                    // Pisahkan opsi yang dipilih (misal: "nuklir (+5000), rudal (+3000)")
                    $selectedOptions = explode(', ', $item['customization']);
                    foreach ($selectedOptions as $optText) {
                        // Cari opsi di database yang namanya ada di dalam teks pilihan
                        $opsi = \App\Models\ProdukOpsi::where('produk_id', $produk->id)
                            ->where('nama_opsi', 'like', '%' . $optText . '%')
                            ->first();
                        if ($opsi) {
                            $extraPrice += $opsi->harga_tambahan;
                        }
                    }
                }

                $subtotal = ($produk->harga_jual + $extraPrice) * $item['qty'];
                $subtotalHarga += $subtotal;
                // ------------------------------------------

                $detailItems[] = [
                    'produk_id' => $produk->id,
                    'QTY' => $item['qty'],
                    'subtotal' => $subtotal,
                    'customization' => $item['customization'] ?? null,
                ];

                $produk->decrement('stock', $item['qty']);
            }

            $pajak = round($subtotalHarga * 0.1, 2);
            $totalHarga = $subtotalHarga + $pajak;

            $transaksi = Transaksi::create([
                'meja_id' => $request->meja_id,
                'karyawan_id' => auth('karyawan')->id(),
                'tipe_pesanan' => $request->tipe_pesanan,
                'total_harga' => $totalHarga,
                'status_pesanan' => 'pending',
                'waktu_transaksi' => now(),
            ]);

            foreach ($detailItems as $detail) {
                $detail['transaksi_id'] = $transaksi->id;
                DetailTransaksi::create($detail);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'transaksi' => $transaksi->load(['meja', 'detail_transaksis.produk']),
                    'message' => 'Transaksi berhasil dibuat.',
                ]);
            }

            if ($request->filled('redirect_to')) {
                return redirect($request->redirect_to)->with('success', 'Transaksi berhasil dibuat.');
            }

            if (auth('karyawan')->check() && auth('karyawan')->user()->role === 'kasir') {
                return redirect()->route('dashboard')->with('success', 'Transaksi berhasil dibuat.');
            }

            return redirect()->route('transaksis.show', $transaksi->id)
                ->with('success', 'Transaksi berhasil dibuat.');
        });
    }

    public function show(Transaksi $transaksi)
    {
        Gate::authorize('view-transaction', $transaksi);

        $transaksi->load(['meja', 'karyawan', 'detail_transaksis.produk', 'pembayaran']);
        return view('transaksis.show', compact('transaksi'));
    }

    public function updateStatus(Request $request, Transaksi $transaksi)
    {
        Gate::authorize('view-transaction', $transaksi);

        $request->validate([
            'status_pesanan' => 'required|in:pending,ready,prosesed,completed',
        ]);

        $transaksi->update(['status_pesanan' => $request->status_pesanan]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status_pesanan' => $transaksi->status_pesanan,
                'message' => 'Status pesanan #' . $transaksi->id . ' berhasil diperbarui.',
            ]);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function activeOrdersJson()
    {
        $activeOrders = Transaksi::with(['meja', 'karyawan', 'detail_transaksis.produk', 'pembayaran'])
            ->whereIn('status_pesanan', ['pending', 'ready', 'prosesed'])
            ->latest('waktu_transaksi')
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $activeOrders,
            'counts' => [
                'total' => $activeOrders->count(),
                'pending' => $activeOrders->where('status_pesanan', 'pending')->count(),
                'prosesed' => $activeOrders->where('status_pesanan', 'prosesed')->count(),
                'ready' => $activeOrders->where('status_pesanan', 'ready')->count(),
            ],
        ]);
    }

    public function destroy(Transaksi $transaksi)
    {
        Gate::authorize('view-transaction', $transaksi);

        $transaksi->delete();
        return redirect()->route('transaksis.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
