<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Meja;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::with(['meja', 'karyawan', 'pembayaran'])
            ->latest('waktu_transaksi')
            ->paginate(10);

        return view('transaksis.index', compact('transaksis'));
    }

    public function create()
    {
        $mejas = Meja::where('status_aktif', true)->get();
        $produks = Produk::where('status_aktif', true)->where('tipe', 'jual')->get();
        $karyawans = Karyawan::all();

        return view('transaksis.create', compact('mejas', 'produks', 'karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'meja_id' => 'required|exists:mejas,id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'tipe_pesanan' => 'required|in:dine-in,take-away',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $totalHarga = 0;
            $detailItems = [];

            foreach ($request->items as $item) {
                $produk = Produk::findOrFail($item['produk_id']);

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
                'meja_id' => $request->meja_id,
                'karyawan_id' => $request->karyawan_id,
                'tipe_pesanan' => $request->tipe_pesanan,
                'total_harga' => $totalHarga,
                'status_pesanan' => 'pending',
                'waktu_transaksi' => now(),
            ]);

            foreach ($detailItems as $detail) {
                $detail['transaksi_id'] = $transaksi->id;
                DetailTransaksi::create($detail);
            }

            return redirect()->route('transaksis.show', $transaksi->id)
                ->with('success', 'Transaksi berhasil dibuat.');
        });
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['meja', 'karyawan', 'detail_transaksis.produk', 'pembayaran']);
        return view('transaksis.show', compact('transaksi'));
    }

    public function updateStatus(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'status_pesanan' => 'required|in:pending,ready,prosesed,completed',
        ]);

        $transaksi->update(['status_pesanan' => $request->status_pesanan]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();
        return redirect()->route('transaksis.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
