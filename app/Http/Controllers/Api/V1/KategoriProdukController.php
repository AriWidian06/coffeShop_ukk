<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use App\Support\ApiPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KategoriProdukController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = KategoriProduk::withCount('produks')->latest();

        if ($request->filled('search')) {
            $query->where('nama_kategori', 'like', '%'.$request->string('search').'%');
        }

        $kategoris = $query
            ->paginate($request->integer('per_page', 15))
            ->through(fn (KategoriProduk $kategori) => ApiPresenter::kategori($kategori));

        return $this->paginated($kategoris, 'Daftar kategori produk.');
    }

    public function show(KategoriProduk $kategoriProduk): JsonResponse
    {
        $kategoriProduk->loadCount('produks');

        return $this->ok(ApiPresenter::kategori($kategoriProduk), 'Detail kategori produk.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:255', 'unique:kategori_produks,nama_kategori'],
        ]);

        $kategori = KategoriProduk::create($validated);
        $kategori->loadCount('produks');

        return $this->created(ApiPresenter::kategori($kategori), 'Kategori produk berhasil ditambahkan.');
    }

    public function update(Request $request, KategoriProduk $kategoriProduk): JsonResponse
    {
        $validated = $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                'unique:kategori_produks,nama_kategori,'.$kategoriProduk->id,
            ],
        ]);

        $kategoriProduk->update($validated);
        $kategoriProduk->loadCount('produks');

        return $this->ok(ApiPresenter::kategori($kategoriProduk), 'Kategori produk berhasil diperbarui.');
    }

    public function destroy(KategoriProduk $kategoriProduk): JsonResponse
    {
        if ($kategoriProduk->produks()->exists()) {
            return $this->fail(
                'Kategori masih dipakai oleh produk. Pindahkan atau hapus produk terlebih dahulu.',
                422
            );
        }

        $kategoriProduk->delete();

        return $this->ok(null, 'Kategori produk berhasil dihapus.');
    }
}
