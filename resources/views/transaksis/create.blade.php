@php
    // DETEKSI ROLE SECARA DINAMIS UNTUK MEMILIH LAYOUT
    $role = auth('karyawan')->user()->role;
    if ($role === 'kasir') {
        $activeLayout = 'layouts.kasir';
    } elseif ($role === 'manager') {
        $activeLayout = 'layouts.manager';
    } else {
        $activeLayout = 'layouts.app';
    }

    $kategoriProduks = \App\Models\KategoriProduk::orderBy('nama_kategori')->get();
@endphp

@extends($activeLayout)

@section('content')
<div class="min-h-screen bg-slate-50">
    
    <!-- TOP HEADER -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-2xl font-bold text-slate-900">Terminal Kasir</h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                            Station #01
                        </span>
                    </div>
                    <p class="text-sm text-slate-500">Pilih menu, atur jumlah, lalu simpan pesanan.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-3 py-2 bg-slate-50 rounded-lg border border-slate-200">
                        <p class="text-[10px] text-slate-500 font-semibold uppercase">Kasir</p>
                        <p class="text-sm font-bold text-slate-900">{{ auth('karyawan')->user()->nama_karyawan }}</p>
                    </div>
                    <div class="px-3 py-2 bg-slate-50 rounded-lg border border-slate-200">
                        <p class="text-[10px] text-slate-500 font-semibold uppercase">Waktu</p>
                        <p class="text-sm font-bold text-slate-900">{{ now()->format('H:i') }} WIB</p>
                    </div>
                    <a href="{{ route('transaksis.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-semibold text-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Riwayat Transaksi
                    </a>
                </div>
            </div>

            <!-- View Switcher (Katalog Menu vs Denah Meja) -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl">
                    <button type="button" id="tab-btn-menu" class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition-all bg-white text-slate-900 shadow-sm" onclick="switchPosView('menu')">
                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>Katalog Menu</span>
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] bg-primary-100 text-primary-700 font-bold">
                            {{ $produks->count() }}
                        </span>
                    </button>
                    <button type="button" id="tab-btn-denah" class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition-all text-slate-600 hover:text-slate-900" onclick="switchPosView('denah')">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Denah Meja</span>
                        @php
                            $totalMejaCount = $mejas->count();
                            $occupiedMejaCount = count($mejaStatus ?? []);
                        @endphp
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ $occupiedMejaCount > 0 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }} font-bold">
                            {{ $totalMejaCount - $occupiedMejaCount }}/{{ $totalMejaCount }} Kosong
                        </span>
                    </button>
                </div>

                <div id="denah-legend-bar" class="hidden items-center gap-3 text-xs font-semibold">
                    <span class="inline-flex items-center gap-1.5 text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Tersedia (Siap Pakai)
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Terisi (Ada Pesanan)
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Toast Notification -->
    <div id="pos-toast" class="fixed bottom-6 right-6 z-50 transform transition-all duration-300 translate-y-20 opacity-0 pointer-events-none">
        <div class="flex items-center gap-3 px-4 py-3 bg-slate-900 text-white text-sm rounded-xl shadow-xl border border-slate-700">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span id="pos-toast-msg">Notifikasi</span>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- SECTION KIRI: KATALOG MENU & DENAH MEJA (2/3 lebar) -->
            <section class="lg:col-span-2">

                <!-- 1. VIEW KATALOG MENU -->
                <div id="pos-view-menu" class="space-y-4">
                    <!-- Search & Category Filter -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input id="pos-search" type="search" placeholder="Cari nama menu atau kategori..." class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:bg-white transition-all">
                        </div>
                        <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar pt-1">
                            <button class="pos-category-tab active" data-category="all">
                                Semua Menu <span class="ml-1 bg-white/20 px-1.5 py-0.5 rounded-full text-[10px]">{{ $produks->count() }}</span>
                            </button>
                            @foreach($kategoriProduks as $kategori)
                                <button class="pos-category-tab inactive" data-category="{{ strtolower($kategori->nama_kategori) }}">
                                    {{ $kategori->nama_kategori }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @if ($produks->isEmpty())
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
                            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Belum ada menu aktif</h3>
                            <p class="text-sm text-slate-500">Aktifkan produk bertipe jual untuk menerima pesanan.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4" data-pos-products>
                            @foreach ($produks as $produk)
                                <article class="pos-product-card" data-pos-product data-search="{{ strtolower($produk->nama_produk . ' ' . ($produk->kategoriProduk->nama_kategori ?? '')) }}" data-category="{{ strtolower($produk->kategoriProduk->nama_kategori ?? '') }}">
                                    @if($produk->gambar)
                                        <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama_produk }}" class="pos-product-card-img">
                                    @else
                                        <div class="w-full h-32 bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center border-b border-slate-100">
                                            <svg class="w-12 h-12 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="p-3">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-[10px] font-bold text-primary-600 uppercase tracking-wider">{{ $produk->kategoriProduk->nama_kategori ?? 'Menu' }}</span>
                                            @if($produk->stock < 5)
                                                <span class="text-[10px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded">Stok: {{ $produk->stock }}</span>
                                            @else
                                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">Stok: {{ $produk->stock }}</span>
                                            @endif
                                        </div>
                                        <h2 class="font-bold text-slate-900 text-sm mb-1 line-clamp-2 min-h-[2.5rem]">{{ $produk->nama_produk }}</h2>
                                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100">
                                            <span class="font-bold text-primary-600 text-sm">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
                                            <button
                                                class="pos-add-btn w-7 h-7 rounded-lg bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                type="button"
                                                data-add-product
                                                data-id="{{ $produk->id }}"
                                                data-name="{{ $produk->nama_produk }}"
                                                data-price="{{ (float) $produk->harga_jual }}"
                                                data-stock="{{ $produk->stock }}"
                                                aria-label="Tambah {{ $produk->nama_produk }}"
                                                @disabled($produk->stock < 1)
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        <p class="pos-no-results hidden text-center py-12 text-slate-500" data-pos-no-results>Menu tidak ditemukan. Coba kata pencarian lain.</p>
                    @endif
                </div>

                <!-- 2. VIEW DENAH MEJA (Tanpa pindah halaman) -->
                <div id="pos-view-denah" class="hidden space-y-4">
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-100">
                            <div>
                                <h2 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Denah Meja Outlet Live
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">Klik meja yang kosong untuk langsung memilihnya pada pesanan kasir ini.</p>
                            </div>
                            <button type="button" onclick="switchPosView('menu')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali ke Menu
                            </button>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @forelse($mejas as $meja)
                                @php
                                    $isOccupied = in_array($meja->id, $mejaStatus ?? []);
                                    $activeOrder = ($activeOrders ?? collect())->get($meja->id);
                                @endphp
                                <div class="pos-table-card rounded-2xl border-2 transition-all p-4 text-center relative flex flex-col justify-between {{ $isOccupied ? 'border-amber-200 bg-amber-50/70' : 'border-slate-200 bg-white hover:border-primary-400 hover:shadow-md' }}"
                                     id="table-card-{{ $meja->id }}"
                                     data-meja-id="{{ $meja->id }}"
                                     data-meja-nomor="{{ $meja->nomor_meja }}">
                                    
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Meja</span>
                                            <span class="text-[10px] font-semibold text-slate-500">{{ $meja->kapasitas }} Kursi</span>
                                        </div>
                                        <div class="text-3xl font-black mb-2 {{ $isOccupied ? 'text-amber-700' : 'text-slate-900' }}">
                                            {{ str_pad($meja->nomor_meja, 2, '0', STR_PAD_LEFT) }}
                                        </div>
                                    </div>

                                    @if($isOccupied)
                                        <div class="mt-2 space-y-2">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Terisi
                                            </span>
                                            @if($activeOrder)
                                                <div class="text-[10px] text-slate-600 bg-white/80 rounded-lg p-1.5 border border-amber-200 text-left">
                                                    <div class="font-bold text-slate-900">#{{ $activeOrder->id }} · Rp {{ number_format($activeOrder->total_harga, 0, ',', '.') }}</div>
                                                    <div class="text-[9px] text-slate-500 capitalize">{{ $activeOrder->status_pesanan }}</div>
                                                </div>
                                            @endif
                                            <div class="flex gap-1.5 mt-2">
                                                @if($activeOrder)
                                                    <a href="{{ route('transaksis.show', $activeOrder->id) }}" class="flex-1 py-1.5 bg-white border border-amber-300 text-amber-800 rounded-lg text-[10px] font-bold hover:bg-amber-100 transition">
                                                        Detail
                                                    </a>
                                                @endif
                                                <button type="button" onclick="selectTableFromDenah({{ $meja->id }}, '{{ $meja->nomor_meja }}')" class="flex-1 py-1.5 bg-amber-600 text-white rounded-lg text-[10px] font-bold hover:bg-amber-700 transition">
                                                    Pilih
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-2">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-2">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Tersedia
                                            </span>
                                            <button type="button" onclick="selectTableFromDenah({{ $meja->id }}, '{{ $meja->nomor_meja }}')" class="w-full py-2 bg-primary-50 text-primary-700 hover:bg-primary-600 hover:text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Pilih Meja Ini
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="col-span-full py-8 text-center text-slate-500 text-sm">
                                    Belum ada data meja aktif.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </section>

            <!-- PANEL ORDER / KERANJANG (1/3 lebar) -->
            <aside class="pos-order-panel" id="pos-order" aria-labelledby="pos-order-title">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 id="pos-order-title" class="font-bold text-slate-900">Pesanan Baru</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Periksa item sebelum disimpan</p>
                    </div>
                    <span class="px-2.5 py-1 bg-primary-100 text-primary-700 rounded-lg text-xs font-bold" data-cart-count>0 item</span>
                </div>

                <p class="pos-cart-empty text-center py-12 text-slate-500 text-sm" data-cart-empty>
                    Keranjang kosong.<br>Tambahkan menu dari katalog.
                </p>
                <div class="pos-cart-lines max-h-96 overflow-y-auto p-4" data-cart-lines aria-live="polite"></div>

                <form class="pos-order-form p-4 border-t border-slate-200" id="pos-form" method="post" action="{{ route('transaksis.store') }}">
                    @csrf
                    <div data-cart-inputs></div>

                    <!-- Tipe Pesanan -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Tipe Pesanan</label>
                        <div class="pos-type-selector">
                            <label class="pos-type-option">
                                <input type="radio" name="tipe_pesanan" value="dine-in" checked>
                                <span>🍽️ Makan di Tempat</span>
                            </label>
                            <label class="pos-type-option">
                                <input type="radio" name="tipe_pesanan" value="take-away">
                                <span>🥡 Bawa Pulang</span>
                            </label>
                        </div>
                    </div>

                    <!-- Pilih Meja -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-slate-700">
                                Pilih Meja <span class="text-red-500">*</span>
                            </label>
                            <button type="button" onclick="switchPosView('denah')" class="text-xs text-primary-600 hover:text-primary-700 font-bold flex items-center gap-1 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Buka Denah Meja
                            </button>
                        </div>
                        <select name="meja_id" data-table-select required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Pilih Meja --</option>
                            @foreach ($mejas as $meja)
                                @php
                                    $isMejaOcc = in_array($meja->id, $mejaStatus ?? []);
                                @endphp
                                <option value="{{ $meja->id }}" data-nomor="{{ $meja->nomor_meja }}" {{ (isset($selectedMejaId) && $selectedMejaId == $meja->id) ? 'selected' : '' }}>
                                    Meja {{ $meja->nomor_meja }} · {{ $meja->kapasitas }} kursi {{ $isMejaOcc ? '(Terisi)' : '(Kosong)' }}
                                </option>
                            @endforeach
                        </select>
                        <div id="selected-table-indicator" class="mt-2 p-2 bg-primary-50 border border-primary-200 rounded-xl flex items-center justify-between text-xs {{ isset($selectedMejaId) ? '' : 'hidden' }}">
                            <div class="flex items-center gap-1.5 text-primary-800 font-bold">
                                <span class="w-2 h-2 rounded-full bg-primary-600"></span>
                                <span id="selected-table-text">Meja Terpilih</span>
                            </div>
                            <button type="button" onclick="switchPosView('denah')" class="text-[11px] font-semibold text-primary-600 hover:text-primary-800 underline">Ganti Meja</button>
                        </div>
                        <p class="pos-field-hint text-xs text-slate-500 mt-1.5" data-takeaway-hint hidden>Pesanan bawa pulang tidak memerlukan meja.</p>
                        @if ($mejas->isEmpty())
                            <span class="text-xs text-red-600 mt-1.5 block">Tidak ada meja aktif. Aktifkan meja untuk melanjutkan pesanan.</span>
                        @endif
                    </div>

                    <!-- Catatan -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Catatan Pesanan (Opsional)</label>
                        <textarea name="catatan" rows="2" placeholder="Contoh: Jangan terlalu manis..." class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 resize-none"></textarea>
                    </div>

                    <!-- Ringkasan Total -->
                    <div class="space-y-2 pt-3 border-t border-slate-200">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Subtotal</span>
                            <strong class="text-slate-900" data-cart-subtotal>Rp 0</strong>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Pajak & Layanan (10%)</span>
                            <strong class="text-slate-900" data-cart-tax>Rp 0</strong>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-slate-200">
                            <span class="font-bold text-slate-900">Total Bayar</span>
                            <strong class="text-xl font-bold text-primary-600" data-cart-total>Rp 0</strong>
                        </div>
                    </div>

                    <button class="pos-checkout w-full mt-4 px-4 py-3 bg-primary-600 text-white rounded-xl font-bold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2" type="submit" data-checkout disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Pesanan
                    </button>
                </form>
            </aside>
        </div>
    </main>
