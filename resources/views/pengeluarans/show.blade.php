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
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Pengeluaran / <span class="text-primary-600">Detail</span></p>
            <h1 class="text-2xl font-bold text-slate-900">Detail Pengeluaran</h1>
        </div>
        <a href="{{ route('pengeluarans.index') }}" class="self-start px-4 py-2.5 border border-slate-200 bg-white rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Kembali</a>
    </header>

    <div class="max-w-3xl grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-500">{{ $pengeluaran->tanggal->translatedFormat('d F Y') }}</p>
                    <h2 class="text-xl font-bold text-slate-900 mt-1">{{ $pengeluaran->deskripsi }}</h2>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    {{ $pengeluaran->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($pengeluaran->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                    {{ $pengeluaran->status_label }}
                </span>
            </div>

            <p class="text-3xl font-bold text-slate-900">Rp{{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</p>

            <dl class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4 text-sm">
                <div><dt class="text-slate-500">Kategori</dt><dd class="font-semibold text-slate-800">{{ $pengeluaran->kategori_label }}</dd></div>
                <div><dt class="text-slate-500">Supplier</dt><dd class="font-semibold text-slate-800">{{ $pengeluaran->supplier?->nama_supplier ?? '-' }}</dd></div>
                <div><dt class="text-slate-500">Metode</dt><dd class="font-semibold text-slate-800">{{ ucfirst($pengeluaran->metode) }}</dd></div>
                <div><dt class="text-slate-500">Akun Kas</dt><dd class="font-semibold text-slate-800">{{ $pengeluaran->account?->nama ?? '-' }}</dd></div>
                <div><dt class="text-slate-500">Diajukan Oleh</dt><dd class="font-semibold text-slate-800">{{ $pengeluaran->karyawan?->nama_karyawan ?? '-' }}</dd></div>
                <div><dt class="text-slate-500">Disetujui Oleh</dt><dd class="font-semibold text-slate-800">{{ $pengeluaran->approver?->nama_karyawan ?? '-' }}</dd></div>
            </dl>

            @if ($pengeluaran->catatan)
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Catatan Pengaju</p>
                    <p class="text-sm text-slate-700">{{ $pengeluaran->catatan }}</p>
                </div>
            @endif

            @if ($pengeluaran->catatan_approval)
                <div class="bg-amber-50 rounded-xl p-3">
                    <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-1">Catatan Persetujuan</p>
                    <p class="text-sm text-amber-900">{{ $pengeluaran->catatan_approval }}</p>
                </div>
            @endif

            @if ($pengeluaran->bukti_url)
                <a href="{{ $pengeluaran->bukti_url }}" target="_blank" class="inline-block px-3 py-2 border border-slate-200 rounded-xl text-sm font-bold text-primary-600 hover:bg-slate-50">Buka Bukti</a>
            @endif

            @if ($pengeluaran->approved_at)
                <p class="text-xs text-slate-400">{{ $pengeluaran->status_label }} pada {{ $pengeluaran->approved_at->translatedFormat('d M Y H:i') }} WIB</p>
            @endif
        </div>

        @can('approve-pengeluaran')
            @if ($pengeluaran->status === 'pending')
                <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-sm h-fit space-y-4">
                    <h3 class="font-bold text-slate-900">Persetujuan</h3>
                    <form method="POST" action="{{ route('pengeluarans.approve', $pengeluaran) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="text-xs font-bold text-slate-600">Catatan <span class="font-normal">(opsional)</span></label>
                            <textarea name="catatan_approval" rows="2" maxlength="500" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 text-sm"></textarea>
                        </div>
                        <button class="w-full px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-bold hover:bg-emerald-700">Setujui &amp; Potong Kas</button>
                    </form>
                    <form method="POST" action="{{ route('pengeluarans.reject', $pengeluaran) }}" class="space-y-3 border-t border-slate-100 pt-4">
                        @csrf
                        <div>
                            <label class="text-xs font-bold text-slate-600">Alasan Penolakan</label>
                            <textarea name="catatan_approval" rows="2" required maxlength="500" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 text-sm"></textarea>
                            @error('catatan_approval') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button class="w-full px-4 py-2.5 border border-red-200 text-red-600 rounded-xl text-sm font-bold hover:bg-red-50">Tolak Pengajuan</button>
                    </form>
                </div>
            @endif
        @endcan

        @can('input-pengeluaran')
            @if ($pengeluaran->status === 'pending')
                <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-sm h-fit space-y-3">
                    <h3 class="font-bold text-slate-900">Aksi Pengaju</h3>
                    <a href="{{ route('pengeluarans.edit', $pengeluaran) }}" class="block w-full text-center px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Ubah Pengajuan</a>
                    <form method="POST" action="{{ route('pengeluarans.destroy', $pengeluaran) }}" onsubmit="return confirm('Batalkan pengajuan ini?')">
                        @csrf @method('DELETE')
                        <button class="w-full px-4 py-2.5 border border-red-200 text-red-600 rounded-xl text-sm font-bold hover:bg-red-50">Batalkan Pengajuan</button>
                    </form>
                </div>
            @endif
        @endcan
    </div>
</div>
@endsection
