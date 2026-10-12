@php
    // DETEKSI ROLE SECARA DINAMIS UNTUK MEMILIH LAYOUT
    $role = auth('karyawan')->user()->role;
    if ($role === 'kasir') {
        $activeLayout = 'layouts.kasir';
    } elseif ($role === 'manager') {
        $activeLayout = 'layouts.manager';
    } else {
        $activeLayout = 'layouts.app'; // Fallback untuk admin
    }

    $statusLabels = [
        'pending' => 'Menunggu',
        'ready' => 'Siap',
        'prosesed' => 'Diproses',
        'completed' => 'Selesai',
    ];
@endphp

@extends($activeLayout)

@section('content')
<div class="min-h-screen bg-slate-50">
    
    <!-- TOP HEADER -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">
                        Operasional / <span class="text-primary-600">Laporan Transaksi</span>
                    </p>
                    <h1 class="text-2xl font-bold text-slate-900">Riwayat Pesanan</h1>
                    <p class="text-sm text-slate-500 mt-1">Pantau semua transaksi dari POS kasir dan pemesanan web.</p>
                </div>
                @if (in_array($role, ['kasir', 'admin'], true))
                    <a href="{{ route('transaksis.create') }}" class="px-4 py-2.5 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Pesanan Baru
                    </a>
                @endif
            </div>

            <!-- Filters -->
            <form method="get" action="{{ route('transaksis.index') }}" class="bg-slate-50 rounded-xl p-4 border border-slate-200 flex flex-col lg:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Cari Transaksi / Meja</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Contoh: ID atau No. Meja" class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div class="w-full lg:w-48">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Pesanan</label>
                    <select name="status" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua status</option>
                        @foreach ($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 w-full lg:w-auto">
                    <button type="submit" class="flex-1 lg:flex-none px-6 py-2.5 bg-primary-600 text-white rounded-lg font-semibold text-sm hover:bg-primary-700 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Tampilkan
                    </button>
                    @if (request()->filled('q') || request()->filled('status'))
                        <a href="{{ route('transaksis.index') }}" class="flex-1 lg:flex-none px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-lg font-semibold text-sm hover:bg-slate-50 transition-all flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="p-6">
        <div class="mb-4 flex items-center justify-between">
            <p class="text-sm text-slate-600">Menampilkan <span class="font-bold text-slate-900">{{ $transaksis->total() }}</span> transaksi</p>
        </div>

        @if ($transaksis->count())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="transaction-table">
                        <thead>
                            <tr>
                                <th>Transaksi</th>
                                <th>Detail Pesanan</th>
                                <th>Sumber / Kasir</th>
                                <th class="text-right">Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Pembayaran</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($transaksis as $transaksi)
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
                                        @if ($transaksi->catatan)
                                            <div class="mt-2 text-xs text-amber-700 bg-amber-50 px-2 py-1 rounded border border-amber-200 inline-block">
                                                Catatan: {{ $transaksi->catatan }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($transaksi->sumber_pesanan === 'web')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-bold">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                                                Web Kafe
                                            </span>
                                        @else
                                            <div class="text-sm text-slate-700 font-medium">{{ $transaksi->karyawan->nama_karyawan ?? '-' }}</div>
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
                                    <td class="text-center">
                                        @if ($transaksi->pembayaran)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                Lunas
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                Belum Dibayar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('transaksis.show', $transaksi) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-50 text-primary-700 rounded-lg text-xs font-bold hover:bg-primary-100 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            Rincian
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($transaksis->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                        <p class="text-sm text-slate-600">
                            Halaman {{ $transaksis->currentPage() }} dari {{ $transaksis->lastPage() }}
                        </p>
                        {{ $transaksis->links() }}
                    </div>
                @endif
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                @if (request()->filled('q') || request()->filled('status'))
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Tidak ada transaksi yang cocok</h3>
                    <p class="text-sm text-slate-500 mb-4">Coba ubah kata pencarian atau pilih status lain.</p>
                    <a href="{{ route('transaksis.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition-all">
                        Hapus Filter
                    </a>
                @else
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Belum ada transaksi</h3>
                    <p class="text-sm text-slate-500 mb-4">Pesanan dari POS dan web kafe akan muncul di sini.</p>
                    @if (in_array($role, ['kasir', 'admin'], true))
                        <a href="{{ route('transaksis.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20">
                            Buat Pesanan Pertama
                        </a>
                    @endif
                @endif
            </div>
        @endif
    </main>
</div>

<script>
    document.querySelector('form')?.addEventListener('submit', function () {
        const submit = this.querySelector('button[type="submit"]');
        if(submit) {
            submit.disabled = true;
            submit.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memuat...`;
        }
    });
</script>
@endsection