</div>

<script>
    (() => {
        const products = document.querySelector('[data-pos-products]');
        const search = document.querySelector('#pos-search');
        const noResults = document.querySelector('[data-pos-no-results]');
        const cartLines = document.querySelector('[data-cart-lines]');
        const cartEmpty = document.querySelector('[data-cart-empty]');
        const cartInputs = document.querySelector('[data-cart-inputs]');
        const cartCount = document.querySelector('[data-cart-count]');
        const cartSubtotal = document.querySelector('[data-cart-subtotal]');
        const cartTax = document.querySelector('[data-cart-tax]');
        const cartTotal = document.querySelector('[data-cart-total]');
        const tableSelect = document.querySelector('[data-table-select]');
        const orderTypeInputs = document.querySelectorAll('input[name="tipe_pesanan"]');
        const takeawayHint = document.querySelector('[data-takeaway-hint]');
        const checkout = document.querySelector('[data-checkout]');
        const form = document.querySelector('#pos-form');
        const categoryTabs = document.querySelectorAll('.pos-category-tab');
        const selectedTableIndicator = document.querySelector('#selected-table-indicator');
        const selectedTableText = document.querySelector('#selected-table-text');

        // View Tabs Elements
        const viewMenu = document.querySelector('#pos-view-menu');
        const viewDenah = document.querySelector('#pos-view-denah');
        const tabBtnMenu = document.querySelector('#tab-btn-menu');
        const tabBtnDenah = document.querySelector('#tab-btn-denah');
        const denahLegendBar = document.querySelector('#denah-legend-bar');

        const cart = new Map();
        let activeCategory = 'all';
        const currency = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        // ==========================================
        // TOAST NOTIFICATION
        // ==========================================
        function showToast(message) {
            const toast = document.querySelector('#pos-toast');
            const toastMsg = document.querySelector('#pos-toast-msg');
            if (!toast || !toastMsg) return;

            toastMsg.textContent = message;
            toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
            toast.classList.add('translate-y-0', 'opacity-100');

            clearTimeout(window._toastTimeout);
            window._toastTimeout = setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 2500);
        }

        // ==========================================
        // SPA VIEW SWITCHER (MENU vs DENAH MEJA)
        // ==========================================
        window.switchPosView = function(viewName) {
            const sidebarMenu = document.querySelector('#sidebar-nav-menu');
            const sidebarDenah = document.querySelector('#sidebar-nav-denah');

            if (viewName === 'denah') {
                viewMenu?.classList.add('hidden');
                viewDenah?.classList.remove('hidden');
                denahLegendBar?.classList.remove('hidden');
                denahLegendBar?.classList.add('flex');

                // Update Tab Buttons
                tabBtnMenu?.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
                tabBtnMenu?.classList.add('text-slate-600', 'hover:text-slate-900');

                tabBtnDenah?.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
                tabBtnDenah?.classList.remove('text-slate-600', 'hover:text-slate-900');

                // Update Sidebar
                sidebarMenu?.classList.remove('active');
                sidebarMenu?.classList.add('inactive');
                sidebarDenah?.classList.add('active');
                sidebarDenah?.classList.remove('inactive');

                if (history.replaceState) {
                    history.replaceState(null, null, '#denah');
                }
            } else {
                viewMenu?.classList.remove('hidden');
                viewDenah?.classList.add('hidden');
                denahLegendBar?.classList.add('hidden');
                denahLegendBar?.classList.remove('flex');

                // Update Tab Buttons
                tabBtnMenu?.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
                tabBtnMenu?.classList.remove('text-slate-600', 'hover:text-slate-900');

                tabBtnDenah?.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
                tabBtnDenah?.classList.add('text-slate-600', 'hover:text-slate-900');

                // Update Sidebar
                sidebarMenu?.classList.add('active');
                sidebarMenu?.classList.remove('inactive');
                sidebarDenah?.classList.remove('active');
                sidebarDenah?.classList.add('inactive');

                if (history.replaceState) {
                    history.replaceState(null, null, '#menu');
                }
            }
        };

        // ==========================================
        // PILIH MEJA DARI DENAH (TANPA PINDAH HALAMAN)
        // ==========================================
        window.selectTableFromDenah = function(mejaId, mejaNomor) {
            // 1. Set dropdown value
            if (tableSelect) {
                tableSelect.value = String(mejaId);
            }

            // 2. Set order type to dine-in
            const dineInRadio = document.querySelector('input[name="tipe_pesanan"][value="dine-in"]');
            if (dineInRadio) {
                dineInRadio.checked = true;
                syncOrderType();
            }

            // 3. Update Indicator & Highlight Card
            updateTableSelectionUI(mejaId, mejaNomor);

            // 4. Kembali otomatis ke Katalog Menu
            window.switchPosView('menu');

            // 5. Beri feedback Toast
            showToast(`Meja ${mejaNomor} terpilih untuk pesanan ini!`);
        };

        function updateTableSelectionUI(mejaId, mejaNomor) {
            if (!mejaId) {
                selectedTableIndicator?.classList.add('hidden');
                document.querySelectorAll('.pos-table-card').forEach(card => card.classList.remove('ring-4', 'ring-primary-400'));
                return;
            }

            // Update badge in cart
            if (selectedTableIndicator && selectedTableText) {
                selectedTableText.textContent = `Meja ${mejaNomor || mejaId} Terpilih`;
                selectedTableIndicator.classList.remove('hidden');
            }

            // Highlight in denah
            document.querySelectorAll('.pos-table-card').forEach(card => {
                if (card.dataset.mejaId === String(mejaId)) {
                    card.classList.add('ring-4', 'ring-primary-400');
                } else {
                    card.classList.remove('ring-4', 'ring-primary-400');
                }
            });
        }

        // ==========================================
        // POS CART LOGIC
        // ==========================================
        function selectedOrderType() {
            return form.querySelector('input[name="tipe_pesanan"]:checked').value;
        }

        function makeButton(label, action, product, ariaLabel) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'pos-qty-btn';
            button.textContent = label;
            button.dataset.cartAction = action;
            button.dataset.id = product.id;
            button.setAttribute('aria-label', ariaLabel);
            return button;
        }

        function renderCart() {
            cartLines.replaceChildren();
            cartInputs.replaceChildren();
            let itemCount = 0;
            let subtotal = 0;
            let index = 0;

            for (const product of cart.values()) {
                itemCount += product.qty;
                subtotal += product.price * product.qty;

                const line = document.createElement('div');
                line.className = 'pos-cart-item';

                const detail = document.createElement('div');
                detail.className = 'flex-1 min-w-0';
                const name = document.createElement('div');
                name.className = 'font-bold text-slate-900 text-sm truncate';
                name.textContent = product.name;
                const price = document.createElement('div');
                price.className = 'text-xs text-slate-500 mt-0.5';
                price.textContent = currency.format(product.price * product.qty);
                detail.append(name, price);

                const customizationLabel = document.createElement('div');
                customizationLabel.className = 'mt-2 pt-2 border-t border-slate-200';
                const customTitle = document.createElement('div');
                customTitle.className = 'text-[10px] font-bold text-slate-500 uppercase mb-1.5';
                customTitle.textContent = 'Kustomisasi';
                customizationLabel.appendChild(customTitle);

                const presetsContainer = document.createElement('div');
                presetsContainer.className = 'grid grid-cols-2 gap-1 mb-2';
                const presets = ['Es sedikit', 'Gula aren dipisah', 'Sirup dipisah', 'Oatmilk (+Rp 8.000)'];

                presets.forEach(preset => {
                    const label = document.createElement('label');
                    label.className = 'pos-customization-preset';

                    const input = document.createElement('input');
                    input.type = 'checkbox';
                    input.checked = (product.customization || '').includes(preset);

                    const text = document.createTextNode(preset);
                    label.append(input, text);

                    presetsContainer.append(label);
                });

                const customization = document.createElement('textarea');
                customization.className = 'w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs resize-none focus:outline-none focus:ring-1 focus:ring-primary-500';
                customization.name = `items[${index}][customization]`;
                customization.maxLength = 250;
                customization.rows = 2;
                customization.placeholder = 'Catatan tambahan...';

                const currentCustom = product.customization || '';
                const noteOnly = currentCustom.split(', ').filter(p => !presets.includes(p)).join(', ');
                customization.value = noteOnly;

                const updateCustomization = () => {
                    const checked = [];
                    presetsContainer.querySelectorAll('input:checked').forEach(cb => {
                        checked.push(cb.nextSibling.textContent.trim());
                    });
                    const note = customization.value.trim();
                    product.customization = [checked.join(', '), note].filter(Boolean).join(', ');
                };

                presetsContainer.querySelectorAll('input').forEach(cb => {
                    cb.addEventListener('change', updateCustomization);
                });
                customization.addEventListener('input', updateCustomization);

                customizationLabel.append(presetsContainer, customization);

                const controls = document.createElement('div');
                controls.className = 'flex items-center gap-1.5 ml-3';
                controls.append(makeButton('−', 'decrease', product, `Kurangi ${product.name}`));
                const quantity = document.createElement('span');
                quantity.className = 'w-6 text-center font-bold text-sm text-slate-900';
                quantity.textContent = product.qty;
                controls.append(quantity);
                const increase = makeButton('+', 'increase', product, `Tambah ${product.name}`);
                increase.disabled = product.qty >= product.stock;
                controls.append(increase);

                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'text-xs text-red-600 hover:text-red-700 font-semibold ml-2';
                remove.dataset.cartAction = 'remove';
                remove.dataset.id = product.id;
                remove.textContent = 'Hapus';

                line.append(detail, controls);
                cartLines.append(line, customizationLabel, remove);

                const productId = document.createElement('input');
                productId.type = 'hidden';
                productId.name = `items[${index}][produk_id]`;
                productId.value = product.id;
                const productQty = document.createElement('input');
                productQty.type = 'hidden';
                productQty.name = `items[${index}][qty]`;
                productQty.value = product.qty;
                cartInputs.append(productId, productQty);
                index += 1;
            }

            cartEmpty.hidden = cart.size > 0;
            cartCount.textContent = `${itemCount} item`;
            const tax = Math.round(subtotal * 0.1);
            cartSubtotal.textContent = currency.format(subtotal);
            cartTax.textContent = currency.format(tax);
            cartTotal.textContent = currency.format(subtotal + tax);
            checkout.disabled = cart.size === 0 || (selectedOrderType() === 'dine-in' && !tableSelect.value);

            document.querySelectorAll('[data-add-product]').forEach((button) => {
                const product = cart.get(button.dataset.id);
                button.disabled = Number(button.dataset.stock) < 1 || (product && product.qty >= product.stock);
            });
        }

        products?.addEventListener('click', (event) => {
            const button = event.target.closest('[data-add-product]');
            if (!button || button.disabled) return;

            const id = button.dataset.id;
            const product = cart.get(id) ?? {
                id,
                name: button.dataset.name,
                price: Number(button.dataset.price),
                stock: Number(button.dataset.stock),
                qty: 0,
                customization: '',
            };
            product.qty = Math.min(product.qty + 1, product.stock);
            cart.set(id, product);
            renderCart();
            showToast(`${product.name} ditambahkan`);
        });

        cartLines.addEventListener('click', (event) => {
            const button = event.target.closest('[data-cart-action]');
            if (!button) return;

            const product = cart.get(button.dataset.id);
            if (!product) return;
            if (button.dataset.cartAction === 'increase') product.qty = Math.min(product.qty + 1, product.stock);
            if (button.dataset.cartAction === 'decrease') product.qty -= 1;
            if (button.dataset.cartAction === 'remove' || product.qty < 1) cart.delete(product.id);
            else cart.set(product.id, product);
            renderCart();
        });

        search?.addEventListener('input', () => {
            const query = search.value.trim().toLowerCase();
            let visibleCount = 0;
            document.querySelectorAll('[data-pos-product]').forEach((product) => {
                const matchSearch = product.dataset.search.includes(query);
                const matchCategory = activeCategory === 'all' || product.dataset.category === activeCategory;
                product.hidden = !(matchSearch && matchCategory);
                if (!product.hidden) visibleCount += 1;
            });
            if (noResults) noResults.hidden = visibleCount > 0;
        });

        categoryTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                categoryTabs.forEach(t => {
                    t.classList.remove('active');
                    t.classList.add('inactive');
                });
                tab.classList.remove('inactive');
                tab.classList.add('active');
                activeCategory = tab.dataset.category;
                search?.dispatchEvent(new Event('input'));
            });
        });

        function syncOrderType() {
            const dineIn = selectedOrderType() === 'dine-in';
            tableSelect.disabled = !dineIn;
            tableSelect.required = dineIn;
            takeawayHint.hidden = dineIn;
            if (!dineIn) {
                selectedTableIndicator?.classList.add('hidden');
            } else if (tableSelect.value) {
                const opt = tableSelect.options[tableSelect.selectedIndex];
                updateTableSelectionUI(tableSelect.value, opt?.dataset?.nomor || opt?.text?.split('·')[0]);
            }
            renderCart();
        }

        orderTypeInputs.forEach((input) => input.addEventListener('change', syncOrderType));
        tableSelect?.addEventListener('change', () => {
            const opt = tableSelect.options[tableSelect.selectedIndex];
            updateTableSelectionUI(tableSelect.value, opt?.dataset?.nomor || opt?.text?.split('·')[0]);
            renderCart();
        });

        form?.addEventListener('submit', (event) => {
            const dineIn = selectedOrderType() === 'dine-in';
            if (cart.size === 0 || (dineIn && !tableSelect.value)) {
                event.preventDefault();
                return;
            }
            checkout.disabled = true;
            checkout.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan pesanan...`;
        });

        // ==========================================
        // INITIALIZATION
        // ==========================================
        syncOrderType();

        // Check URL hash on load
        if (window.location.hash === '#denah') {
            window.switchPosView('denah');
        } else {
            window.switchPosView('menu');
        }

        // Initialize table selection if already chosen
        if (tableSelect && tableSelect.value) {
            const opt = tableSelect.options[tableSelect.selectedIndex];
            updateTableSelectionUI(tableSelect.value, opt?.dataset?.nomor || opt?.text?.split('·')[0]);
        }
    })();
</script>
@endsection