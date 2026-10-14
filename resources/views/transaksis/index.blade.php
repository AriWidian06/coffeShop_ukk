@php
    $role = auth('karyawan')->user()->role ?? 'kasir';
    $activeLayout = match($role) {
        'kasir' => 'layouts.kasir',
        'manager' => 'layouts.manager',
        default => 'layouts.app',
    };

    $statusLabels = [
        'pending' => 'Menunggu',
        'ready' => 'Siap',
        'prosesed' => 'Diproses',
        'completed' => 'Selesai',
    ];
@endphp

@extends($activeLayout)

@section('content')
<div class="min-h-screen bg-slate-50 p-6">
    
    <!-- TOP HEADER -->
    <header class="bg-white border border-slate-200 rounded-2xl p-6 mb-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">
                    Operasional / <span class="text-primary-600">Laporan Transaksi</span>
                </p>
                <h1 class="text-2xl font-bold text-slate-900">Riwayat Pesanan</h1>
                <p class="text-sm text-slate-500 mt-1">Pantau semua transaksi dari POS kasir dan pemesanan web.</p>
            </div>
            @if (in_array($role, ['kasir', 'admin'], true))
                <a href="{{ route('transaksis.create') }}" class="px-4 py-2.5 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 flex items-center justify-center gap-2">
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
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Contoh: ID atau No. Meja" class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="w-full lg:w-48">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Pesanan</label>
                <select name="status" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua status</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 w-full lg:w-auto">
                <button type="submit" class="flex-1 lg:flex-none px-6 py-2.5 bg-primary-600 text-white rounded-lg font-semibold text-sm hover:bg-primary-700 transition-all flex items-center justify-center gap-2">
                    Tampilkan
                </button>
                @if (request()->filled('q') || request()->filled('status'))
                    <a href="{{ route('transaksis.index') }}" class="flex-1 lg:flex-none px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-lg font-semibold text-sm hover:bg-slate-50 transition-all flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </header>

    <!-- MAIN CONTENT -->
    <main>
        <div class="mb-4 flex items-center justify-between">
            <p class="text-sm text-slate-600">Menampilkan <span class="font-bold text-slate-900">{{ isset($transaksis) ? $transaksis->total() : 0 }}</span> transaksi</p>
        </div>

        @if (isset($transaksis) && $transaksis->count())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
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
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-4 px-4 align-top">
                                        <div class="font-bold text-slate-900 text-sm">#{{ $transaksi->id }}</div>
                                        <div class="text-xs text-slate-500 mt-1">{{ optional($transaksi->waktu_transaksi)->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="py-4 px-4 align-top">
                                        <div class="text-sm font-semibold text-slate-900">
                                            {{ $transaksi->tipe_pesanan === 'take-away' ? 'Bawa Pulang' : 'Makan di Tempat' }}
                                        </div>
                                        <div class="text-xs text-slate-500 mt-1">
                                            {{ $transaksi->meja ? 'Meja ' . $transaksi->meja->nomor_meja : 'Tanpa meja' }}
                                        </div>
                                        <div class="text-xs text-slate-600 mt-2 space-y-1">
                                            @if($transaksi->detail_transaksis)
                                                @foreach ($transaksi->detail_transaksis->take(2) as $detail)
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="w-1 h-1 rounded-full bg-slate-400"></span>
                                                        <span>{{ optional($detail->produk)->nama_produk ?? 'Produk dihapus' }} x{{ $detail->QTY ?? $detail->qty }}</span>
                                                        @if ($detail->customization)
                                                            <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 text-[10px] rounded border border-slate-200 italic">{{ $detail->customization }}</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                @if ($transaksi->detail_transaksis->count() > 2)
                                                    <div class="text-slate-500 italic">+ {{ $transaksi->detail_transaksis->count() - 2 }} produk lain</div>
                                                @endif
                                            @endif
                                        </div>
                                        @if ($transaksi->catatan)
                                            <div class="mt-2 text-xs text-amber-700 bg-amber-50 px-2 py-1 rounded border border-amber-200 inline-block">
                                                Catatan: {{ $transaksi->catatan }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 align-top">
                                        @if ($transaksi->sumber_pesanan === 'web')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-bold">
                                                Web Kafe
                                            </span>
                                        @else
                                            <div class="text-sm text-slate-700 font-medium">{{ optional($transaksi->karyawan)->nama_karyawan ?? '-' }}</div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 align-top text-right">
                                        <div class="font-bold text-slate-900 text-sm">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="py-4 px-4 align-top text-center">
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
                                    <td class="py-4 px-4 align-top text-center">
                                        @if ($transaksi->pembayaran)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Lunas
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                Belum Dibayar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 align-top text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if (!$transaksi->pembayaran)
                                                <a href="{{ route('kasir.payment.qris', $transaksi->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition shadow-sm">
                                                    Bayar QRIS
                                                </a>
                                            @endif
                                            <a href="{{ route('transaksis.show', $transaksi->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-50 text-primary-700 rounded-lg text-xs font-bold hover:bg-primary-100 transition">
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
                    <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                        <p class="text-sm text-slate-600">
                            Halaman {{ $transaksis->currentPage() }} dari {{ $transaksis->lastPage() }}
                        </p>
                        {{ $transaksis->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
                <h3 class="text-lg font-bold text-slate-900 mb-1">Belum ada transaksi</h3>
                <p class="text-sm text-slate-500 mb-4">Pesanan dari POS dan web kafe akan muncul di sini.</p>
            </div>
        @endif
    </main>
</div>
@endsection