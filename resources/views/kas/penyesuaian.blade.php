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
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Kas / <span class="text-primary-600">Penyesuaian</span></p>
            <h1 class="text-2xl font-bold text-slate-900">Penyesuaian Saldo</h1>
            <p class="text-sm text-slate-500 mt-1">Gunakan untuk koreksi manual, misalnya selisih hasil tutup hari.</p>
        </div>
        <a href="{{ route('kas.index') }}" class="self-start px-4 py-2.5 border border-slate-200 bg-white rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Kembali</a>
    </header>

    <form method="POST" action="{{ route('kas.penyesuaian.store') }}" class="max-w-xl bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-sm space-y-4">
        @csrf
        <div>
            <label class="text-sm font-bold text-slate-800">Akun Kas</label>
            <select name="kas_account_id" required class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}" {{ old('kas_account_id') == $account->id ? 'selected' : '' }}>{{ $account->nama }}</option>
                @endforeach
            </select>
            @error('kas_account_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-bold text-slate-800">Arah</label>
                <select name="arah" required class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">
                    <option value="debit" {{ old('arah') === 'debit' ? 'selected' : '' }}>Tambah Saldo (Masuk)</option>
                    <option value="kredit" {{ old('arah') === 'kredit' ? 'selected' : '' }}>Kurangi Saldo (Keluar)</option>
                </select>
                @error('arah') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-bold text-slate-800">Jumlah (Rp)</label>
                <input type="number" name="jumlah" required min="0.01" step="0.01" value="{{ old('jumlah') }}" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">
                @error('jumlah') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div>
            <label class="text-sm font-bold text-slate-800">Keterangan</label>
            <input type="text" name="keterangan" required maxlength="255" value="{{ old('keterangan') }}" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">
            @error('keterangan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="px-4 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-bold hover:bg-primary-700">Simpan Penyesuaian</button>
    </form>
</div>
@endsection
