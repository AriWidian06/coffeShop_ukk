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
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Keuangan / <span class="text-primary-600">Kas</span></p>
            <h1 class="text-2xl font-bold text-slate-900">Saldo &amp; Mutasi Kas</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau saldo tunai dan bank/QRIS secara real-time.</p>
        </div>
        <div class="flex flex-wrap gap-2 self-start">
            <a href="{{ route('kas.mutasi') }}" class="px-3 py-2 border border-slate-200 bg-white rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Lihat Mutasi</a>
            @can('tutup-kas')
                <a href="{{ route('kas.tutup-hari') }}" class="px-3 py-2 bg-primary-600 text-white rounded-xl text-sm font-bold hover:bg-primary-700">Tutup Hari</a>
            @endcan
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
        @foreach ($accounts as $account)
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-sm font-bold text-slate-700">{{ $account->nama }}</p>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary-50 text-primary-700 uppercase tracking-wider">{{ $account->kode }}</span>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-3">Rp{{ number_format($account->saldo_saat_ini, 0, ',', '.') }}</p>
                <div class="mt-3 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                    <div><p class="text-slate-500">Masuk hari ini</p><p class="font-bold text-emerald-600">+Rp{{ number_format($account->ringkasan_hari_ini['total_masuk'], 0, ',', '.') }}</p></div>
                    <div><p class="text-slate-500">Keluar hari ini</p><p class="font-bold text-red-600">-Rp{{ number_format($account->ringkasan_hari_ini['total_keluar'], 0, ',', '.') }}</p></div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-slate-900 text-white rounded-2xl p-5 mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <p class="text-xs uppercase tracking-wider text-slate-400 font-bold">Total Saldo Kas</p>
            <p class="text-3xl font-bold mt-1">Rp{{ number_format($totalSaldo, 0, ',', '.') }}</p>
        </div>
        @can('kelola-kas')
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('kas.saldo-awal') }}" class="px-3 py-2 bg-white/10 rounded-xl text-xs font-bold hover:bg-white/20">Saldo Awal</a>
                <a href="{{ route('kas.transfer') }}" class="px-3 py-2 bg-white/10 rounded-xl text-xs font-bold hover:bg-white/20">Transfer</a>
                <a href="{{ route('kas.penyesuaian') }}" class="px-3 py-2 bg-white/10 rounded-xl text-xs font-bold hover:bg-white/20">Penyesuaian</a>
            </div>
        @endcan
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-bold text-slate-900">Mutasi Terbaru</h2>
                <a href="{{ route('kas.mutasi') }}" class="text-xs font-bold text-primary-600 hover:underline">Semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentMovements as $movement)
                    <div class="px-4 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $movement->keterangan }}</p>
                            <p class="text-xs text-slate-400">{{ $movement->tanggal->translatedFormat('d M Y') }} · {{ $movement->account?->nama }} · {{ $movement->jenis_label }}</p>
                        </div>
                        <p class="text-sm font-bold whitespace-nowrap {{ $movement->debit > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $movement->debit > 0 ? '+' : '-' }}Rp{{ number_format($movement->debit > 0 ? $movement->debit : $movement->kredit, 0, ',', '.') }}
                        </p>
                    </div>
                @empty
                    <p class="px-4 py-8 text-center text-sm text-slate-500">Belum ada mutasi kas.</p>
                @endforelse
            </div>
        </section>

        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100">
                <h2 class="font-bold text-slate-900">Tutup Hari Terbaru</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentClosings as $closing)
                    <div class="px-4 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800">{{ $closing->account?->nama }} · {{ $closing->tanggal->translatedFormat('d M Y') }}</p>
                            <p class="text-xs text-slate-400">Buku Rp{{ number_format($closing->saldo_buku, 0, ',', '.') }} · Fisik Rp{{ number_format($closing->saldo_fisik, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold {{ abs($closing->selisih) > 0 ? 'text-amber-600' : 'text-emerald-600' }}">Selisih Rp{{ number_format($closing->selisih, 0, ',', '.') }}</p>
                            <p class="text-[10px] font-bold text-slate-500 mt-1">{{ $closing->verified_at ? 'Terverifikasi' : 'Menunggu verifikasi' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="px-4 py-8 text-center text-sm text-slate-500">Belum ada tutup hari.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
