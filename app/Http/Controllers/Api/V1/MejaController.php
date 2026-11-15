<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Meja;
use App\Models\Transaksi;
use App\Support\ApiPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MejaController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'area' => ['nullable', 'in:lantai1,lantai2'],
            'status_aktif' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Meja::query()->orderBy('nomor_meja');

        if ($request->input('area') === 'lantai1') {
            $query->where('nomor_meja', '<=', 10);
        } elseif ($request->input('area') === 'lantai2') {
            $query->where('nomor_meja', '>', 10);
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->boolean('status_aktif'));
        }

        $occupiedIds = $this->occupiedTableIds();

        $mejas = $query
            ->paginate($request->integer('per_page', 30))
            ->through(fn (Meja $meja) => ApiPresenter::meja($meja, in_array($meja->id, $occupiedIds, true)));

        return $this->paginated($mejas, 'Daftar meja.');
    }

    public function status(): JsonResponse
    {
        $occupiedIds = $this->occupiedTableIds();

        $mejas = Meja::query()
            ->orderBy('nomor_meja')
            ->get()
            ->map(fn (Meja $meja) => ApiPresenter::meja($meja, in_array($meja->id, $occupiedIds, true)));

        return $this->ok([
            'tables' => $mejas,
            'summary' => [
                'total' => $mejas->count(),
                'active' => $mejas->where('status_aktif', true)->count(),
                'inactive' => $mejas->where('status_aktif', false)->count(),
                'occupied' => $mejas->where('is_occupied', true)->count(),
                'available' => $mejas->where('status_aktif', true)->where('is_occupied', false)->count(),
            ],
        ], 'Status meja.');
    }

    public function show(Meja $meja): JsonResponse
    {
        return $this->ok(
            ApiPresenter::meja($meja, in_array($meja->id, $this->occupiedTableIds(), true)),
            'Detail meja.'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nomor_meja' => ['required', 'integer', 'min:1', 'unique:mejas,nomor_meja'],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        $meja = Meja::create([
            'nomor_meja' => $validated['nomor_meja'],
            'kapasitas' => $validated['kapasitas'],
            'status_aktif' => $request->boolean('status_aktif', true),
        ]);

        return $this->created(ApiPresenter::meja($meja), 'Meja berhasil ditambahkan.');
    }

    public function update(Request $request, Meja $meja): JsonResponse
    {
        $validated = $request->validate([
            'nomor_meja' => ['required', 'integer', 'min:1', 'unique:mejas,nomor_meja,'.$meja->id],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        $meja->update([
            'nomor_meja' => $validated['nomor_meja'],
            'kapasitas' => $validated['kapasitas'],
            'status_aktif' => $request->boolean('status_aktif', (bool) $meja->status_aktif),
        ]);

        return $this->ok(
            ApiPresenter::meja($meja->fresh(), in_array($meja->id, $this->occupiedTableIds(), true)),
            'Data meja berhasil diperbarui.'
        );
    }

    public function destroy(Meja $meja): JsonResponse
    {
        if ($this->occupiedTableIds() !== [] && in_array($meja->id, $this->occupiedTableIds(), true)) {
            return $this->fail('Meja masih memiliki pesanan aktif dan tidak dapat dihapus.', 422);
        }

        $meja->delete();

        return $this->ok(null, 'Meja berhasil dihapus.');
    }

    /**
     * @return array<int, int>
     */
    private function occupiedTableIds(): array
    {
        return Transaksi::query()
            ->whereIn('status_pesanan', ['pending', 'ready', 'prosesed'])
            ->whereNotNull('meja_id')
            ->distinct()
            ->pluck('meja_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}
