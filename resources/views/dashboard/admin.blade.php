@extends('layouts.app')

@section('content')
    <h1>Dashboard Admin</h1>
    <p>Kelola data umum, produk, meja, supplier, dan karyawan.</p>

    <div style="display:grid; gap:1rem; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-top: 1.5rem;">
        <a class="button" href="{{ route('karyawans.index') }}">Karyawan</a>
        <a class="button" href="{{ route('produks.index') }}">Produk</a>
        <a class="button" href="{{ route('mejas.index') }}">Meja</a>
        <a class="button" href="{{ route('suppliers.index') }}">Supplier</a>
        <a class="button" href="{{ route('kategori_produks.index') }}">Kategori Produk</a>

    </div>

    <div style="margin-top:2rem; background:#fff; padding:1rem; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
        <h2>Admin Panel</h2>
        <p>Admin memiliki akses penuh ke pengelolaan sistem.</p>
    </div>
@endsection
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
                        <h3 class="text-3xl font-bold">Rp {{ number_format($totalPenjualan ?? 14850000, 0, ',', '.') }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="bg-white/20 px-2 py-1 rounded-lg font-semibold">+12.4%</span>
                    <span class="text-primary-100">vs kemarin</span>
                </div>
            </div>

            <!-- Total Pesanan -->
            <div class="stat-card">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">Total Pesanan Selesai</p>
                        <h3 class="text-3xl font-bold text-slate-900">{{ $totalPesanan ?? 142 }} <span class="text-sm font-normal text-slate-500">Tiket</span></h3>
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
                    <span class="text-slate-600">Waktu Saji Rata-rata</span>
                    <span class="font-bold text-blue-600">8.5 Menit</span>
                </div>
            </div>

            <!-- Status Meja -->
            <div class="stat-card">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">Status Meja Terisi</p>
                        <h3 class="text-3xl font-bold text-slate-900">{{ $mejaTerisi ?? 18 }} <span class="text-sm font-normal text-slate-500">/ {{ $totalMeja ?? 24 }} Meja</span></h3>
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
                        <span class="font-bold text-slate-900">{{ $okupansi ?? 75 }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $okupansi ?? 75 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Peringatan Inventori -->
            <div class="stat-card border-red-200">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-red-600 text-xs font-bold uppercase tracking-wider mb-1">Peringatan Inventori</p>
                        <h3 class="text-3xl font-bold text-red-600">{{ $stokHabis ?? 3 }} <span class="text-sm font-normal text-slate-500">Bahan Baku</span></h3>
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
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-primary-200 rounded-t-xl" style="height: 40%"></div>
                        <span class="text-xs text-slate-600 font-semibold">Pagi<br>07-11</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-primary-600 rounded-t-xl" style="height: 100%"></div>
                        <span class="text-xs text-primary-700 font-bold">Siang (Peak)<br>11-15</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-primary-400 rounded-t-xl" style="height: 75%"></div>
                        <span class="text-xs text-slate-600 font-semibold">Sore<br>15-18</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-primary-100 rounded-t-xl" style="height: 25%"></div>
                        <span class="text-xs text-slate-600 font-semibold">Malam<br>18-22</span>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4 mt-6 pt-4 border-t border-slate-200">
                    <div>
                        <p class="text-xs text-slate-500">Puncak Transaksi</p>
                        <p class="text-sm font-bold text-slate-900">12:30 - 13:30 WIB</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Avg Ticket Size</p>
                        <p class="text-sm font-bold text-slate-900">Rp 104.500</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Tipe Order</p>
                        <p class="text-sm font-bold text-slate-900">68% Dine-in</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Metode Terbanyak</p>
                        <p class="text-sm font-bold text-slate-900">QRIS (74%)</p>
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
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-primary-600"></span>
                            <span class="text-slate-700">Signature Espresso</span>
                        </div>
                        <div class="text-right">
                            <span class="block font-bold text-slate-900">Rp 6.682K</span>
                            <span class="text-xs font-bold text-primary-600">45%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-primary-400"></span>
                            <span class="text-slate-700">Manual Brew</span>
                        </div>
                        <div class="text-right">
                            <span class="block font-bold text-slate-900">Rp 3.712K</span>
                            <span class="text-xs font-bold text-primary-600">25%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-primary-300"></span>
                            <span class="text-slate-700">Pastry</span>
                        </div>
                        <div class="text-right">
                            <span class="block font-bold text-slate-900">Rp 2.970K</span>
                            <span class="text-xs font-bold text-primary-600">20%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                            <span class="text-slate-700">Whole Beans</span>
                        </div>
                        <div class="text-right">
                            <span class="block font-bold text-slate-900">Rp 1.485K</span>
                            <span class="text-xs font-bold text-primary-600">10%</span>
                        </div>
                    </div>
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
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-primary-50 text-primary-700 text-xs font-bold">Meja 04</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-900">#ORD-9021</div>
                                <div class="text-xs text-slate-500">14:28 WIB • QR Order</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-900">2x Kopi Susu Aren Gula Melaka, 1x Butter Croissant</div>
                                <div class="text-xs text-slate-500 mt-1">Catatan: Less ice, oat milk substitution</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-900">Rp 118.000</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                    Sedang Diracik
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-xs font-semibold text-primary-600 hover:text-primary-700 bg-primary-50 hover:bg-primary-100 px-3 py-1.5 rounded-lg transition">
                                    Detail Tiket
                                </button>
                            </td>
                        </tr>

                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold">Meja 11</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-900">#ORD-9020</div>
                                <div class="text-xs text-slate-500">14:22 WIB • Kasir POS</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-900">1x V60 Gayo Anaerob Natural, 1x Cinnamon Roll</div>
                                <div class="text-xs text-slate-500 mt-1">Roaster: Batch #GAYO-102</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-900">Rp 82.000</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Selesai
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-xs font-semibold text-primary-600 hover:text-primary-700 bg-primary-50 hover:bg-primary-100 px-3 py-1.5 rounded-lg transition">
                                    Detail Tiket
                                </button>
                            </td>
                        </tr>

                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-primary-50 text-primary-700 text-xs font-bold">Meja 02</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-900">#ORD-9019</div>
                                <div class="text-xs text-slate-500">14:15 WIB • QR Order</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-900">1x Flat White, 1x Magic Double Ristretto</div>
                                <div class="text-xs text-slate-500 mt-1">Dine-in Bar Counter</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-900">Rp 76.000</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                    Sedang Diracik
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-xs font-semibold text-primary-600 hover:text-primary-700 bg-primary-50 hover:bg-primary-100 px-3 py-1.5 rounded-lg transition">
                                    Detail Tiket
                                </button>
                            </td>
                        </tr>
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
                <div class="p-4 rounded-xl border border-slate-200 hover:border-primary-300 hover:shadow-md transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">DS</div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Dimas Satria</h4>
                            <p class="text-xs text-slate-500">Head Barista • Espresso</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600"><strong>54 Pesanan Selesai</strong></span>
                    </div>
                </div>

                <div class="p-4 rounded-xl border border-slate-200 hover:border-primary-300 hover:shadow-md transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm">RA</div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Rania Amalia</h4>
                            <p class="text-xs text-slate-500">Barista Slow Bar • Manual</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600"><strong>32 Pour-over Selesai</strong></span>
                    </div>
                </div>

                <div class="p-4 rounded-xl border border-slate-200 hover:border-primary-300 hover:shadow-md transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold text-sm">FW</div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Fajar Wicaksono</h4>
                            <p class="text-xs text-slate-500">Kasir POS & Order Taker</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600"><strong>Rp 8.920K Diterima</strong></span>
                    </div>
                </div>

                <div class="p-4 rounded-xl border border-slate-200 hover:border-primary-300 hover:shadow-md transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">KL</div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Kevin Lestari</h4>
                            <p class="text-xs text-slate-500">Floor Runner & Table Bus</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600"><strong>Turnover: 12 Menit</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
