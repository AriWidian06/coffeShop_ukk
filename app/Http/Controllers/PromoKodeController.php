<?php

namespace App\Http\Controllers;

use App\Models\PromoKode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromoKodeController extends Controller
{
    public function index()
    {
        $promoKodes = PromoKode::latest()->paginate(15);

        return view('promo_kodes.index', compact('promoKodes'));
    }

    public function create()
    {
        return view('promo_kodes.form');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        PromoKode::create($validated);

        return redirect()->route('promo-kodes.index')->with('success', 'Kode promo berhasil ditambahkan.');
    }

    public function edit(PromoKode $promoKode)
    {
        return view('promo_kodes.form', compact('promoKode'));
    }

    public function update(Request $request, PromoKode $promoKode)
    {
        $validated = $this->validated($request, $promoKode);

        $promoKode->update($validated);

        return redirect()->route('promo-kodes.index')->with('success', 'Kode promo berhasil diperbarui.');
    }

    public function destroy(PromoKode $promoKode)
    {
        $promoKode->usages()->delete();
        $promoKode->delete();

        return redirect()->route('promo-kodes.index')->with('success', 'Kode promo berhasil dihapus.');
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

        $validated['kode'] = strtoupper(trim($validated['kode']));
        $validated['is_aktif'] = $request->has('is_aktif');

        return $validated;
    }
}