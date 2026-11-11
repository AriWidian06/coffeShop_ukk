@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-4 sm:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">
                        Promo / <span class="text-primary-600">Kode Promo</span>
                    </p>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900">{{ isset($promoKode) ? 'Edit Kode Promo' : 'Tambah Kode Promo' }}</h1>
                </div>
                <a href="{{ route('promo-kodes.index') }}" class="px-3.5 py-1.5 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 self-start shrink-0">Kembali</a>
            </div>
        </div>
    </header>

    <main class="p-4 sm:p-6 max-w-3xl mx-auto">
        <form method="POST" action="{{ isset($promoKode) ? route('promo-kodes.update', $promoKode) : route('promo-kodes.store') }}" class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 space-y-4 shadow-sm">
            @csrf
            @if (isset($promoKode))
                @method('PUT')
            @endif

            <div>
                <label class="text-sm font-bold text-slate-800">Kode Promo (Persen)</label>
                <input type="text" name="kode" value="{{ old('kode', $promoKode->kode ?? '') }}" required class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-primary-500">
                @error('kode') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-sm font-bold text-slate-800">Nama Promo / Penjelasan</label>
                <input type="text" name="nama_promo" value="{{ old('nama_promo', $promoKode->nama_promo ?? '') }}" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-primary-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-800">Nilai Diskon (%)</label>
                    <input type="number" step="0.01" min="1" max="100" name="nilai_diskon" value="{{ old('nilai_diskon', isset($promoKode) ? (float) $promoKode->nilai_diskon : '') }}" required class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-primary-500">
                    @error('nilai_diskon') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-bold text-slate-800">Minimal Belanja</label>
                    <input type="number" min="0" name="min_belanja" value="{{ old('min_belanja', isset($promoKode) ? (float) $promoKode->min_belanja : 0) }}" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-800">Maksimal Diskon (opsional)</label>
                    <input type="number" min="0" name="maks_diskon" value="{{ old('maks_diskon', isset($promoKode) && $promoKode->maks_diskon !== null ? (float) $promoKode->maks_diskon : '') }}" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="text-sm font-bold text-slate-800">Kuota (opsional)</label>
                    <input type="number" min="0" name="kuota" value="{{ old('kuota', $promoKode->kuota ?? '') }}" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-800">Tanggal Mulai <span class="text-xs font-normal text-slate-500">(WIB)</span></label>
                    <input type="datetime-local" name="tanggal_mulai" value="{{ old('tanggal_mulai', isset($promoKode) && $promoKode->tanggal_mulai ? $promoKode->tanggal_mulai->copy()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i') : '') }}" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="text-sm font-bold text-slate-800">Tanggal Berakhir <span class="text-xs font-normal text-slate-500">(WIB)</span></label>
                    <input type="datetime-local" name="tanggal_berakhir" value="{{ old('tanggal_berakhir', isset($promoKode) && $promoKode->tanggal_berakhir ? $promoKode->tanggal_berakhir->copy()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i') : '') }}" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm font-bold text-slate-800">
                <input type="checkbox" name="is_aktif" value="1" class="rounded border-slate-300 text-primary-600" {{ old('is_aktif', $promoKode->is_aktif ?? true) ? 'checked' : '' }}>
                Promo aktif
            </label>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2">
                <a href="{{ route('promo-kodes.index') }}" class="px-4 py-2.5 border border-slate-200 text-slate-700 rounded-xl font-semibold text-sm hover:bg-slate-50 text-center">Batal</a>
                <button class="px-4 py-2.5 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 shadow-md shadow-primary-600/20" type="submit">
                    {{ isset($promoKode) ? 'Simpan Perubahan' : 'Simpan Promo' }}
                </button>
            </div>
        </form>
    </main>
</div>
@endsection