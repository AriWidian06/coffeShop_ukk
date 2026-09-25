<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use Illuminate\Http\Request;

class KategoriProdukController extends Controller
{
    public function index()
    {
        $kategoriProduks = KategoriProduk::withCount('produks')->latest()->paginate(10);
        return view('kategori_produks.index', compact('kategoriProduks'));
    }

    public function create()
    {
        return view('kategori_produks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_produks,nama_kategori',
        ]);

        KategoriProduk::create($validated);

        return redirect()->route('kategori-produks.index')->with('success', 'Kategori produk berhasil ditambahkan.');
    }

    public function edit(KategoriProduk $kategoriProduk)
    {
        return view('kategori_produks.edit', compact('kategoriProduk'));
    }

    public function update(Request $request, KategoriProduk $kategoriProduk)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_produks,nama_kategori,' . $kategoriProduk->id,
        ]);

        $kategoriProduk->update($validated);

        return redirect()->route('kategori-produks.index')->with('success', 'Kategori produk berhasil diperbarui.');
    }

    public function destroy(KategoriProduk $kategoriProduk)
    {
        $kategoriProduk->delete();
        return redirect()->route('kategori-produks.index')->with('success', 'Kategori produk berhasil dihapus.');
    }
}
