@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-slate-50">
    
    <!-- TOP HEADER -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs text-slate-500 font-semibold">Cabang Senopati Flagship</span>
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full">Live POS Sync</span>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900">Ringkasan Penjualan</h1>
                    <p class="text-sm text-slate-500 mt-1">Shift Pagi Aktif (07:00 - 15:30 WIB) • Lead Barista: Dimas & 4 Kru</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-slate-50 rounded-xl border border-slate-200">
                        <p class="text-xs text-slate-500 font-semibold">Bulan Ini (1 - {{ now()->format('d') }} Okt 2024)</p>
                    </div>
                    <button class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Snapshot Real-time -->
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-5 border border-blue-200 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-600 font-semibold mb-1">Snapshot Real-time Hari Ini</p>
                        <p class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalPendapatanHariIni ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-500">dari {{ $totalTransaksiHariIni ?? 0 }} transaksi struk tercatat</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-xs text-slate-600 font-semibold">Kecepatan Bar</p>
                        <p class="text-sm font-bold text-slate-900">2.4 mnt/cup</p>
                    </div>
                    <div class="px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">
                        Shift Pagi Berjalan Normal
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="p-6 space-y-6">
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Pendapatan -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Total Pendapatan Kotor</p>
                        <h3 class="text-3xl font-bold text-slate-900">Rp {{ number_format($totalPendapatanBulanIni ?? 0, 0, ',', '.') }}</h3>
                        <p class="text-xs text-slate-500 mt-1">vs Rp {{ number_format(($totalPendapatanBulanIni ?? 0) * 0.85, 0, ',', '.') }} periode lalu</p>
                    </div>
                    <div class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">
                        +14.8%
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs mb-2">
                        <span class="text-slate-600">Target Bulanan (Cap: Rp 300 Jt)</span>
                        <span class="font-bold text-primary-600">94.8%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="bg-primary-600 h-2 rounded-full" style="width: 94.8%"></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Tercapai 24 dari 31 hari • Sisa Rp {{ number_format(300000000 - ($totalPendapatanBulanIni ?? 0), 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Jumlah Transaksi -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Jumlah Transaksi</p>
                        <h3 class="text-3xl font-bold text-slate-900">{{ $totalTransaksiBulanIni ?? 0 }}</h3>
                        <p class="text-xs text-slate-500 mt-1">Total tiket POS yang terselesaikan</p>
                    </div>
                    <div class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">
                        +8.2% YoY
                    </div>
                </div>
                
                <div class="flex items-center gap-4 mt-4 pt-4 border-t border-slate-100">
                    <div class="flex-1">
                        <p class="text-xs text-slate-500">Rata-rata Harian</p>
                        <p class="text-sm font-bold text-slate-900">174 transaksi/hari</p>
                    </div>
                    <div class="flex-1 text-right">
                        <p class="text-xs text-slate-500">Peak Jam</p>
                        <p class="text-sm font-bold text-primary-600">12:00 - 14:00</p>
                    </div>
                </div>
            </div>

            <!-- Nilai Rata-rata Transaksi -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Nilai Rata-rata Transaksi (ATV)</p>
                        <h3 class="text-3xl font-bold text-slate-900">Rp {{ number_format($averageTransactionValue ?? 0, 0, ',', '.') }}</h3>
                        <p class="text-xs text-slate-500 mt-1">/ Struk</p>
                    </div>
                    <div class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">
                        +6.1%
                    </div>
                </div>
                
                <p class="text-xs text-slate-500 mt-4">Peningkatan basket size via bundling pastry</p>
                
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span class="text-slate-600">Attach Rate Pastry</span>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-900">34.2% dari struk kopi</p>
                        <p class="text-xs font-bold text-emerald-600">+4.5% MoM</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Kurva Penjualan -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-2 h-2 rounded-full bg-primary-600"></div>
                    <h3 class="font-bold text-slate-900">Kurva Penjualan & Volume Jam Operasional</h3>
                </div>
                <p class="text-xs text-slate-500 mb-6">Akumulasi ritme omzet 7 hari terakhir (Jumat - Kamis)</p>
                
                <!-- Simple Bar Chart Representation -->
                <div class="flex items-end justify-between gap-2 h-48 px-4">
                    @php
                        $transaksiPerJam = $transaksiPerJam ?? collect();
                        $hours = [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];
                        $maxTransaksi = $transaksiPerJam->max('jumlah') ?: 1;
                    @endphp
                    @foreach($hours as $hour)
                        @php
                            $data = $transaksiPerJam->firstWhere('jam', $hour);
                            $jumlah = $data ? $data->jumlah : 0;
                            $height = ($jumlah / $maxTransaksi) * 100;
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full bg-primary-100 rounded-t-lg relative" style="height: {{ max($height, 5) }}%">
                                <div class="absolute bottom-0 left-0 right-0 bg-primary-600 rounded-t-lg" style="height: 60%"></div>
                            </div>
                            <span class="text-[10px] text-slate-600 font-semibold">{{ $hour }}:00</span>
                        </div>
                    @endforeach
                </div>
                
                <div class="flex items-center justify-center gap-6 mt-4 pt-4 border-t border-slate-200">
                    <div class="flex items-center gap-2 text-xs">
                        <div class="w-3 h-3 rounded bg-primary-600"></div>
                        <span class="text-slate-600">Minuman Espresso</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <div class="w-3 h-3 rounded bg-blue-300"></div>
                        <span class="text-slate-600">Pastry & Roastery Beans</span>
                    </div>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-slate-900">Metode Pembayaran</h3>
                    <button class="text-xs text-primary-600 font-semibold hover:text-primary-700">Lihat Detail</button>
                </div>
                <p class="text-xs text-slate-500 mb-6">Distribusi penyelesaian tagihan di kasir POS</p>
                
                <!-- Payment Methods -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">QRIS</p>
                                <p class="text-xs text-slate-500">Termasuk GoPay, OVO, Dana</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-slate-900">88%</p>
                            <p class="text-xs text-emerald-600 font-semibold">+12% dari bulan lalu</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v2a2 2 0 002 2h6a2 2 0 002-2v-1" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Tunai</p>
                                <p class="text-xs text-slate-500">Cash payment</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-slate-900">12%</p>
                            <p class="text-xs text-slate-500">-12% dari bulan lalu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produk Terlaris -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <h3 class="font-bold text-slate-900">Top 5 Produk Terlaris Hari Ini</h3>
                </div>
                <a href="#" class="text-xs text-primary-600 font-semibold hover:text-primary-700">Lihat Semua Produk →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase">Produk</th>
                            <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase">Kategori</th>
                            <th class="text-right py-3 px-4 text-xs font-bold text-slate-500 uppercase">Terjual</th>
                            <th class="text-right py-3 px-4 text-xs font-bold text-slate-500 uppercase">Pendapatan</th>
                            <th class="text-center py-3 px-4 text-xs font-bold text-slate-500 uppercase">Tren</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $produkTerlaris = $produkTerlaris ?? collect();
                        @endphp
                        @forelse($produkTerlaris as $item)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm">{{ $item->produk->nama_produk ?? 'Produk dihapus' }}</p>
                                        <p class="text-xs text-slate-500">SKU: {{ $item->produk->sku ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-primary-50 text-primary-700 text-xs font-bold">
                                    {{ $item->produk->kategoriProduk->nama_kategori ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <span class="font-bold text-slate-900">{{ $item->total_qty }} pcs</span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <span class="font-bold text-slate-900">Rp {{ number_format($item->total_qty * ($item->produk->harga ?? 0), 0, ',', '.') }}</span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                    </svg>
                                    +15%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500 text-sm">
                                Belum ada data penjualan hari ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection