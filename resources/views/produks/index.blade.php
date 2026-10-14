@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50">
    
    <!-- TOP HEADER -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between mb-4">
                <!-- Breadcrumb & Title -->
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">
                        Katalog Utama / <span class="text-primary-600">Outlet Bagi Kopi</span>
                    </p>
                    <h1 class="text-2xl font-bold text-slate-900">Manajemen Katalog Produk & Menu</h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Kelola seluruh sajian minuman, makanan, beans, dan bahan baku produksi dengan sinkronisasi langsung ke sistem kasir dan QR table ordering.
                    </p>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    <button class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-semibold text-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Ekspor CSV
                    </button>
                    <a href="{{ route('produks.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Produk Baru
                    </a>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total SKU Aktif -->
                <div class="stat-card-product">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Total SKU Aktif</p>
                            <h3 class="text-3xl font-bold text-slate-900">{{ $produks->total() }}</h3>
                            <p class="text-xs text-emerald-600 mt-1 font-semibold">+4 bulan ini</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Menu Siap Jual -->
                <div class="stat-card-product">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Menu Siap Jual</p>
                            <h3 class="text-3xl font-bold text-slate-900">{{ $menuSiapJual ?? 36 }}</h3>
                            <p class="text-xs text-slate-500 mt-1">75% dari portofolio</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Peringatan Stok Tipis -->
                <div class="stat-card-product border-amber-200">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-amber-600 font-bold uppercase tracking-wider mb-1">Peringatan Stok Tipis</p>
                            <h3 class="text-3xl font-bold text-amber-600">{{ $stokTipis ?? 3 }}</h3>
                            <p class="text-xs text-amber-700 mt-1 font-semibold">Perlu restock segera</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Stok Habis -->
                <div class="stat-card-product border-red-200">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-red-600 font-bold uppercase tracking-wider mb-1">Stok Habis (Sold Out)</p>
                            <h3 class="text-3xl font-bold text-red-600">{{ $stokHabis ?? 2 }}</h3>
                            <p class="text-xs text-red-700 mt-1 font-semibold">Otomatis hidden di POS</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="p-6">
        
        <!-- Search & Filters -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">
            <form method="get" class="flex flex-col lg:flex-row gap-4">
                <!-- Search Input -->
                <div class="flex-1 relative">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari menu, SKU, atau kategori..." 
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
                </div>

                <!-- Category Filter -->
                <select name="kategori_id" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoriProduks as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                <!-- Type Filter Pills -->
                <div class="flex gap-2">
                    <button type="submit" name="tipe" value="" class="filter-pill {{ !request('tipe') ? 'active' : 'inactive' }}">
                        Semua
                    </button>
                    <button type="submit" name="tipe" value="jual" class="filter-pill {{ request('tipe') == 'jual' ? 'active' : 'inactive' }}">
                        Siap Jual
                    </button>
                    <button type="submit" name="tipe" value="bahan baku" class="filter-pill {{ request('tipe') == 'bahan baku' ? 'active' : 'inactive' }}">
                        Bahan Baku
                    </button>
                </div>

                <!-- Stock Status Filter -->
                <select name="stock_status" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Status Stok</option>
                    <option value="tersedia" {{ request('stock_status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="menipis" {{ request('stock_status') == 'menipis' ? 'selected' : '' }}>Menipis</option>
                    <option value="habis" {{ request('stock_status') == 'habis' ? 'selected' : '' }}>Habis</option>
                </select>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th class="w-20">Foto</th>
                            <th>Nama Produk & SKU</th>
                            <th>Kategori</th>
                            <th>Tipe Produk</th>
                            <th>Harga Jual / HPP</th>
                            <th>Status Stok</th>
                            <th class="text-center">QR Menu</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produks as $produk)
                            @php
                                $stockStatus = $produk->stock == 0 ? 'habis' : ($produk->stock < 10 ? 'menipis' : 'tersedia');
                                $stockLabel = $stockStatus == 'habis' ? 'Habis' : ($stockStatus == 'menipis' ? 'Menipis' : 'Tersedia');
                                $stockColor = $stockStatus == 'habis' ? 'red' : ($stockStatus == 'menipis' ? 'amber' : 'emerald');
                                $margin = $produk->harga_beli > 0 ? round((($produk->harga_jual - $produk->harga_beli) / $produk->harga_jual) * 100) : 0;
                            @endphp
                            <tr>
                                <!-- Foto -->
                                <td>
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden">
                                        @if($produk->gambar)
                                            <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama_produk }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Nama Produk & SKU -->
                                <td>
                                    <div class="font-bold text-slate-900 text-sm">{{ $produk->nama_produk }}</div>
                                    <div class="text-xs text-slate-500 mt-1">SKU: {{ strtoupper(substr($produk->nama_produk, 0, 3)) }}-{{ $produk->id }}</div>
                                </td>

                                <!-- Kategori -->
                                <td>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-primary-50 text-primary-700 text-xs font-bold">
                                        {{ $produk->kategoriProduk->nama_kategori ?? '-' }}
                                    </span>
                                </td>

                                <!-- Tipe Produk -->
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ $produk->tipe == 'jual' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span class="text-sm font-semibold text-slate-700 capitalize">{{ $produk->tipe }}</span>
                                    </div>
                                </td>

                                <!-- Harga Jual / HPP -->
                                <td>
                                    <div class="text-sm font-bold text-slate-900">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</div>
                                    <div class="text-xs text-slate-500">HPP: Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</div>
                                    <div class="text-xs font-bold text-emerald-600 mt-1">{{ $margin }}% margin</div>
                                </td>

                                <!-- Status Stok -->
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-{{ $stockColor }}-50 text-{{ $stockColor }}-700 text-xs font-bold">
                                            {{ $stockLabel }}
                                        </span>
                                        <span class="text-xs text-slate-500">({{ $produk->stock }} {{ $produk->satuan }})</span>
                                    </div>
                                </td>

                                <!-- QR Menu Toggle -->
                                <td class="text-center">
                                    <div class="toggle-switch {{ $produk->status_aktif ? 'enabled' : 'disabled' }} mx-auto"></div>
                                </td>

                                <!-- Aksi -->
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('produks.edit', $produk) }}" class="p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form method="post" action="{{ route('produks.destroy', $produk) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-16">
                                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <p class="font-bold text-slate-900 mb-1">Belum ada produk</p>
                                    <p class="text-sm text-slate-500">Mulai tambahkan produk pertama Anda</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($produks->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                    <p class="text-sm text-slate-600">
                        Menampilkan <span class="font-bold">{{ $produks->firstItem() }}</span> - <span class="font-bold">{{ $produks->lastItem() }}</span> dari <span class="font-bold">{{ $produks->total() }}</span> produk
                    </p>
                    {{ $produks->withQueryString()->links() }}
                </div>
            @endif
        </div>

        <!-- Bottom Banner -->
        <div class="mt-6 bg-gradient-to-r from-primary-50 to-blue-50 border border-primary-200 rounded-2xl p-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Butuh pembaruan batch stok harian?</h3>
                    <p class="text-sm text-slate-600">Lakukan stock opname cepat untuk produk pastry & biji sangrai tanpa perlu mengedit item satu per satu.</p>
                </div>
            </div>
            <button class="px-5 py-2.5 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20">
                Buka Mode Stock Opname
            </button>
        </div>
    </main>
</div>
@endsection