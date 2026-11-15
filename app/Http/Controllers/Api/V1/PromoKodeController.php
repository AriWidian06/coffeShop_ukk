<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\PromoKode;
use App\Support\ApiPresenter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromoKodeController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'is_aktif' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = PromoKode::latest();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($query) use ($search) {
                $query->where('kode', 'like', '%'.$search.'%')
                    ->orWhere('nama_promo', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('is_aktif')) {
            $query->where('is_aktif', $request->boolean('is_aktif'));
        }

        $promos = $query
            ->paginate($request->integer('per_page', 15))
            ->through(fn (PromoKode $promo) => ApiPresenter::promo($promo));

        return $this->paginated($promos, 'Daftar kode promo.');
    }

    public function show(PromoKode $promoKode): JsonResponse
    {
        return $this->ok(ApiPresenter::promo($promoKode), 'Detail kode promo.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validated($request);
        $validated['is_aktif'] = $request->boolean('is_aktif', true);

        $promo = PromoKode::create($validated);

        return $this->created(ApiPresenter::promo($promo), 'Kode promo berhasil ditambahkan.');
    }

    public function update(Request $request, PromoKode $promoKode): JsonResponse
    {
        $validated = $this->validated($request, $promoKode);
        $validated['is_aktif'] = $request->boolean('is_aktif', (bool) $promoKode->is_aktif);

        $promoKode->update($validated);

        return $this->ok(ApiPresenter::promo($promoKode->fresh()), 'Kode promo berhasil diperbarui.');
    }

    public function destroy(PromoKode $promoKode): JsonResponse
    {
        $promoKode->usages()->delete();
        $promoKode->delete();

        return $this->ok(null, 'Kode promo berhasil dihapus.');
    }

    private function validated(Request $request, ?PromoKode $promoKode = null): array
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', Rule::unique('promo_kodes', 'kode')->ignore($promoKode?->id)],
            'nama_promo' => ['nullable', 'string', 'max:100'],
            'nilai_diskon' => ['required', 'numeric', 'gt:0', 'lte:100'],
            'min_belanja' => ['nullable', 'numeric', 'min:0'],
            'maks_diskon' => ['nullable', 'numeric', 'min:0'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_berakhir' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'kuota' => ['nullable', 'integer', 'min:0'],
            'is_aktif' => ['nullable', 'boolean'],
        ]);

        $validated['kode'] = PromoKode::normalized($validated['kode']);

        // Input tanggal dari admin menggunakan WIB, simpan ke database sebagai UTC.
        if (! empty($validated['tanggal_mulai'])) {
            $validated['tanggal_mulai'] = Carbon::parse($validated['tanggal_mulai'], 'Asia/Jakarta')->utc();
        }

        if (! empty($validated['tanggal_berakhir'])) {
            $validated['tanggal_berakhir'] = Carbon::parse($validated['tanggal_berakhir'], 'Asia/Jakarta')->utc();
        }

        return $validated;
    }
}
