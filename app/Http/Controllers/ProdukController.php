<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\ProdukOpsi;
use App\Models\KategoriProduk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query dengan relasi
        $query = Produk::with(['supplier', 'kategoriProduk']);

        // 1. Filter Pencarian
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        // 2. Filter Kategori
        if ($request->has('kategori_id') && $request->kategori_id != '') {
            $query->where('kategori_produk_id', $request->kategori_id);
        }

        // 3. Filter Tipe Produk
        if ($request->has('tipe') && $request->tipe != '') {
            $query->where('tipe', $request->tipe);
        }

        // 4. Filter Status Stok
        if ($request->has('stock_status') && $request->stock_status != '') {
            if ($request->stock_status === 'habis') {
                $query->where('stock', 0);
            } elseif ($request->stock_status === 'menipis') {
                $query->where('stock', '>', 0)->where('stock', '<', 10);
            } elseif ($request->stock_status === 'tersedia') {
                $query->where('stock', '>=', 10);
            }
        }

        // Eksekusi query dengan pagination
        $produks = $query->latest()->paginate(10);
        $kategoriProduks = KategoriProduk::all();

        // Hitung Statistik untuk Card di Bagian Atas View
        $menuSiapJual = Produk::where('tipe', 'jual')->where('status_aktif', true)->count();
        $stokTipis = Produk::where('tipe', 'jual')->where('stock', '>', 0)->where('stock', '<', 10)->count();
        $stokHabis = Produk::where('tipe', 'jual')->where('stock', 0)->count();

        return view('produks.index', compact(
            'produks',
            'kategoriProduks',
            'menuSiapJual',
            'stokTipis',
            'stokHabis'
        ));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $kategoriProduks = KategoriProduk::all();
        return view('produks.create', compact('suppliers', 'kategoriProduks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'kategori_produk_id' => 'required|exists:kategori_produks,id',
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'harga_jual' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'tipe' => 'required|in:jual,bahan baku',
            'satuan' => 'required|string|max:50',
            'stock' => 'required|integer|min:0',
            'status_aktif' => 'nullable|boolean',
            'is_customizable' => 'nullable|boolean',
            'opsis' => 'nullable|array',
            'opsis.*.nama_opsi' => 'required_with:opsis|string|max:255',
            'opsis.*.harga_tambahan' => 'required_with:opsis|numeric|min:0',
        ]);

        $validated['status_aktif'] = $request->has('status_aktif');
        $validated['is_customizable'] = $request->has('is_customizable');

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('produks', 'public');
            $validated['gambar'] = $path;
        }

        $produk = Produk::create($validated);

        if ($validated['is_customizable'] && $request->has('opsis')) {
            foreach ($request->opsis as $opsi) {
                $produk->opsis()->create([
                    'nama_opsi' => $opsi['nama_opsi'],
                    'harga_tambahan' => $opsi['harga_tambahan'],
                ]);
            }
        }

        return redirect()->route('produks.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        $suppliers = Supplier::all();
        $kategoriProduks = KategoriProduk::all();
        return view('produks.edit', compact('produk', 'suppliers', 'kategoriProduks'));
    }

    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'kategori_produk_id' => 'required|exists:kategori_produks,id',
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'harga_jual' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'tipe' => 'required|in:jual,bahan baku',
            'satuan' => 'required|string|max:50',
            'stock' => 'required|integer|min:0',
            'status_aktif' => 'nullable|boolean',
            'is_customizable' => 'nullable|boolean',
            'opsis' => 'nullable|array',
            'opsis.*.nama_opsi' => 'required_with:opsis|string|max:255',
            'opsis.*.harga_tambahan' => 'required_with:opsis|numeric|min:0',
        ]);

        $validated['status_aktif'] = $request->has('status_aktif');
        $validated['is_customizable'] = $request->has('is_customizable');

        if ($request->hasFile('gambar')) {
            if ($produk->gambar) {
                Storage::disk('public')->delete($produk->gambar);
            }
            $path = $request->file('gambar')->store('produks', 'public');
            $validated['gambar'] = $path;
        }

        $produk->update($validated);

        if ($validated['is_customizable']) {
            $produk->opsis()->delete();
            if ($request->has('opsis')) {
                foreach ($request->opsis as $opsi) {
                    $produk->opsis()->create([
                        'nama_opsi' => $opsi['nama_opsi'],
                        'harga_tambahan' => $opsi['harga_tambahan'],
                    ]);
                }
            }
        } else {
            $produk->opsis()->delete();
        }

        return redirect()->route('produks.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();
        return redirect()->route('produks.index')->with('success', 'Produk berhasil dihapus.');
    }
}
