@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50">

    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">
                        Promo / <span class="text-primary-600">Kombinasi Diskon</span>
                    </p>
                    <h1 class="text-2xl font-bold text-slate-900">Pengelolaan Kode Promo Diskon</h1>
                    <p class="text-sm text-slate-500 mt-1">Kelola kode promo persentase, batas pemakaian, dan periode aktif.</p>
                </div>
                <a href="{{ route('promo-kodes.create') }}" class="px-4 py-2.5 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Kode Promo
                </a>
            </div>
        </div>
    </header>

    <main class="p-6 max-w-7xl mx-auto space-y-6">

        @if (session('success'))
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left">Kode</th>
                            <th class="px-4 py-3 text-left">Nama Promo</th>
                            <th class="px-4 py-3 text-left">Diskon</th>
                            <th class="px-4 py-3 text-left">Min Belanja</th>
                            <th class="px-4 py-3 text-left">Maks Diskon</th>
                            <th class="px-4 py-3 text-left">Periode</th>
                            <th class="px-4 py-3 text-left">Kuota</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($promoKodes as $promo)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $promo->kode }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $promo->nama_promo ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ number_format((float) $promo->nilai_diskon, 0) }}%</td>
                                <td class="px-4 py-3 text-slate-700">Rp {{ number_format((float) $promo->min_belanja, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ $promo->maks_diskon !== null ? 'Rp ' . number_format((float) $promo->maks_diskon, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-xs">
                                    {{ optional($promo->tanggal_mulai)->format('d/m/Y') ?? 'mulai sekarang' }} -
                                    {{ optional($promo->tanggal_berakhir)->format('d/m/Y') ?? 'tanpa batas' }}
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ $promo->kuota_terpakai }}{{ $promo->kuota ? '/' . $promo->kuota : '' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-md text-xs font-bold {{ $promo->is_aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $promo->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-1">
                                    <a href="{{ route('promo-kodes.edit', $promo) }}" class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50">Edit</a>
                                    <form action="{{ route('promo-kodes.destroy', $promo) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kode promo ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1.5 border border-red-200 rounded-lg text-xs font-bold text-red-600 hover:bg-red-50">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-slate-500">Belum ada kode promo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $promoKodes->links() }}</div>
    </main>
</div>
@endsection