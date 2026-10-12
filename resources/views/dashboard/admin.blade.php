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
                    <button class="px-4 py-2 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export Laporan
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN DASHBOARD CONTENT -->
    <main class="p-6 space-y-6">
        
        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
                    <span class="bg-white/20 px-2 py-1 rounded-lg font-semibold">Hari Ini</span>
                    <span class="text-primary-100">Real-time</span>
                </div>
            </div>

            <!-- Total Pesanan -->
            <div class="stat-card">
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
                    <span class="text-slate-600">Total Transaksi</span>
                    <span class="font-bold text-blue-600">{{ $totalPesanan }} Tiket</span>
                </div>
            </div>

            <!-- Status Meja -->
            <div class="stat-card">
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
            <div class="stat-card border-red-200">
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

        <!-- CHARTS & ANALYTICS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Penjualan per Jam -->
            <div class="chart-container lg:col-span-2">
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
                        $hours = [
                            ['label' => 'Pagi', 'time' => '07-11', 'hours' => range(7, 11)],
                            ['label' => 'Siang (Peak)', 'time' => '11-15', 'hours' => range(11, 15)],
                            ['label' => 'Sore', 'time' => '15-18', 'hours' => range(15, 18)],
                            ['label' => 'Malam', 'time' => '18-22', 'hours' => range(18, 22)],
                        ];
                        $maxSales = max(0, ...$salesByHour->values()->toArray());
                    @endphp
                    @foreach($hours as $period)
                        @php
                            $periodTotal = 0;
                            foreach($period['hours'] as $h) {
                                $periodTotal += $salesByHour[$h] ?? 0;
                            }
                            $height = $maxSales > 0 ? ($periodTotal / $maxSales) * 100 : 0;
                            $isPeak = $period['label'] === 'Siang (Peak)';
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full {{ $isPeak ? 'bg-primary-600' : 'bg-primary-200' }} rounded-t-xl transition-all duration-500" style="height: {{ $height }}%"></div>
                            <span class="text-xs {{ $isPeak ? 'text-primary-700 font-bold' : 'text-slate-600 font-semibold' }}">
                                {{ $period['label'] }}<br>{{ $period['time'] }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-4 gap-4 mt-6 pt-4 border-t border-slate-200">
                    <div>
                        <p class="text-xs text-slate-500">Total Omzet</p>
                        <p class="text-sm font-bold text-slate-900">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Avg Ticket Size</p>
                        <p class="text-sm font-bold text-slate-900">
                            Rp {{ $totalPesanan > 0 ? number_format($totalPenjualan / $totalPesanan, 0, ',', '.') : 0 }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Tipe Order</p>
                        <p class="text-sm font-bold text-slate-900">
                            {{ $transaksiTerbaru->count() > 0 ? round(($transaksiTerbaru->whereNotNull('meja_id')->count() / $transaksiTerbaru->count()) * 100) : 0 }}% Dine-in
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Total Pesanan</p>
                        <p class="text-sm font-bold text-slate-900">{{ $totalPesanan }} Tiket</p>
                    </div>
                </div>
            </div>

            <!-- Kategori Terlaris -->
            <div class="chart-container">
                <h3 class="font-bold text-slate-900 text-lg mb-1">Kategori Terlaris</h3>
                <p class="text-xs text-slate-500 mb-6">Porsi kontribusi omzet shift</p>
                
                <!-- Donut Chart Representation -->
                <div class="flex items-center justify-center mb-6">
                    <div class="relative w-32 h-32">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-blue-100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="4"/>
                            <path class="text-primary-600" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="4" stroke-dasharray="45, 100"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <span class="block text-2xl font-bold text-slate-900">384</span>
                                <span class="text-[10px] text-slate-500 uppercase">Porsi Item</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($topCategories as $category)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $loop->first ? 'bg-primary-600' : ($loop->iteration == 2 ? 'bg-primary-400' : ($loop->iteration == 3 ? 'bg-primary-300' : 'bg-slate-300')) }}"></span>
                            <span class="text-slate-700">{{ $category->nama_kategori }}</span>
                        </div>
                        <div class="text-right">
                            <span class="block font-bold text-slate-900">Rp {{ number_format($category->total_revenue, 0, ',', '.') }}</span>
                            <span class="text-xs font-bold text-primary-600">
                                {{ $topCategories->sum('total_qty') > 0 ? round(($category->total_qty / $topCategories->sum('total_qty')) * 100) : 0 }}%
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-xs text-slate-500 py-4">
                        Belum ada data kategori terlaris.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RECENT ORDERS TABLE -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Daftar Pesanan Terkini</h3>
                    <p class="text-xs text-slate-500">Aktivitas pesanan pelanggan masuk dan status dapur live</p>
                </div>
                <a href="{{ route('transaksis.index') }}" class="text-center text-xs font-bold text-primary-600 hover:text-primary-700 bg-primary-50 px-3 py-1.5 rounded-lg">
                    Lihat Semua KDS
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]">
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
                                    {{ $transaksi->meja ? 'Meja ' . $transaksi->meja->nomor_meja : 'Take away' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-900">#ORD-{{ $transaksi->id }}</div>
                                <div class="text-xs text-slate-500">{{ $transaksi->waktu_transaksi->format('H:i') }} WIB • {{ $transaksi->sumber_pesanan }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-900">
                                    @foreach($transaksi->detail_transaksis as $detail)
                                        {{ $detail->QTY }}x {{ $detail->produk->nama_produk }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </div>
                                @if($transaksi->catatan)
                                    <div class="text-xs text-slate-500 mt-1">Catatan: {{ $transaksi->catatan }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-900">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($transaksi->status_pesanan === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                        Sedang Diracik
                                    </span>
                                @elseif($transaksi->status_pesanan === 'selesai')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-50 text-slate-700 text-xs font-bold">
                                        {{ ucfirst($transaksi->status_pesanan) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('transaksis.show', $transaksi->id) }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 bg-primary-50 hover:bg-primary-100 px-3 py-1.5 rounded-lg transition">
                                    Detail Tiket
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-500 text-sm">
                                Belum ada pesanan masuk hari ini.
                            </td>
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
                    <h3 class="font-bold text-slate-900 text-lg">Karyawan Bertugas (Shift Siang)</h3>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600">4 Aktif di Bar & Lantai</span>
                    </span>
                    <span class="text-slate-500">Supervisor: <strong>Budi Santoso</strong></span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($karyawans as $karyawan)
                <div class="p-4 rounded-xl border border-slate-200 hover:border-primary-300 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($karyawan->nama, 0, 2)) }}
                            </div>
                            <div class="text-left">
                                <h4 class="font-bold text-slate-900 text-sm">{{ $karyawan->nama }}</h4>
                                <p class="text-xs text-slate-500">{{ $karyawan->jabatan ?? 'Karyawan' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-slate-600">Aktif</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh halaman setiap 30 detik agar data tetap sinkron dengan database
    setInterval(function() {
        window.location.reload();
    }, 30000);
</script>
@endpush