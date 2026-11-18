<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\ProdukGambar;
use App\Support\ApiPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kategori_id' => ['nullable', 'integer', 'exists:kategori_produks,id'],
            'tipe' => ['nullable', 'in:jual,bahan baku'],
            'stock_status' => ['nullable', 'in:habis,menipis,tersedia'],
            'status_aktif' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Produk::with(['supplier', 'kategoriProduk', 'opsis']);

        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%'.$request->string('search').'%');
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_produk_id', $request->integer('kategori_id'));
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->string('tipe'));
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->boolean('status_aktif'));
        }

        match ($request->input('stock_status')) {
            'habis' => $query->where('stock', 0),
            'menipis' => $query->where('stock', '>', 0)->where('stock', '<', 10),
            'tersedia' => $query->where('stock', '>=', 10),
            default => null,
        };

        $produks = $query->latest()
            ->paginate($request->integer('per_page', 15))
            ->through(fn (Produk $produk) => ApiPresenter::produk($produk));

        return $this->paginated($produks, 'Daftar produk.');
    }

    public function show(Produk $produk): JsonResponse
    {
        $produk->load(['supplier', 'kategoriProduk', 'opsis']);

        return $this->ok(ApiPresenter::produk($produk), 'Detail produk.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validated($request);
        $opsis = $validated['opsis'] ?? [];
        unset($validated['opsis']);
        $validated['status_aktif'] = $request->boolean('status_aktif', true);
        $validated['is_customizable'] = $request->boolean('is_customizable', false);

        $hasGambar = $request->hasFile('gambar');
        $validated['has_gambar_data'] = $hasGambar;

        if ($hasGambar) {
            $path = $request->file('gambar')->store('produks', 'public');
            $validated['gambar'] = $path;
        }

        $produk = Produk::create($validated);

        if ($hasGambar && isset($path)) {
            $this->simpanGambarData($produk, $path);
        }

        if ($validated['is_customizable'] && $opsis !== []) {
            $this->simpanOpsi($produk, $opsis);
        }

        $produk->load(['supplier', 'kategoriProduk', 'opsis']);

        return $this->created(ApiPresenter::produk($produk), 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Produk $produk): JsonResponse
    {
        $validated = $this->validated($request);
        $opsis = $validated['opsis'] ?? [];
        unset($validated['opsis']);
        $validated['status_aktif'] = $request->boolean('status_aktif', (bool) $produk->status_aktif);
        $validated['is_customizable'] = $request->boolean('is_customizable', (bool) $produk->is_customizable);

        if ($request->hasFile('gambar')) {
            if ($produk->gambar) {
                Storage::disk('public')->delete($produk->gambar);
            }

            $path = $request->file('gambar')->store('produks', 'public');
            $validated['gambar'] = $path;
            $validated['has_gambar_data'] = true;

            $this->simpanGambarData($produk, $path);
        }

        $produk->update($validated);

        $produk->opsis()->delete();

        if ($validated['is_customizable'] && $opsis !== []) {
            $this->simpanOpsi($produk, $opsis);
        }

        $produk->load(['supplier', 'kategoriProduk', 'opsis']);

        return $this->ok(ApiPresenter::produk($produk), 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk): JsonResponse
    {
        // FK detail_transaksis.produk_id memakai cascade; jangan hapus produk
        // yang sudah terjual agar riwayat laporan tidak ikut hilang.
        if ($produk->detailTransaksis()->exists()) {
            return $this->fail('Produk sudah memiliki riwayat penjualan dan tidak dapat dihapus. Nonaktifkan produk sebagai gantinya.', 422);
        }

        if ($produk->gambar) {
            Storage::disk('public')->delete($produk->gambar);
        }

        $produk->gambarData()->delete();
        $produk->opsis()->delete();
        $produk->delete();

        return $this->ok(null, 'Produk berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'kategori_produk_id' => ['required', 'exists:kategori_produks,id'],
            'nama_produk' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'tipe' => ['required', 'in:jual,bahan baku'],
            'satuan' => ['required', 'string', 'max:50'],
            'stock' => ['required', 'integer', 'min:0'],
            'status_aktif' => ['nullable', 'boolean'],
            'is_customizable' => ['nullable', 'boolean'],
            'opsis' => ['nullable', 'array'],
            'opsis.*.kategori' => ['nullable', 'string', 'max:255'],
            'opsis.*.tipe' => ['required_with:opsis', 'in:single,multiple'],
            'opsis.*.is_required' => ['nullable', 'boolean'],
            'opsis.*.nama_opsi' => ['required_with:opsis', 'string', 'max:255'],
            'opsis.*.harga_tambahan' => ['required_with:opsis', 'numeric', 'min:0'],
        ]);
    }

    private function simpanOpsi(Produk $produk, array $opsis): void
    {
        foreach ($opsis as $opsi) {
            $produk->opsis()->create([
                'kategori' => $opsi['kategori'] ?? null,
                'tipe' => $opsi['tipe'],
                'is_required' => (bool) ($opsi['is_required'] ?? false),
                'nama_opsi' => $opsi['nama_opsi'],
                'harga_tambahan' => $opsi['harga_tambahan'],
            ]);
        }
    }

    /**
     * Simpan salinan gambar di database agar bertahan setelah filesystem
     * sementara produksi dibersihkan (pola yang sama dengan web admin).
     */
    private function simpanGambarData(Produk $produk, string $path): void
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            return;
        }

        ProdukGambar::updateOrCreate(
            ['produk_id' => $produk->id],
            [
                'mime' => $disk->mimeType($path) ?: 'image/jpeg',
                'data' => base64_encode($disk->get($path)),
            ]
        );

        if (! $produk->has_gambar_data) {
            $produk->forceFill(['has_gambar_data' => true])->save();
        }
    }
}
