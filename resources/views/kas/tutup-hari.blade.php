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
    <header class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Kas / <span class="text-primary-600">Tutup Hari</span></p>
            <h1 class="text-2xl font-bold text-slate-900">Tutup Hari Kas</h1>
            <p class="text-sm text-slate-500 mt-1">Hitung saldo buku vs saldo fisik, lalu ajukan ke manager untuk verifikasi.</p>
        </div>
        <a href="{{ route('kas.index') }}" class="self-start px-4 py-2.5 border border-slate-200 bg-white rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Kembali</a>
    </header>

    <form method="GET" action="{{ route('kas.tutup-hari') }}" class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm mb-5 flex flex-col sm:flex-row sm:items-end gap-3">
        <div class="sm:max-w-xs">
            <label class="text-xs font-bold text-slate-600">Tanggal</label>
            <input type="date" name="tanggal" value="{{ $tanggal->toDateString() }}" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <button class="px-4 py-2 bg-slate-800 text-white rounded-xl text-sm font-bold self-start sm:self-auto">Tampilkan</button>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach ($accounts as $account)
            @php $r = $account->ringkasan; @endphp
            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="font-bold text-slate-900">{{ $account->nama }}</h2>
                        <p class="text-xs text-slate-400">{{ $tanggal->translatedFormat('d F Y') }}</p>
                    </div>
                    @if ($account->closing)
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $account->closing->verified_at ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $account->closing->verified_at ? 'Terverifikasi' : 'Menunggu Verifikasi' }}
                        </span>
                    @endif
                </div>

                <dl class="grid grid-cols-2 gap-3 text-sm mb-4">
                    <div class="bg-slate-50 rounded-xl p-3"><dt class="text-xs text-slate-500">Saldo Awal</dt><dd class="font-bold text-slate-800 mt-0.5">Rp{{ number_format($r['saldo_awal'], 0, ',', '.') }}</dd></div>
                    <div class="bg-emerald-50 rounded-xl p-3"><dt class="text-xs text-emerald-600">Total Masuk</dt><dd class="font-bold text-emerald-700 mt-0.5">Rp{{ number_format($r['total_masuk'], 0, ',', '.') }}</dd></div>
                    <div class="bg-red-50 rounded-xl p-3"><dt class="text-xs text-red-600">Total Keluar</dt><dd class="font-bold text-red-700 mt-0.5">Rp{{ number_format($r['total_keluar'], 0, ',', '.') }}</dd></div>
                    <div class="bg-slate-900 rounded-xl p-3"><dt class="text-xs text-slate-400">Saldo Buku</dt><dd class="font-bold text-white mt-0.5">Rp{{ number_format($r['saldo_buku'], 0, ',', '.') }}</dd></div>
                </dl>

                <form method="POST" action="{{ route('kas.tutup-hari.store') }}" class="space-y-3 border-t border-slate-100 pt-4">
                    @csrf
                    <input type="hidden" name="kas_account_id" value="{{ $account->id }}">
                    <input type="hidden" name="tanggal" value="{{ $tanggal->toDateString() }}">
                    <div>
                        <label class="text-xs font-bold text-slate-700">Saldo Fisik (Rp)</label>
                        <input type="number" name="saldo_fisik" required min="0" step="0.01" value="{{ old('saldo_fisik', $account->closing ? (float) $account->closing->saldo_fisik : (float) $r['saldo_buku']) }}" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-700">Catatan <span class="font-normal text-slate-400">(wajib bila ada selisih)</span></label>
                        <textarea name="catatan" rows="2" maxlength="500" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 text-sm">{{ old('catatan', $account->closing?->catatan) }}</textarea>
                        @error('catatan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full px-4 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-bold hover:bg-primary-700">Simpan Tutup Hari</button>
                </form>

                @can('verifikasi-kas')
                    @if ($account->closing && ! $account->closing->verified_at)
                        <form method="POST" action="{{ route('kas.verifikasi', $account->closing) }}" class="mt-3 border-t border-slate-100 pt-3">
                            @csrf
                            <button class="w-full px-4 py-2 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-bold hover:bg-emerald-50">Verifikasi Closing Ini</button>
                        </form>
                    @endif
                @endcan
            </div>
        @endforeach
    </div>
</div>
@endsection
