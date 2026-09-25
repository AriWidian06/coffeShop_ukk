<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with(['supplier', 'kategori_produk']);

        if ($request->has('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        if ($request->has('kategori_id')) {
            $query->where('kategori_produk_id', $request->kategori_id);
        }

        $produks = $query->latest()->paginate(10);
        $kategoriProduks = KategoriProduk::all();

        return view('produks.index', compact('produks', 'kategoriProduks'));
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
            'harga_jual' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'tipe' => 'required|in:jual,bahan baku',
            'satuan' => 'required|string|max:50',
            'stock' => 'required|integer|min:0',
            'status_aktif' => 'nullable|boolean',
        ]);

        $validated['status_aktif'] = $request->has('status_aktif');

        Produk::create($validated);

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
            'harga_jual' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'tipe' => 'required|in:jual,bahan baku',
            'satuan' => 'required|string|max:50',
            'stock' => 'required|integer|min:0',
            'status_aktif' => 'nullable|boolean',
        ]);

        $validated['status_aktif'] = $request->has('status_aktif');

        $produk->update($validated);

        return redirect()->route('produks.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();
        return redirect()->route('produks.index')->with('success', 'Produk berhasil dihapus.');
    }
}
