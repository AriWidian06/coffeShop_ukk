@php
    $role = auth('karyawan')->user()->role ?? 'kasir';
    $activeLayout = match($role) {
        'kasir' => 'layouts.kasir',
        'manager' => 'layouts.manager',
        default => 'layouts.app',
    };

    $statusLabels = [
        'pending' => 'Menunggu',
        'prosesed' => 'Diproses',
        'ready' => 'Siap',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];
@endphp

@extends($activeLayout)

@section('content')
<div class="transaction-history min-h-screen bg-slate-50 p-4 sm:p-6">

    <!-- TOP HEADER -->
    <header class="relative overflow-hidden bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 mb-4 sm:mb-6 shadow-sm">
        <div class="absolute -right-16 -top-24 w-64 h-64 rounded-full bg-primary-100/60 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-28 w-56 h-56 rounded-full bg-slate-100/80 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col md:flex-row md:items-start justify-between gap-4 mb-5 sm:mb-6">
            <div class="min-w-0">
                <div class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-500 mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                    Operasional
                    <span class="text-slate-300">/</span>
                    <span class="text-primary-600">Laporan Transaksi</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Riwayat Pesanan</h1>
                <p class="text-sm text-slate-500 mt-1.5">Pantau semua transaksi dari POS kasir dan pemesanan web.</p>
            </div>
            @if (in_array($role, ['kasir', 'admin'], true))
                <a href="{{ route('transaksis.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-xl font-bold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Pesanan Baru
                </a>
            @endif
        </div>

        <!-- Filters -->
        <form method="get" action="{{ route('transaksis.index') }}" class="relative bg-slate-50/80 rounded-2xl p-3 sm:p-4 border border-slate-200 flex flex-col lg:flex-row gap-3 sm:gap-4 items-stretch lg:items-end">
            <div class="flex-1 w-full">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Cari Transaksi / Meja</label>
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Contoh: ID atau No. Meja" class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-400 transition-all">
                </div>
            </div>
            <div class="w-full lg:w-48">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Pembayaran</label>
                <select name="metode_pembayaran" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua metode</option>
                    <option value="cash" {{ request('metode_pembayaran') === 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="QRIS" {{ request('metode_pembayaran') === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                </select>
            </div>

            <div class="w-full lg:w-48">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Status Pesanan</label>
                <select name="status" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-400 transition-all">
                    <option value="">Semua status</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 w-full lg:w-auto">
                <button type="submit" class="flex-1 lg:flex-none px-6 py-2.5 bg-primary-600 text-white rounded-xl font-bold text-sm hover:bg-primary-700 transition-all shadow-sm shadow-primary-600/20 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Tampilkan
                </button>
                @if (request()->filled('q') || request()->filled('status') || request()->filled('metode_pembayaran'))
                    <a href="{{ route('transaksis.index') }}" class="flex-1 lg:flex-none px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 hover:border-slate-300 transition-all flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </header>

    <!-- MAIN CONTENT -->
    <main>
        <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-primary-600 flex items-center justify-center shadow-sm shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="text-sm font-bold text-slate-900 leading-tight">Daftar Transaksi</h2>
                    <p class="text-xs text-slate-500">Menampilkan <span class="font-bold text-slate-900">{{ isset($transaksis) ? $transaksis->total() : 0 }}</span> transaksi</p>
                    <p class="text-xs text-slate-500 mt-0.5">Total nominal terfilter: <span class="font-bold text-slate-900">Rp {{ number_format((float) ($totalNominalTerfilter ?? 0), 0, ',', '.') }}</span></p>
                </div>
            </div>

            @if (request()->filled('q') || request()->filled('status') || request()->filled('metode_pembayaran'))
                <div class="flex flex-wrap items-center gap-1.5 text-[10px] font-bold">
                    <span class="text-slate-400 uppercase tracking-wider">Filter aktif:</span>
                    @if (request()->filled('q'))
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-white border border-slate-200 text-slate-600 max-w-[240px] truncate">
                            <span class="text-slate-400 shrink-0">Cari:</span> {{ request('q') }}
                        </span>
                    @endif
                    @if (request()->filled('status'))
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-primary-50 border border-primary-100 text-primary-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                            {{ $statusLabels[request('status')] ?? request('status') }}
                        </span>
                    @endif
                    @if (request()->filled('metode_pembayaran'))
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ request('metode_pembayaran') === 'cash' ? 'Tunai' : request('metode_pembayaran') }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

        @if (isset($transaksis) && $transaksis->count())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="history-scroll overflow-x-auto">
                    <table class="history-table w-full text-left border-collapse min-w-[900px]">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/80 text-slate-500 text-[10px] uppercase tracking-[0.14em]">
                                <th class="py-3 px-4 font-bold">Transaksi</th>
                                <th class="py-3 px-4 font-bold">Detail Pesanan</th>
                                <th class="py-3 px-4 font-bold">Sumber / Kasir</th>
                                <th class="py-3 px-4 font-bold text-right">Total</th>
                                <th class="py-3 px-4 font-bold text-center">Status</th>
                                <th class="py-3 px-4 font-bold text-center">Pembayaran</th>
                                <th class="py-3 px-4 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($transaksis as $transaksi)
                                <tr class="history-row transition">
                                    <td class="py-4 px-4 align-top">
                                        <span class="inline-flex items-center rounded-lg bg-primary-50 border border-primary-100 px-2 py-1 text-xs font-bold text-primary-700 font-mono">#{{ $transaksi->id }}</span>
                                        <div class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1.5 whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            {{ optional($transaksi->waktu_transaksi)->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 align-top">
                                        <div class="text-sm font-bold text-slate-900">
                                            {{ $transaksi->tipe_pesanan === 'take-away' ? 'Bawa Pulang' : 'Makan di Tempat' }}
                                        </div>
                                        <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                            {{ $transaksi->meja ? 'Meja ' . $transaksi->meja->nomor_meja : 'Tanpa meja' }}
                                        </div>
                                        <div class="text-xs text-slate-600 mt-2.5 space-y-1.5">
                                            @if($transaksi->detail_transaksis)
                                                @foreach ($transaksi->detail_transaksis->take(2) as $detail)
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-primary-300 shrink-0"></span>
                                                        <span>{{ optional($detail->produk)->nama_produk ?? 'Produk dihapus' }} x{{ $detail->QTY ?? $detail->qty }}</span>
                                                        @if ($detail->customization)
                                                            <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 text-[10px] rounded border border-slate-200 italic">{{ $detail->customization }}</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                @if ($transaksi->detail_transaksis->count() > 2)
                                                    <div class="text-slate-500 italic pl-3">+ {{ $transaksi->detail_transaksis->count() - 2 }} produk lain</div>
                                                @endif
                                            @endif
                                        </div>
                                        @if ($transaksi->catatan)
                                            <div class="mt-2 text-xs text-amber-700 bg-amber-50 px-2 py-1 rounded-lg border border-amber-200 inline-block">
                                                Catatan: {{ $transaksi->catatan }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 align-top">
                                        @if ($transaksi->sumber_pesanan === 'web')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-xs font-bold">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                                                Web Kafe
                                            </span>
                                        @else
                                            <div class="flex items-center gap-2">
                                                <span class="w-7 h-7 rounded-full bg-slate-100 border border-slate-200 text-slate-500 flex items-center justify-center shrink-0">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                </span>
                                                <span class="text-sm text-slate-700 font-semibold">{{ optional($transaksi->karyawan)->nama_karyawan ?? '-' }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 align-top text-right">
                                        <div class="font-black text-slate-900 text-sm whitespace-nowrap">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="py-4 px-4 align-top text-center">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'ready' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'prosesed' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            ];
                                            $statusClass = $statusColors[$transaksi->status_pesanan] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border whitespace-nowrap {{ $statusClass }}">
                                            {{ $statusLabels[$transaksi->status_pesanan] ?? $transaksi->status_pesanan }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 align-top text-center">
                                        @if ($transaksi->pembayaran)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 whitespace-nowrap">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                Lunas
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap">
                                                Belum Dibayar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 align-top text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if (!$transaksi->pembayaran)
                                                <a href="{{ route('kasir.payment.qris', $transaksi->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition shadow-sm whitespace-nowrap">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                                    Bayar QRIS
                                                </a>
                                            @endif
                                            <a href="{{ route('transaksis.show', $transaksi->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-50 text-primary-700 border border-primary-100 rounded-lg text-xs font-bold hover:bg-primary-100 transition whitespace-nowrap">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                Rincian
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($transaksis->hasPages())
                    <div class="px-4 sm:px-6 py-4 border-t border-slate-200 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-xs text-slate-500">
                            Halaman <span class="font-bold text-slate-900">{{ $transaksis->currentPage() }}</span> dari <span class="font-bold text-slate-900">{{ $transaksis->lastPage() }}</span>
                        </p>
                        <div class="overflow-x-auto">{{ $transaksis->links() }}</div>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 sm:p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-dashed border-slate-200 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-1">Belum ada transaksi</h3>
                <p class="text-sm text-slate-500 mb-4">Pesanan dari POS dan web kafe akan muncul di sini.</p>
                @if (request()->filled('q') || request()->filled('status'))
                    <a href="{{ route('transaksis.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">
                        Reset Filter
                    </a>
                @endif
            </div>
        @endif
    </main>
</div>
@endsection
