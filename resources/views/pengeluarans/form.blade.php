@php
    $activeLayout = match (auth('karyawan')->user()->role ?? 'kasir') {
        'kasir' => 'layouts.kasir',
        'manager' => 'layouts.manager',
        default => 'layouts.app',
    };
@endphp
@extends($activeLayout)

@section('content')
<div class="min-h-screen bg-slate-50 p-4 sm:p-6">
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Pengeluaran / <span class="text-primary-600">{{ $pengeluaran->exists ? 'Ubah' : 'Ajukan' }}</span></p>
            <h1 class="text-2xl font-bold text-slate-900">{{ $pengeluaran->exists ? 'Ubah Pengajuan' : 'Ajukan Pengeluaran' }}</h1>
            <p class="text-sm text-slate-500 mt-1">Pengajuan baru akan menunggu persetujuan manager.</p>
        </div>
        <a href="{{ route('pengeluarans.index') }}" class="self-start px-4 py-2.5 border border-slate-200 bg-white rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Kembali</a>
    </header>

    <form method="POST" action="{{ $pengeluaran->exists ? route('pengeluarans.update', $pengeluaran) : route('pengeluarans.store') }}" class="max-w-3xl bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-sm space-y-4" enctype="multipart/form-data">
        @csrf
        @if ($pengeluaran->exists) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-bold text-slate-800">Kategori</label>
                <select name="kategori" required class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">
                    @foreach (['bahan_baku' => 'Bahan Baku', 'operasional' => 'Operasional', 'gaji' => 'Gaji Karyawan', 'lainnya' => 'Lainnya'] as $value => $label)
                        <option value="{{ $value }}" {{ old('kategori', $pengeluaran->kategori) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('kategori') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-bold text-slate-800">Supplier <span class="font-normal text-slate-400">(opsional)</span></label>
                <select name="supplier_id" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">
                    <option value="">Tidak ada</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $pengeluaran->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->nama_supplier }}</option>
                    @endforeach
                </select>
                @error('supplier_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="text-sm font-bold text-slate-800">Deskripsi Pengeluaran</label>
            <input type="text" name="deskripsi" value="{{ old('deskripsi', $pengeluaran->deskripsi) }}" required maxlength="255" placeholder="Contoh: Pembelian biji kopi arabika" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">
            @error('deskripsi') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-sm font-bold text-slate-800">Jumlah (Rp)</label>
                <input type="number" name="jumlah" value="{{ old('jumlah', $pengeluaran->exists ? (float) $pengeluaran->jumlah : '') }}" required min="0.01" step="0.01" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">
                @error('jumlah') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-bold text-slate-800">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $pengeluaran->tanggal?->toDateString()) }}" required class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">
                @error('tanggal') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-bold text-slate-800">Metode</label>
                <select name="metode" required class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">
                    @foreach (['tunai' => 'Tunai (Kas Tunai)', 'transfer' => 'Transfer (Bank / QRIS)'] as $value => $label)
                        <option value="{{ $value }}" {{ old('metode', $pengeluaran->metode) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('metode') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="text-sm font-bold text-slate-800">Bukti <span class="font-normal text-slate-400">(jpg/png/pdf, maks. 2 MB)</span></label>
            <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">
            @if ($pengeluaran->bukti_url)
                <a href="{{ $pengeluaran->bukti_url }}" target="_blank" class="inline-block mt-2 text-xs font-bold text-primary-600 hover:underline">Lihat bukti saat ini</a>
            @endif
            @error('bukti') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="text-sm font-bold text-slate-800">Catatan <span class="font-normal text-slate-400">(opsional)</span></label>
            <textarea name="catatan" rows="3" maxlength="1000" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">{{ old('catatan', $pengeluaran->catatan) }}</textarea>
            @error('catatan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2">
            <a href="{{ route('pengeluarans.index') }}" class="px-4 py-2.5 border border-slate-200 text-slate-700 rounded-xl font-bold text-sm text-center hover:bg-slate-50">Batal</a>
            <button type="submit" class="px-4 py-2.5 bg-primary-600 text-white rounded-xl font-bold text-sm hover:bg-primary-700">{{ $pengeluaran->exists ? 'Simpan Perubahan' : 'Ajukan Pengeluaran' }}</button>
        </div>
    </form>
</div>
@endsection
