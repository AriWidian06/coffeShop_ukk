@extends('layouts.kasir')

@section('content')
<div class="min-h-screen bg-slate-50">
    @php
        $statusLabels = [
            'pending' => 'Menunggu',
            'ready' => 'Siap',
            'prosesed' => 'Diproses',
            'completed' => 'Selesai',
        ];
    @endphp

    <!-- TOP HEADER -->
    <header class="kasir-header">
        <div class="flex items-center justify-between mb-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-slate-900">Dashboard Kasir</h1>
                    <span class="kasir-badge active">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                        Mode Operasional Cepat Aktif
                    </span>
                </div>
                <p class="text-sm text-slate-500">Pesanan POS dan web kafe untuk ditangani.</p>
            </div>
            <div class="kasir-info-bar">
                <div class="kasir-badge">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Shift 1 (07:00 - 15:00)
                </div>
                <div class="kasir-badge">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Kasir: {{ auth('karyawan')->user()->nama_karyawan }}
                </div>
                <div class="kasir-badge">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ now()->format('H:i') }} WIB
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex items-center gap-3">
            <a href="{{ route('transaksis.create') }}" class="px-4 py-2.5 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Pesanan Baru
            </a>
            <a href="{{ route('transaksis.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-semibold text-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Lihat Semua Transaksi
            </a>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="p-6">
        @if ($transaksiTerbaru->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Belum ada transaksi</h3>
                <p class="text-sm text-slate-500 mb-6">Pesanan POS dan web kafe akan muncul di sini.</p>
                <a href="{{ route('transaksis.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Pesanan Pertama
                </a>
            </div>
        @else
            <!-- Transactions Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="font-bold text-slate-900 text-lg">Transaksi Terbaru</h2>
                    <a href="{{ route('transaksis.index') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700">Lihat semua →</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="transaction-table">
                        <thead>
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Detail Pesanan</th>
                                <th>Sumber</th>
                                <th class="text-right">Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($transaksiTerbaru as $transaksi)
                                <tr>
                                    <td>
                                        <div class="font-bold text-slate-900 text-sm">#{{ $transaksi->id }}</div>
                                        <div class="text-xs text-slate-500 mt-1">{{ $transaksi->waktu_transaksi?->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td>
                                        <div class="text-sm font-semibold text-slate-900">
                                            {{ $transaksi->tipe_pesanan === 'take-away' ? 'Bawa Pulang' : 'Makan di Tempat' }}
                                        </div>
                                        <div class="text-xs text-slate-500 mt-1">
                                            {{ $transaksi->meja ? 'Meja ' . $transaksi->meja->nomor_meja : 'Tanpa meja' }}
                                        </div>
                                        <div class="text-xs text-slate-600 mt-2 space-y-1">
                                            @foreach ($transaksi->detail_transaksis->take(2) as $detail)
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-1 h-1 rounded-full bg-slate-400"></span>
                                                    <span>{{ $detail->produk->nama_produk ?? 'Produk dihapus' }} x{{ $detail->QTY ?? $detail->qty }}</span>
                                                    @if ($detail->customization)
                                                        <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 text-[10px] rounded border border-slate-200 italic">{{ $detail->customization }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if ($transaksi->detail_transaksis->count() > 2)
                                                <div class="text-slate-500 italic">+ {{ $transaksi->detail_transaksis->count() - 2 }} produk lain</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if ($transaksi->sumber_pesanan === 'web')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-bold">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                                                Web
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-purple-50 text-purple-700 rounded-md text-xs font-bold">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                                POS
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="font-bold text-slate-900 text-sm">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'ready' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'prosesed' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            ];
                                            $statusClass = $statusColors[$transaksi->status_pesanan] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                                            {{ $statusLabels[$transaksi->status_pesanan] ?? $transaksi->status_pesanan }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('transaksis.show', $transaksi) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-50 text-primary-700 rounded-lg text-xs font-bold hover:bg-primary-100 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </main>
</div>
@endsection