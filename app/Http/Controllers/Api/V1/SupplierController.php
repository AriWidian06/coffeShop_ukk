<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Support\ApiPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Supplier::withCount('produks')->latest();

        if ($request->filled('search')) {
            $query->where('nama_supplier', 'like', '%'.$request->string('search').'%');
        }

        $suppliers = $query
            ->paginate($request->integer('per_page', 15))
            ->through(fn (Supplier $supplier) => array_merge(
                ApiPresenter::supplier($supplier),
                ['produks_count' => $supplier->produks_count ?? null]
            ));

        return $this->paginated($suppliers, 'Daftar supplier.');
    }

    public function show(Supplier $supplier): JsonResponse
    {
        $supplier->loadCount('produks');

        return $this->ok(
            array_merge(ApiPresenter::supplier($supplier), ['produks_count' => $supplier->produks_count]),
            'Detail supplier.'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_supplier' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'no_telp' => ['required', 'string', 'max:20'],
        ]);

        $supplier = Supplier::create($validated);

        return $this->created(ApiPresenter::supplier($supplier), 'Supplier berhasil ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        $validated = $request->validate([
            'nama_supplier' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'no_telp' => ['required', 'string', 'max:20'],
        ]);

        $supplier->update($validated);

        return $this->ok(ApiPresenter::supplier($supplier), 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        if ($supplier->produks()->exists()) {
            return $this->fail(
                'Supplier masih dipakai oleh produk. Pindahkan atau hapus produk terlebih dahulu.',
                422
            );
        }

        $supplier->delete();

        return $this->ok(null, 'Supplier berhasil dihapus.');
    }
}
