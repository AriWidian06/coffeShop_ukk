@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50">

    <!-- TOP HEADER -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Breadcrumb & Title -->
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">Operasional / <span class="text-primary-600">Dashboard</span></p>
                    <h1 class="text-2xl font-bold text-slate-900">Ringkasan Operasional Hari Ini</h1>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    <div class="hidden md:flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-lg border border-slate-200">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-sm font-semibold text-slate-700">{{ now()->format('d M Y') }}</span>
                    </div>
                    <a href="{{ route('laporan.export') }}" class="px-4 py-2 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export Laporan
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN DASHBOARD CONTENT -->
    <main class="p-6 space-y-6">

        <!-- TOP SECTION: Stats & Table Map -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Stats Cards (Left/Center) -->
            <div class="xl:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Total Penjualan -->
                <div class="stat-card-blue rounded-2xl p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-primary-100 text-xs font-bold uppercase tracking-wider mb-1">Total Penjualan Hari Ini</p>
                            <h3 class="text-3xl font-bold">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="bg-white/20 px-2 py-1 rounded-lg font-semibold">Data Real-time</span>
                        <span class="text-primary-100">Hari ini</span>
                    </div>
                </div>

                <!-- Total Pesanan -->
                <div class="stat-card rounded-2xl p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">Total Pesanan Selesai</p>
                            <h3 class="text-3xl font-bold text-slate-900">{{ $totalPesanan }} <span class="text-sm font-normal text-slate-500">Tiket</span></h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-slate-600">Pesanan Selesai Hari Ini</span>
                    </div>
                </div>

                <!-- Status Meja -->
                <div class="stat-card rounded-2xl p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">Status Meja Terisi</p>
                            <h3 class="text-3xl font-bold text-slate-900">{{ $mejaTerisi }} <span class="text-sm font-normal text-slate-500">/ {{ $totalMeja }} Meja</span></h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Tingkat Okupansi</span>
                            <span class="font-bold text-slate-900">{{ $okupansi }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $okupansi }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Peringatan Inventori -->
                <div class="stat-card border-red-200 rounded-2xl p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-red-600 text-xs font-bold uppercase tracking-wider mb-1">Peringatan Inventori</p>
                            <h3 class="text-3xl font-bold text-red-600">{{ $stokHabis }} <span class="text-sm font-normal text-slate-500">Bahan Baku</span></h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-lg text-xs font-bold">Perlu Reorder</span>
                        <a href="{{ route('suppliers.index') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700">Detail Stok →</a>
                    </div>
                </div>
            </div>

            <!-- Live Table Map (Admin) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-bold text-slate-900 text-lg">Pantauan Meja Live</h3>
                        <p class="text-xs text-slate-500">Status okupansi meja saat ini</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <span class="text-slate-600">Tersedia</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            <span class="text-slate-600">Terisi</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                    @php
                        $adminMejaStatus = \App\Models\Transaksi::whereIn('status_pesanan', ['pending', 'ready', 'prosesed'])
                                            ->whereNotNull('meja_id')
                                            ->pluck('meja_id')
                                            ->toArray();
                        $allMejas = \App\Models\Meja::where('status_aktif', true)->orderBy('nomor_meja')->get();
                    @endphp
                    @foreach($allMejas as $meja)
                        @php
                            $occupied = in_array($meja->id, $adminMejaStatus);
                            $mejaLink = $occupied
                                ? route('transaksis.show', \App\Models\Transaksi::where('meja_id', $meja->id)
                                    ->whereIn('status_pesanan', ['pending', 'ready', 'prosesed'])
                                    ->latest('waktu_transaksi')
                                    ->first()?->id ?? 1)
                                : route('transaksis.create', ['meja_id' => $meja->id]);
                        @endphp
                        <a href="{{ $mejaLink }}" class="flex flex-col items-center justify-center group relative p-3 rounded-xl border-2 text-center transition-all cursor-pointer hover:scale-105 active:scale-95 {{ $occupied ? 'border-amber-200 bg-amber-50 hover:border-amber-400' : 'border-slate-100 bg-white hover:border-primary-300 hover:shadow-sm' }}">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase leading-tight">Meja {{ $meja->nomor_meja }}</span>
                            <span class="text-[10px] font-black {{ $occupied ? 'text-amber-600' : 'text-emerald-600' }} uppercase tracking-tighter">
                                {{ $occupied ? 'TERISI' : 'KOSONG' }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- SECOND SECTION: Analytics & Table -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Penjualan per Jam -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 lg:col-span-2">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-bold text-slate-900 text-lg">Penjualan Real-time per Jam</h3>
                        <p class="text-xs text-slate-500">Distribusi transaksi aktif berdasarkan periode shift</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="px-3 py-1.5 bg-primary-50 text-primary-700 text-xs font-bold rounded-lg">Nilai (Rp)</button>
                        <button class="px-3 py-1.5 text-slate-600 text-xs font-bold rounded-lg hover:bg-slate-50">Volume</button>
                    </div>
                </div>

                <!-- Simple Bar Chart Representation -->
                <div class="flex items-end justify-between gap-4 h-48 px-4">
                    @php
                        $maxSales = $salesByHour->max() ?? 1;
                    @endphp
                    @for ($hour = 0; $hour < 24; $hour++)
                        @php
                            $value = $salesByHour->get($hour, 0);
                            $height = ($value / $maxSales) * 100;
                            $isPeak = ($hour == $peakHour);
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-2 group relative">
                            <div class="w-full {{ $isPeak ? 'bg-primary-600' : 'bg-primary-200' }} rounded-t-sm transition-all hover:bg-primary-400" style="height: {{ $height }}%"></div>
                            <span class="text-[10px] text-slate-400 font-semibold">{{ sprintf('%02d', $hour) }}</span>
                            @if($isPeak)
                                <span class="absolute -top-6 text-[10px] font-bold text-primary-600">Peak</span>
                            @endif
                        </div>
                    @endfor
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-4 border-t border-slate-200">
                    <div>
                        <p class="text-xs text-slate-500">Puncak Transaksi</p>
                        <p class="text-sm font-bold text-slate-900">{{ $peakHour !== null ? $peakHour . ':00 WIB' : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Avg Ticket Size</p>
                        <p class="text-sm font-bold text-slate-900">Rp {{ number_format($totalPenjualan > 0 ? $totalPenjualan / ($totalPesanan ?: 1) : 0, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Tipe Order</p>
                        <p class="text-sm font-bold text-slate-900">Real-time Data</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Metode Terbanyak</p>
                        <p class="text-sm font-bold text-slate-900">Real-time Data</p>
                    </div>
                </div>
            </div>

            <!-- Kategori Terlaris -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-900 text-lg mb-1">Kategori Terlaris</h3>
                <p class="text-xs text-slate-500 mb-6">Porsi kontribusi omzet shift</p>

                <!-- Donut Chart Representation -->
                <div class="flex items-center justify-center mb-6">
                    <div class="relative w-32 h-32">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-blue-100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="4"/>
                            @php
                                $totalRevenue = $topCategories->sum('total_revenue');
                                $firstCatRevenue = $topCategories->first() ? $topCategories->first()->total_revenue : 0;
                                $percentage = $totalRevenue > 0 ? ($firstCatRevenue / $totalRevenue) * 100 : 0;
                            @endphp
                            <path class="text-primary-600" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="4" stroke-dasharray="{{ $percentage }}, 100"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <span class="block text-2xl font-bold text-slate-900">{{ $topCategories->sum('total_qty') }}</span>
                                <span class="text-[10px] text-slate-500 uppercase">Porsi Item</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach($topCategories as $index => $category)
                        @php
                            $catPercentage = $totalRevenue > 0 ? ($category->total_revenue / $totalRevenue) * 100 : 0;
                            $colors = ['bg-primary-600', 'bg-primary-400', 'bg-primary-300', 'bg-slate-300'];
                            $color = $colors[$index] ?? 'bg-slate-300';
                        @endphp
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $color }}"></span>
                                <span class="text-slate-700">{{ $category->nama_kategori }}</span>
                            </div>
                            <div class="text-right">
                                <span class="block font-bold text-slate-900">Rp {{ number_format($category->total_revenue, 0, ',', '.') }}</span>
                                <span class="text-xs font-bold text-primary-600">{{ round($catPercentage) }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- RECENT ORDERS TABLE -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Daftar Pesanan Terkini</h3>
                    <p class="text-xs text-slate-500">Aktivitas pesanan pelanggan masuk dan status dapur live</p>
                </div>
                <a href="{{ route('transaksis.index') }}" class="text-xs font-bold text-primary-600 hover:text-primary-700 bg-primary-50 px-3 py-1.5 rounded-lg">
                    Lihat Semua KDS
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">No. Meja</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tiket & Jam</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Daftar Pesanan</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Total Nilai</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Status Produksi</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($transaksiTerbaru as $transaksi)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg {{ $transaksi->meja ? 'bg-primary-50 text-primary-700' : 'bg-slate-100 text-slate-700' }} text-xs font-bold">
                                        {{ $transaksi->meja ? 'Meja ' . $transaksi->meja->nomor_meja : 'Take Away' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-900">#{{ $transaksi->id }}</div>
                                    <div class="text-xs text-slate-500">{{ $transaksi->waktu_transaksi?->format('H:i') }} WIB • {{ $transaksi->sumber_pesanan }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-slate-900">
                                        @foreach($transaksi->detail_transaksis->take(2) as $detail)
                                            {{ $detail->QTY }}x {{ $detail->produk->nama_produk }},
                                        @endforeach
                                        @if($transaksi->detail_transaksis->count() > 2)
                                            ...
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-slate-900">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'dot' => 'bg-amber-600', 'label' => 'Menunggu'],
                                            'ready' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'dot' => 'bg-blue-600', 'label' => 'Siap'],
                                            'prosesed' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'dot' => 'bg-purple-600', 'label' => 'Diproses'],
                                            'completed' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-600', 'label' => 'Selesai'],
                                        ];
                                        $status = $statusColors[$transaksi->status_pesanan] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'dot' => 'bg-slate-600', 'label' => 'Unknown'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $status['bg'] }} {{ $status['text'] }} text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $status['dot'] }} {{ $transaksi->status_pesanan !== 'completed' ? 'animate-pulse' : '' }}"></span>
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('transaksis.show', $transaksi->id) }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 bg-primary-50 hover:bg-primary-100 px-3 py-1.5 rounded-lg transition">
                                        Detail Tiket
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-500 text-sm">Belum ada pesanan hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- STAFF ON DUTY -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Karyawan Bertugas</h3>
                    <p class="text-xs text-slate-500">Metrik kinerja staf operasional hari ini</p>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600">{{ $karyawans->count() }} Staff Terdaftar</span>
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($karyawans as $karyawan)
                    <div class="p-4 rounded-xl border border-slate-200 hover:border-primary-300 hover:shadow-md transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($karyawan->nama_karyawan, 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ $karyawan->nama_karyawan }}</h4>
                                <p class="text-xs text-slate-500">{{ $karyawan->role }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-slate-600"><strong class="text-slate-900">{{ $karyawan->completed_orders }} Pesanan Selesai</strong></span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs mt-1">
                            <span class="text-slate-500">Omzet: <strong class="text-slate-900">Rp {{ number_format($karyawan->total_revenue, 0, ',', '.') }}</strong></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
</div>
@endsection