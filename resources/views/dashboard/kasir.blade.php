@extends('layouts.kasir')

@section('content')
@php
    // Query produk & kategori inline (tidak mengubah route)
    $kategoriProduks = \App\Models\KategoriProduk::untukKatalog()->orderBy('nama_kategori')->get();
    $produks = \App\Models\Produk::with(['kategoriProduk', 'opsis'])
        ->where('tipe', 'jual')
        ->where('status_aktif', true)
        ->orderBy('nama_produk')
        ->get();

    $produksData = $produks->map(fn($p) => [
        'id' => $p->id,
        'opsis' => $p->opsis->map(fn($o) => [
            'nama' => $o->nama_opsi,
            'harga' => (float) $o->harga_tambahan
        ])
    ])->keyBy('id');

    $statusLabels = [
        'pending' => 'Menunggu',
        'ready' => 'Siap',
        'prosesed' => 'Diproses',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    $hour = now('Asia/Jakarta')->hour;
    $shift = match(true) {
        $hour >= 6 && $hour < 14 => 'Pagi (06:00 - 14:00)',
        $hour >= 14 && $hour < 22 => 'Siang/Sore (14:00 - 22:00)',
        default => 'Malam (22:00 - 06:00)',
    };
@endphp

<div class="kasir-pos min-h-screen lg:h-screen flex flex-col bg-slate-50 lg:overflow-hidden">

    {{-- ═══════════════════════════════════════════════════════════════
        TOP BAR — Terminal title, shift info, kasir, time, tab buttons
        ═══════════════════════════════════════════════════════════════ --}}
    <header class="kasir-topbar bg-white border-b border-slate-200/80 flex-shrink-0 z-20">
        <div class="px-3 sm:px-5 py-3 flex flex-wrap items-center justify-between gap-2.5">
            <div class="flex items-center gap-3 min-w-0">
                <div class="hidden sm:flex relative w-11 h-11 rounded-2xl bg-primary-600 text-white items-center justify-center shrink-0">
                    <svg class="relative w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 1v3M10 1v3M14 1v3" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 whitespace-nowrap tracking-tight">Terminal Kasir</h1>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-[9px] sm:text-[10px] font-extrabold whitespace-nowrap">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Aktif
                        </span>
                    </div>
                    <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5 truncate">Shift {{ $shift }} · Siap menerima pesanan</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <div class="kasir-badge hidden xl:inline-flex">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ $shift }}
                </div>
                <div class="kasir-badge hidden sm:inline-flex max-w-[170px]">
                    <span class="w-5 h-5 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-[9px] font-extrabold shrink-0">{{ substr(auth('karyawan')->user()->nama_karyawan, 0, 1) }}</span>
                    <span class="truncate">{{ auth('karyawan')->user()->nama_karyawan }}</span>
                </div>
                <div class="kasir-badge inline-flex" id="live-clock">
                    <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span id="clock-text" class="tabular-nums">{{ now('Asia/Jakarta')->format('H:i:s') }}</span>
                    <span class="text-slate-400 font-medium">WIB</span>
                </div>
            </div>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════════════════════════
         TAB CONTENT AREA — fills remaining height
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="flex-1 lg:overflow-hidden">

        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
             TAB 1: KASIR & MENU — POS Ordering Interface
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
        <div id="tab-kasir" class="flex flex-col lg:h-full lg:min-h-0">
            {{-- Search & Category Filter Bar --}}
            <div class="px-3 sm:px-5 pt-4 pb-1 flex-shrink-0">
                <div class="pos-toolbar rounded-2xl border border-slate-200/80 bg-white shadow-[0_10px_35px_-25px_rgba(15,23,42,.45)] p-2.5 sm:p-3">
                    <div class="flex flex-col gap-2.5 xl:flex-row xl:items-center">
                        <div class="flex items-center gap-3 xl:w-52 shrink-0">
                            <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h10" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-slate-900 leading-tight">Katalog Menu</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $produks->count() }} menu aktif</p>
                            </div>
                        </div>
                        <div class="relative flex-1 min-w-0">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input id="pos-search" type="search" placeholder="Cari nama menu atau kategori..." aria-label="Cari menu"
                                class="w-full pl-10 pr-4 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-400 focus:bg-white transition-all">
                        </div>
                        <div class="flex items-center gap-1.5 overflow-x-auto hide-scrollbar pb-0.5 xl:max-w-[55%]">
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
                </div>
            </div>

            {{-- Split layout: Catalog (left 2/3) + Cart (right 1/3) --}}
            <div class="flex flex-col lg:flex-row lg:flex-1 lg:min-h-0 lg:overflow-hidden">

                {{-- Product Catalog (scrollable) --}}
                <section class="flex-1 lg:w-2/3 lg:overflow-y-auto p-3 sm:p-5" id="catalog-section">
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
                        <div class="mb-4 flex items-end justify-between gap-3">
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-primary-600">Menu Tersedia</p>
                                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight mt-0.5">Pilih menu untuk pesanan</h2>
                                <p class="text-[11px] text-slate-500 mt-1">Klik tombol + pada kartu untuk menambahkan ke keranjang.</p>
                            </div>
                            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-slate-200 text-[11px] font-bold text-slate-600 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $produks->count() }} item
                            </span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-3 sm:gap-4" data-pos-products>
                            @foreach ($produks as $produk)
                                <article @class([
                                    'pos-product-card group',
                                    'is-sold-out' => $produk->stock < 1,
                                ]) data-pos-product data-search="{{ strtolower($produk->nama_produk . ' ' . ($produk->kategoriProduk->nama_kategori ?? '')) }}" data-category="{{ strtolower($produk->kategoriProduk->nama_kategori ?? '') }}">
                                    <div class="pos-product-media relative">
                                        @if($produk->gambar_url)
                                            <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama_produk }}" class="pos-product-card-img" loading="lazy">
                                        @else
                                            <div class="w-full h-full bg-primary-50 flex items-center justify-center">
                                                <svg class="w-12 h-12 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="absolute inset-x-0 bottom-0 h-12 bg-slate-900/20 pointer-events-none"></div>
                                        <span class="absolute left-2.5 top-2.5 px-2 py-1 rounded-lg bg-white/90 backdrop-blur text-[9px] font-extrabold text-primary-700 uppercase tracking-wider shadow-sm">
                                            {{ $produk->kategoriProduk->nama_kategori ?? 'Menu' }}
                                        </span>
                                        @if($produk->stock < 1)
                                            <span class="absolute right-2.5 top-2.5 px-2 py-1 rounded-lg bg-red-500 text-white text-[9px] font-extrabold uppercase tracking-wider shadow-sm">Habis</span>
                                        @else
                                            <span class="absolute right-2.5 top-2.5 px-2 py-1 rounded-lg bg-slate-900/65 backdrop-blur text-white text-[9px] font-bold shadow-sm">Sisa {{ $produk->stock }}</span>
                                        @endif
                                    </div>
                                    <div class="p-3.5">
                                        <h2 class="font-extrabold text-slate-900 text-sm leading-snug line-clamp-2 min-h-[2.5rem]">{{ $produk->nama_produk }}</h2>
                                        <p class="text-[10px] text-slate-500 line-clamp-1 mt-1">{{ $produk->deskripsi ?: 'Menu pilihan Bagi Kopi' }}</p>
                                        <div class="flex items-end justify-between gap-2 mt-3 pt-3 border-t border-slate-100">
                                            <div>
                                                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Harga</span>
                                                <span class="font-extrabold text-slate-900 text-[15px] tracking-tight">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
                                            </div>
                                            <button
                                                class="pos-add-btn"
                                                type="button"
                                                data-add-product
                                                data-id="{{ $produk->id }}"
                                                data-name="{{ $produk->nama_produk }}"
                                                data-price="{{ (float) $produk->harga_jual }}"
                                                data-stock="{{ $produk->stock }}"
                                                aria-label="Tambah {{ $produk->nama_produk }}"
                                                @disabled($produk->stock < 1)>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        <p class="pos-no-results text-center py-12 text-slate-500" data-pos-no-results hidden>Menu tidak ditemukan. Coba kata pencarian lain.</p>
                    @endif
                </section>

                {{-- Order Panel / Cart (sticky right) --}}
                <aside class="pos-order-panel w-full lg:w-[390px] flex-shrink-0 border-t lg:border-t-0 lg:border-l border-slate-200/80 flex flex-col bg-white" id="pos-order" aria-labelledby="pos-order-title">
                    <div class="pos-cart-header p-4 sm:p-5 bg-slate-50 border-b border-slate-200 flex items-center justify-between flex-shrink-0 relative overflow-hidden">
                        <div class="absolute -right-8 -top-10 w-32 h-32 rounded-full bg-primary-100/60 blur-2xl pointer-events-none"></div>
                        <div class="relative flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-2xl bg-white border border-primary-100 text-primary-600 flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            </div>
                            <div class="min-w-0">
                                <h2 id="pos-order-title" class="font-extrabold text-slate-900 tracking-tight">Pesanan Baru</h2>
                                <p class="text-[10px] text-slate-500 mt-0.5">Periksa item sebelum disimpan</p>
                            </div>
                        </div>
                        <span class="relative px-2.5 py-1 bg-primary-100 border border-primary-200 text-primary-700 rounded-lg text-[11px] font-extrabold" data-cart-count>0 item</span>
                    </div>

                    <div class="pos-panel-scroll">
                    <div class="pos-cart-empty text-center px-6 py-10 text-slate-500 text-sm" data-cart-empty>
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <p class="font-bold text-slate-700">Keranjang masih kosong</p>
                        <p class="text-[11px] text-slate-400 mt-1">Tambahkan menu dari katalog untuk memulai pesanan.</p>
                    </div>
                    <div class="pos-cart-lines flex-1 overflow-y-auto p-3 sm:p-4 space-y-2" data-cart-lines aria-live="polite"></div>

                    <form class="pos-order-form p-4 border-t border-slate-200/80 flex-shrink-0 bg-white" id="pos-form" method="post" action="{{ route('transaksis.store') }}">
                        @csrf
                         <input type="hidden" name="metode_pembayaran" value="cash">
                        <div data-cart-inputs></div>

                        {{-- Tipe Pesanan --}}
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Tipe Pesanan</label>
                                <span class="text-[9px] font-bold text-slate-400">Wajib dipilih</span>
                            </div>
                            <div class="pos-type-selector">
                                <label class="pos-type-option">
                                    <input type="radio" name="tipe_pesanan" value="dine-in" checked>
                                    <span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M5 10v7m14-7v7M7 21v-4m10 4v-4M8 3v4m4-4v4m4-4v4" /></svg>
                                        Makan di Tempat
                                    </span>
                                </label>
                                <label class="pos-type-option">
                                    <input type="radio" name="tipe_pesanan" value="take-away">
                                    <span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                        Bawa Pulang
                                    </span>
                                </label>
                            </div>
                        </div>

                        {{-- Pilih Meja --}}
                        <div class="mb-4">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                Pilih Meja <span class="text-red-500">*</span>
                            </label>
                            <select name="meja_id" data-table-select required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-400 focus:bg-white transition-all">
                                <option value="">-- Pilih Meja --</option>
                                @foreach ($mejas as $meja)
                                    <option value="{{ $meja->id }}">Meja {{ $meja->nomor_meja }} · {{ $meja->kapasitas }} kursi</option>
                                @endforeach
                            </select>
                            <p class="pos-field-hint text-[11px] text-slate-500 mt-1.5" data-takeaway-hint hidden>Pesanan bawa pulang tidak memerlukan meja.</p>
                            @if ($mejas->isEmpty())
                                <span class="text-xs text-red-600 mt-1.5 block">Tidak ada meja aktif. Aktifkan meja untuk melanjutkan pesanan.</span>
                            @endif
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-4">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-2">Catatan Pesanan <span class="text-slate-400 font-semibold normal-case">(Opsional)</span></label>
                            <textarea name="catatan" rows="2" placeholder="Contoh: Jangan terlalu manis..." class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-400 focus:bg-white transition-all resize-none"></textarea>
                        </div>

                        {{-- Ringkasan Total --}}
                        <div class="space-y-2.5 pt-3 border-t border-slate-200">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500 font-medium">Subtotal</span>
                                <strong class="text-slate-900" data-cart-subtotal>Rp 0</strong>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500 font-medium">Pajak &amp; Layanan (10%)</span>
                                <strong class="text-slate-900" data-cart-tax>Rp 0</strong>
                            </div>
                            <div class="mt-3 rounded-2xl bg-white border border-slate-200 p-4 flex items-center justify-between gap-3 shadow-sm">
                                <div>
                                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-[0.16em]">Total Bayar</span>
                                    <strong class="block text-xl sm:text-2xl font-extrabold text-primary-700 tracking-tight mt-0.5" data-cart-total>Rp 0</strong>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-primary-50 border border-primary-100 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                </div>
                            </div>
                        </div>

                        <button class="pos-checkout w-full mt-4 px-4 py-3.5 bg-primary-600 text-white rounded-xl font-extrabold text-sm hover:bg-primary-700 transition-all shadow-lg shadow-primary-600/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2" type="submit" data-checkout disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Pesanan
                        </button>
                        <p class="text-center text-[10px] text-slate-400 mt-2">Pesanan akan masuk ke antrean pesanan aktif.</p>
                    </form>
                    </div>
                </aside>
            </div>
        </div>

        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
             TAB 2: DENAH MEJA — Live Floor Plan Terminal
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
        <div id="tab-meja" class="lg:h-full lg:overflow-hidden hidden flex flex-col">
            {{-- Top summary bar for Denah --}}
            <div class="bg-white border-b border-slate-200 px-3 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-4 flex-shrink-0">
                <div class="flex flex-wrap items-center gap-3 sm:gap-5 min-w-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-2xl bg-primary-50 border border-primary-100 text-primary-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <div class="min-w-0">
                            <h2 class="font-extrabold text-slate-900 text-base sm:text-lg leading-tight tracking-tight">Denah Meja</h2>
                            <p class="text-[11px] text-slate-500 mt-0.5">Pantau ketersediaan dan kelola pesanan per meja.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 text-xs bg-slate-100 border border-slate-200/70 p-1 rounded-xl overflow-x-auto hide-scrollbar max-w-full">
                        <button type="button" class="table-filter-pill active" data-filter="all" onclick="filterTables('all')">
                            Semua (<span id="count-all-tables">{{ $mejas->count() }}</span>)
                        </button>
                        <button type="button" class="table-filter-pill inactive" data-filter="kosong" onclick="filterTables('kosong')">
                            Tersedia (<span id="count-kosong-tables">{{ max(0, $mejas->count() - $activeOrders->count()) }}</span>)
                        </button>
                        <button type="button" class="table-filter-pill inactive" data-filter="terisi" onclick="filterTables('terisi')">
                            Terisi (<span id="count-terisi-tables">{{ $activeOrders->count() }}</span>)
                        </button>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold">
                    <span class="inline-flex items-center gap-1.5 text-emerald-700 bg-emerald-50 px-2.5 py-1.5 rounded-lg border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Tersedia
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-amber-700 bg-amber-50 px-2.5 py-1.5 rounded-lg border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Terisi
                    </span>
                </div>
            </div>

            {{-- Split Layout: Tables Grid (Left 2/3) + Table Detail & Cashier Panel (Right 1/3) --}}
            <div class="flex-1 flex flex-col lg:flex-row lg:overflow-hidden">
                
                {{-- LEFT: Live Table Floor Plan Grid (Tanpa pindah halaman) --}}
                <div class="flex-1 lg:w-2/3 lg:overflow-y-auto p-3 sm:p-6 bg-slate-50">
                    @if($mejas->isEmpty())
                        <div class="p-12 text-center bg-white rounded-2xl border border-slate-200">
                            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Tidak ada meja aktif</h3>
                            <p class="text-sm text-slate-500">Aktifkan meja di panel admin untuk mulai menerima pesanan dine-in.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" id="table-grid-container">
                            @foreach($mejas as $meja)
                                @php
                                    $order = $activeOrders[$meja->id] ?? null;
                                    $isOccupied = !is_null($order);
                                @endphp
                                <div class="terminal-table-card relative overflow-hidden p-4 rounded-2xl border transition-all duration-200 text-left cursor-pointer select-none {{ $isOccupied ? 'border-amber-200 bg-amber-50 hover:border-amber-300' : 'border-slate-200 bg-white hover:border-primary-300' }} shadow-sm"
                                    id="table-node-{{ $meja->id }}"
                                    onclick="inspectTable({{ $meja->id }})"
                                    data-meja-id="{{ $meja->id }}"
                                    data-status="{{ $isOccupied ? 'terisi' : 'kosong' }}">

                                    <span class="absolute inset-x-0 top-0 h-1 {{ $isOccupied ? 'bg-amber-400' : 'bg-emerald-400' }}"></span>

                                    <div class="flex items-center justify-between gap-2">
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                            Meja
                                        </span>
                                        <span class="text-[10px] text-slate-500 font-semibold bg-white/80 border border-slate-200 rounded-md px-1.5 py-0.5">{{ $meja->kapasitas }} Kursi</span>
                                    </div>

                                    <div class="mt-3 flex items-end justify-between gap-2">
                                        <div>
                                            <div class="text-3xl font-black tracking-tight {{ $isOccupied ? 'text-amber-700' : 'text-slate-900' }}">
                                                {{ str_pad($meja->nomor_meja, 2, '0', STR_PAD_LEFT) }}
                                            </div>
                                            <div class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400 mt-0.5">Nomor Meja</div>
                                        </div>
                                        @if($isOccupied)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Terisi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Tersedia
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-3 pt-3 border-t border-dashed {{ $isOccupied ? 'border-amber-200' : 'border-slate-200' }}">
                                        @if($isOccupied)
                                            @if($order)
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="text-xs font-bold text-slate-900">
                                                        Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                                    </div>
                                                    <div class="text-[10px] text-slate-500">
                                                        {{ $order->detail_transaksis->count() }} item
                                                    </div>
                                                </div>
                                                <div class="mt-1.5 flex items-center justify-between">
                                                    <span class="text-[10px] text-slate-500 font-mono">#{{ $order->id }}</span>
                                                    <span class="text-[10px] font-bold text-primary-600">Lihat detail →</span>
                                                </div>
                                            @endif
                                        @else
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-[10px] text-slate-500">Siap menerima pesanan</span>
                                                <span class="text-[10px] font-bold text-primary-600">Kelola →</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- RIGHT: Table Action Terminal Panel (Live Inspector & Kasir) --}}
                <aside class="w-full lg:w-96 bg-white border-t lg:border-t-0 lg:border-l border-slate-200 flex flex-col flex-shrink-0 lg:h-full min-h-[280px] lg:min-h-0 overflow-hidden" id="table-inspector-panel">
                    {{-- Default empty placeholder --}}
                    <div id="inspector-placeholder" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                        <div class="w-20 h-20 rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center mb-4">
                            <svg class="w-9 h-9 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1">Pilih Meja pada Denah</h3>
                        <p class="text-xs text-slate-500 max-w-xs leading-relaxed">Klik salah satu kartu meja untuk melihat rincian pesanan, memproses pembayaran, atau membuat pesanan baru.</p>
                        <div class="mt-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-primary-50 border border-primary-100 text-[10px] font-bold text-primary-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                            Klik kartu meja untuk mulai
                        </div>
                    </div>

                    {{-- Dynamic Inspector Content --}}
                    <div id="inspector-content" class="flex-1 flex flex-col h-full overflow-hidden hidden">
                        {{-- Inspector Header --}}
                        <div class="p-4 border-b border-slate-200 flex items-center justify-between gap-3 bg-primary-50/60 shrink-0">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-white border border-primary-100 text-primary-600 flex items-center justify-center shrink-0 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-bold text-slate-900 text-lg leading-tight" id="inspect-title">Meja 01</h3>
                                        <span id="inspect-badge" class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">Tersedia</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5" id="inspect-subtitle">Kapasitas: 4 Orang</p>
                                </div>
                            </div>
                            <button type="button" onclick="closeInspector()" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-200 transition shrink-0" title="Tutup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        {{-- Inspector Body --}}
                        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50/50" id="inspect-body">
                            {{-- Content injected by JavaScript --}}
                        </div>
                    </div>
                </aside>

            </div>
        </div>

        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
             TAB 3: TRANSAKSI TERBARU — Recent Transactions
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
             TAB 3: PESANAN AKTIF — Active Orders Queue (KDS & POS Terminal)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
        <div id="tab-transaksi" class="lg:h-full flex flex-col lg:overflow-hidden bg-slate-50 hidden">
            {{-- Toolbar: Status Filters, Search, Sync, and Link to History --}}
            <div class="px-3 sm:px-5 py-3 bg-white border-b border-slate-200 flex-shrink-0 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 min-w-0 max-w-full">
                    <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-primary-50 border border-primary-100 text-[10px] font-bold text-primary-700 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary-500 animate-pulse"></span>
                        Aktif
                    </span>
                    <div class="flex items-center gap-1.5 overflow-x-auto hide-scrollbar max-w-full pb-0.5">
                    <button type="button" onclick="filterActiveOrders('all')"
                        class="order-filter-pill active" data-filter="all">
                        Semua Aktif <span id="count-all" class="ml-1.5 bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full text-[10px] font-bold">{{ $pesananAktif->count() }}</span>
                    </button>
                    <button type="button" onclick="filterActiveOrders('pending')"
                        class="order-filter-pill inactive" data-filter="pending">
                        <span class="w-2 h-2 rounded-full bg-amber-500 inline-block mr-1"></span>
                        Menunggu <span id="count-pending" class="ml-1.5 bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full text-[10px] font-bold">{{ $pesananAktif->where('status_pesanan', 'pending')->count() }}</span>
                    </button>
                    <button type="button" onclick="filterActiveOrders('prosesed')"
                        class="order-filter-pill inactive" data-filter="prosesed">
                        <span class="w-2 h-2 rounded-full bg-purple-500 inline-block mr-1"></span>
                        Diproses <span id="count-prosesed" class="ml-1.5 bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full text-[10px] font-bold">{{ $pesananAktif->where('status_pesanan', 'prosesed')->count() }}</span>
                    </button>
                    <button type="button" onclick="filterActiveOrders('ready')"
                        class="order-filter-pill inactive" data-filter="ready">
                        <span class="w-2 h-2 rounded-full bg-blue-500 inline-block mr-1"></span>
                        Siap <span id="count-ready" class="ml-1.5 bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-[10px] font-bold">{{ $pesananAktif->where('status_pesanan', 'ready')->count() }}</span>
                    </button>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative flex-1 min-w-[180px] sm:flex-none sm:w-56 md:w-72">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input id="active-order-search" type="search" placeholder="Cari ID, meja, item..." oninput="searchActiveOrders(this.value)"
                            class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <button type="button" onclick="fetchActiveOrders(true)"
                        id="btn-sync-orders"
                        class="p-2 text-slate-600 hover:text-primary-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition cursor-pointer"
                        title="Sinkronkan Pesanan">
                        <svg id="sync-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>

                    <a href="{{ route('transaksis.index') }}"
                        class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Riwayat</span>
                    </a>
                </div>
            </div>

            {{-- Main Orders Grid Container --}}
            <div class="flex-1 lg:overflow-y-auto p-3 sm:p-5">
                <div id="active-orders-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 gap-4">
                    @forelse ($pesananAktif as $order)
                        @php
                            $isPaid = (bool) $order->pembayaran;
                            $status = $order->status_pesanan;
                            $searchKeywords = strtolower(
                                '#' . $order->id . ' ' .
                                ($order->meja ? 'meja ' . $order->meja->nomor_meja : 'take away bawa pulang') . ' ' .
                                ($order->sumber_pesanan ?? '') . ' ' .
                                $order->detail_transaksis->pluck('produk.nama_produk')->implode(' ') . ' ' .
                                ($order->catatan ?? '')
                            );
                            $statusAccent = match ($status) {
                                'pending' => 'bg-amber-400',
                                'prosesed' => 'bg-purple-400',
                                'ready' => 'bg-blue-400',
                                default => 'bg-slate-300',
                            };
                        @endphp
                        <article class="active-order-card relative overflow-hidden bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col justify-between"
                            id="order-card-{{ $order->id }}"
                            data-order-id="{{ $order->id }}"
                            data-status="{{ $status }}"
                            data-search="{{ $searchKeywords }}">

                            <span class="absolute inset-x-0 top-0 h-1 {{ $statusAccent }}"></span>

                            {{-- Card Header --}}
                            <div class="p-4 border-b border-slate-100">
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="inline-flex items-center justify-center min-w-9 h-9 px-2 rounded-xl bg-primary-50 border border-primary-100 text-primary-700 font-black font-mono text-sm">#{{ $order->id }}</span>
                                        @if ($order->sumber_pesanan === 'web')
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-md text-[10px] font-bold">Web</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-purple-50 text-purple-700 border border-purple-200 rounded-md text-[10px] font-bold">POS</span>
                                        @endif
                                    </div>

                                    {{-- Status Badge --}}
                                    <div class="status-badge-container">
                                        @if($status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                                Menunggu
                                            </span>
                                        @elseif($status === 'prosesed')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                                Diproses
                                            </span>
                                        @elseif($status === 'ready')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                Siap
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-1.5">
                                        @if ($order->meja)
                                            <span class="inline-flex items-center gap-1 font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                                Meja {{ $order->meja->nomor_meja }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 font-bold text-amber-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                                Dibawa Pulang
                                            </span>
                                        @endif
                                    </div>

                                    <div class="text-[11px] text-slate-500 font-medium">
                                        {{ $order->waktu_transaksi?->copy()->timezone('Asia/Jakarta')->format('H:i') }} ({{ $order->waktu_transaksi?->diffForHumans(null, true, true) }})
                                    </div>
                                </div>
                            </div>

                            {{-- Card Body: Item List --}}
                            <div class="p-4 flex-1 overflow-y-auto max-h-48 divide-y divide-slate-100 space-y-2">
                                @foreach ($order->detail_transaksis as $detail)
                                    <div class="pt-2 first:pt-0">
                                        <div class="flex items-start justify-between text-xs gap-2">
                                            <div class="font-bold text-slate-900 leading-snug flex items-start gap-2 min-w-0">
                                                <span class="inline-flex items-center justify-center min-w-6 h-6 px-1.5 rounded-lg bg-primary-50 border border-primary-100 text-primary-700 text-[10px] font-extrabold shrink-0">{{ $detail->QTY ?? $detail->qty }}x</span>
                                                <span class="pt-0.5">{{ $detail->produk->nama_produk ?? 'Menu Dihapus' }}</span>
                                            </div>
                                            <div class="font-semibold text-slate-700 whitespace-nowrap pt-0.5">
                                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                            </div>
                                        </div>
                                        @if ($detail->customization)
                                            <div class="mt-1.5 pl-8">
                                                <span class="inline-block px-1.5 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-semibold italic">
                                                    📝 {{ $detail->customization }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                @if ($order->catatan)
                                    <div class="pt-2 text-[11px] text-slate-600 bg-slate-50 p-2 rounded-lg border border-slate-200 italic">
                                        Catatan: {{ $order->catatan }}
                                    </div>
                                @endif
                            </div>

                            {{-- Card Footer: Pricing & Action Buttons --}}
                            <div class="p-4 bg-slate-50/80 border-t border-slate-100 space-y-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Tagihan</span>
                                        <span class="text-lg font-black text-slate-900 tracking-tight">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="shrink-0">
                                        @if ($isPaid)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                Lunas
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                Belum Bayar
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        {{-- 1-Click Main Progression Button --}}
                                        @if ($status === 'pending')
                                            <button type="button" onclick="quickUpdateStatus({{ $order->id }}, 'prosesed')"
                                                class="flex-1 py-2 px-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /></svg>
                                                Mulai Proses
                                            </button>
                                        @elseif ($status === 'prosesed')
                                            <button type="button" onclick="quickUpdateStatus({{ $order->id }}, 'ready')"
                                                class="flex-1 py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                                Tandai Siap
                                            </button>
                                        @elseif ($status === 'ready')
                                            @if ($isPaid)
                                                <button type="button" onclick="quickUpdateStatus({{ $order->id }}, 'completed')"
                                                    class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                    Selesaikan
                                                </button>
                                            @else
                                                <button type="button" onclick="openQuickPayModal({{ $order->id }}, {{ $order->total_harga }}, '{{ $order->meja ? 'Meja ' . $order->meja->nomor_meja : 'Dibawa Pulang' }}')"
                                                    class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v2a2 2 0 002 2h6a2 2 0 002-2v-1" /></svg>
                                                    Bayar & Selesai
                                                </button>
                                            @endif
                                        @endif

                                        {{-- Extra Pay Button (if unpaid and not yet ready) --}}
                                        @if (!$isPaid && $status !== 'ready')
                                            <button type="button" onclick="openQuickPayModal({{ $order->id }}, {{ $order->total_harga }}, '{{ $order->meja ? 'Meja ' . $order->meja->nomor_meja : 'Dibawa Pulang' }}')"
                                                class="py-2 px-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer"
                                                title="Bayar Langsung">
                                                <span>💵 Bayar</span>
                                            </button>
                                        @endif

                                        {{-- Struk Print Button --}}
                                        <a href="{{ route('transaksis.receipt', $order->id) }}" target="_blank"
                                            class="p-2 bg-white hover:bg-slate-100 text-slate-600 rounded-xl border border-slate-200 text-xs font-bold transition flex items-center justify-center"
                                            title="Cetak Struk">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                        </a>

                                        {{-- Quick Status Dropdown Menu --}}
                                        <select onchange="quickUpdateStatus({{ $order->id }}, this.value)"
                                            class="py-2 px-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary-500 cursor-pointer">
                                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                            <option value="prosesed" {{ $status === 'prosesed' ? 'selected' : '' }}>Diproses</option>
                                            <option value="ready" {{ $status === 'ready' ? 'selected' : '' }}>Siap</option>
                                            <option value="completed">Selesai</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </article>
                    @empty
                        <div id="active-orders-none" class="col-span-full py-16 text-center">
                            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 mb-1">Semua Pesanan Selesai!</h3>
                            <p class="text-xs text-slate-500 mb-5">Tidak ada pesanan aktif saat ini. Pesanan baru akan otomatis muncul di sini.</p>
                            <button type="button" onclick="switchTab('kasir')"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white rounded-xl font-bold text-xs hover:bg-primary-700 transition shadow-md shadow-primary-600/20 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Buat Pesanan Baru
                            </button>
                        </div>
                    @endforelse
                </div>

                <div id="active-orders-search-empty" class="py-16 text-center hidden">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <p class="text-xs font-semibold text-slate-600">Tidak ada pesanan yang cocok dengan pencarian.</p>
                </div>
            </div>
        </div>

    </div>{{-- end flex-1 content area --}}
</div>{{-- end h-screen wrapper --}}

{{-- ═══════════════════════════════════════════════════════════════════
     IN-TERMINAL QUICK PAY MODAL
═══════════════════════════════════════════════════════════════════ --}}
<div id="quick-pay-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-md max-h-[calc(100vh-2rem)] overflow-y-auto transform transition-all">
        <div class="px-4 sm:px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base" id="qp-title">Pembayaran Kasir</h3>
                <p class="text-xs text-slate-500 mt-0.5" id="qp-subtitle">Pesanan # --</p>
            </div>
            <button type="button" onclick="closeQuickPayModal()" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-200 transition" title="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <form id="qp-form" onsubmit="submitQuickPay(event)" class="p-4 sm:p-6 space-y-4">
            <input type="hidden" id="qp-order-id" name="transaksi_id" value="">
            <input type="hidden" id="qp-total-harga" value="0">

            <div class="bg-primary-50 border border-primary-200 rounded-2xl p-4 flex items-center justify-between">
                <span class="text-xs font-bold text-primary-900 uppercase tracking-wider">Total Tagihan</span>
                <span class="text-xl font-black text-primary-700" id="qp-total-display">Rp 0</span>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Metode Pembayaran</label>
                <div class="grid grid-cols-2 gap-2">
                     <label class="flex items-center justify-center gap-2 py-2.5 px-4 border border-slate-200 rounded-xl cursor-pointer has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50 has-[:checked]:text-primary-700 font-bold text-xs transition">
                         <input type="radio" name="metode_pembayaran" value="cash" checked class="hidden" onchange="toggleQpMethod('cash')">
                         <span>💵 Tunai</span>
                     </label>
                     <label class="flex items-center justify-center gap-2 py-2.5 px-4 border border-slate-200 rounded-xl cursor-pointer has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50 has-[:checked]:text-primary-700 font-bold text-xs transition">
                         <input type="radio" name="metode_pembayaran" value="QRIS" class="hidden" onchange="toggleQpMethod('QRIS')">
                         <span>📱 QRIS</span>
                     </label>
                </div>
            </div>

            <div id="qp-cash-group" class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Uang Diterima (Rp)</label>
                    <input type="number" id="qp-cash-input" name="jumlah_bayar" required min="0" oninput="calcQpChange()"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="setQpCash('pas')" class="flex-1 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">Pas</button>
                    <button type="button" onclick="setQpCash(50000)" class="flex-1 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">50k</button>
                    <button type="button" onclick="setQpCash(100000)" class="flex-1 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">100k</button>
                    <button type="button" onclick="setQpCash(200000)" class="flex-1 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">200k</button>
                </div>

                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-xs font-bold">
                    <span class="text-slate-600">Kembalian:</span>
                    <span class="text-sm font-black text-emerald-700" id="qp-change-display">Rp 0</span>
                </div>
            </div>

            <div class="pt-2 flex gap-2">
                <button type="button" onclick="closeQuickPayModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs transition">
                    Batal
                </button>
                <button type="submit" id="qp-submit-btn" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Konfirmasi & Selesaikan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Toast Notification System --}}
<div id="pos-toast" class="fixed bottom-6 right-6 z-50 transform transition-all duration-300 translate-y-20 opacity-0 pointer-events-none bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 border border-slate-700 text-xs font-bold max-w-sm">
    <div id="pos-toast-icon"></div>
    <div id="pos-toast-msg" class="flex-1"></div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     JAVASCRIPT — Tab switching, Table Terminal Inspector, POS cart logic
═══════════════════════════════════════════════════════════════════ --}}
<script>
    // ─────────────────────────────────────────────────────────
    // DATA SERIALIZATION FOR TABLE INSPECTOR & ACTIVE ORDERS
    // ─────────────────────────────────────────────────────────
    const mejaData = @json($mejas->keyBy('id'));
    const productsOptionsData = @json($produksData);
    let activeOrdersData = @json($activeOrders);
    let pesananAktifList = @json($pesananAktif);
    let currentInspectedMejaId = null;
    let currentInspectedOrderId = null;
    let currentTableFilter = 'all';
    const TABLE_CARD_CLASS = {
        terisi: 'terminal-table-card relative overflow-hidden p-4 rounded-2xl border transition-all duration-200 text-left cursor-pointer select-none border-amber-200 bg-amber-50 hover:border-amber-300 shadow-sm',
        kosong: 'terminal-table-card relative overflow-hidden p-4 rounded-2xl border transition-all duration-200 text-left cursor-pointer select-none border-slate-200 bg-white hover:border-primary-300 shadow-sm',
    };
    const csrfToken = '{{ csrf_token() }}';
    const routeStorePembayaran = '{{ route('pembayarans.store') }}';
    const routeUpdateStatus = '{{ url('/transaksis') }}';
    const routeActiveOrders = '{{ route('transaksis.active') }}';
    const statusLabelsMap = {
        'pending': 'Menunggu',
        'ready': 'Siap',
        'prosesed': 'Diproses',
        'completed': 'Selesai'
    };

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    // ─────────────────────────────────────────────────────────
    // TAB SWITCHING (KASIR vs DENAH MEJA vs TRANSAKSI)
    // ─────────────────────────────────────────────────────────
    function switchTab(tabName) {
        const tabs = ['kasir', 'meja', 'transaksi'];
        tabs.forEach(t => {
            const panel = document.getElementById('tab-' + t);
            const btn = document.getElementById('tab-btn-' + t);
            if (t === tabName) {
                panel?.classList.remove('hidden');
                btn?.classList.add('active');
            } else {
                panel?.classList.add('hidden');
                btn?.classList.remove('active');
            }
        });

        // Update sidebar if on page
        const sbMenu = document.querySelector('#sidebar-nav-menu');
        const sbDenah = document.querySelector('#sidebar-nav-denah');
        const sbPesanan = document.querySelector('#sidebar-nav-pesanan');
        if (sbMenu && sbDenah) {
            sbMenu.classList.toggle('active', tabName === 'kasir');
            sbMenu.classList.toggle('inactive', tabName !== 'kasir');
            sbDenah.classList.toggle('active', tabName === 'meja');
            sbDenah.classList.toggle('inactive', tabName !== 'meja');
            if (sbPesanan) {
                sbPesanan.classList.toggle('active', tabName === 'transaksi');
                sbPesanan.classList.toggle('inactive', tabName !== 'transaksi');
            }
        }

        if (history.replaceState) {
            history.replaceState(null, null, '#' + tabName);
        }
    }

    // ─────────────────────────────────────────────────────────
    // FILTER TABLES IN FLOOR PLAN
    // ─────────────────────────────────────────────────────────
    function filterTables(filterType) {
        currentTableFilter = filterType;

        document.querySelectorAll('.table-filter-pill').forEach(pill => {
            if (pill.dataset.filter === filterType) {
                pill.classList.remove('inactive');
                pill.classList.add('active');
            } else {
                pill.classList.remove('active');
                pill.classList.add('inactive');
            }
        });

        document.querySelectorAll('.terminal-table-card').forEach(card => {
            if (filterType === 'all') {
                card.classList.remove('hidden');
            } else if (filterType === 'kosong') {
                card.classList.toggle('hidden', card.dataset.status !== 'kosong');
            } else if (filterType === 'terisi') {
                card.classList.toggle('hidden', card.dataset.status !== 'terisi');
            }
        });
    }

    // ─────────────────────────────────────────────────────────
    // RENDER ULANG KARTU MEJA (STATUS TERISI/KOSONG + RINCIAN)
    // Dipakai setiap sinkronisasi agar status lama tidak tertinggal
    // ketika pesanan sudah selesai.
    // ─────────────────────────────────────────────────────────
    function renderTableCard(mejaId, order) {
        const card = document.getElementById('table-node-' + mejaId);
        const meja = mejaData[mejaId];
        if (!card || !meja) return;

        const isOccupied = !!order;
        const isHighlighted = card.classList.contains('ring-4');
        const isHidden = card.classList.contains('hidden');

        card.className = (isOccupied ? TABLE_CARD_CLASS.terisi : TABLE_CARD_CLASS.kosong)
            + (isHighlighted ? ' ring-4 ring-primary-500' : '')
            + (isHidden ? ' hidden' : '');
        card.dataset.status = isOccupied ? 'terisi' : 'kosong';

        const nomor = String(meja.nomor_meja).padStart(2, '0');
        const header = `
            <span class="absolute inset-x-0 top-0 h-1 ${isOccupied ? 'bg-amber-400' : 'bg-emerald-400'}"></span>
            <div class="flex items-center justify-between gap-2">
                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    Meja
                </span>
                <span class="text-[10px] text-slate-500 font-semibold bg-white/80 border border-slate-200 rounded-md px-1.5 py-0.5">${meja.kapasitas} Kursi</span>
            </div>
            <div class="mt-3 flex items-end justify-between gap-2">
                <div>
                    <div class="text-3xl font-black tracking-tight ${isOccupied ? 'text-amber-700' : 'text-slate-900'}">${nomor}</div>
                    <div class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400 mt-0.5">Nomor Meja</div>
                </div>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold ${isOccupied ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'}">
                    <span class="w-1.5 h-1.5 rounded-full ${isOccupied ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500'}"></span>
                    ${isOccupied ? 'Terisi' : 'Tersedia'}
                </span>
            </div>`;

        if (isOccupied) {
            const itemCount = order.detail_transaksis ? order.detail_transaksis.length : 0;
            card.innerHTML = header + `
                <div class="mt-3 pt-3 border-t border-dashed border-amber-200">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-xs font-bold text-slate-900">
                            Rp ${formatNumber(order.total_harga)}
                        </div>
                        <div class="text-[10px] text-slate-500">
                            ${itemCount} item
                        </div>
                    </div>
                    <div class="mt-1.5 flex items-center justify-between">
                        <span class="text-[10px] text-slate-500 font-mono">#${order.id}</span>
                        <span class="text-[10px] font-bold text-primary-600">Lihat detail →</span>
                    </div>
                </div>`;
        } else {
            card.innerHTML = header + `
                <div class="mt-3 pt-3 border-t border-dashed border-slate-200">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] text-slate-500">Siap menerima pesanan</span>
                        <span class="text-[10px] font-bold text-primary-600">Kelola →</span>
                    </div>
                </div>`;
        }
    }

    // ─────────────────────────────────────────────────────────
    // CLOSE TABLE INSPECTOR
    // ─────────────────────────────────────────────────────────
    function closeInspector() {
        document.querySelectorAll('.terminal-table-card').forEach(c => c.classList.remove('ring-4', 'ring-primary-500'));
        document.getElementById('inspector-content')?.classList.add('hidden');
        document.getElementById('inspector-placeholder')?.classList.remove('hidden');
        currentInspectedMejaId = null;
        currentInspectedOrderId = null;
    }

    // ─────────────────────────────────────────────────────────
    // INSPECT TABLE IN TERMINAL (NO PAGE RELOAD!)
    // ─────────────────────────────────────────────────────────
    function inspectTable(mejaId) {
        document.querySelectorAll('.terminal-table-card').forEach(card => {
            if (card.dataset.mejaId == mejaId) {
                card.classList.add('ring-4', 'ring-primary-500');
            } else {
                card.classList.remove('ring-4', 'ring-primary-500');
            }
        });

        const meja = mejaData[mejaId];
        if (!meja) return;

        const order = activeOrdersData[mejaId];
        const isOccupied = !!order;

        currentInspectedMejaId = mejaId;
        currentInspectedOrderId = order ? order.id : null;

        document.getElementById('inspector-placeholder')?.classList.add('hidden');
        document.getElementById('inspector-content')?.classList.remove('hidden');

        const titleEl = document.getElementById('inspect-title');
        const subtitleEl = document.getElementById('inspect-subtitle');
        const badgeEl = document.getElementById('inspect-badge');
        const bodyEl = document.getElementById('inspect-body');

        if (!titleEl || !subtitleEl || !badgeEl || !bodyEl) return;

        titleEl.textContent = 'Meja ' + String(meja.nomor_meja).padStart(2, '0');
        subtitleEl.textContent = `Kapasitas: ${meja.kapasitas} Kursi`;

        if (!isOccupied) {
            badgeEl.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200';
            badgeEl.innerHTML = '<span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1.5"></span>Tersedia';

            bodyEl.innerHTML = `
                <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-2">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Meja Siap Digunakan</h4>
                    <p class="text-xs text-slate-600">Tidak ada pesanan aktif pada meja ini. Anda dapat langsung membuka pesanan baru.</p>
                </div>

                <div class="pt-2">
                    <button type="button" onclick="selectMejaAndOrder(${meja.id})"
                        class="w-full py-3.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold text-sm shadow-md shadow-primary-600/20 transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Mulai Buat Pesanan untuk Meja ${meja.nomor_meja}
                    </button>
                </div>
            `;
        } else {
            const statusKey = order.status_pesanan || 'pending';
            const statusLabel = statusLabelsMap[statusKey] || statusKey;
            
            badgeEl.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200';
            badgeEl.innerHTML = '<span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>Terisi';

            let itemsHtml = '';
            if (order.detail_transaksis && order.detail_transaksis.length > 0) {
                itemsHtml = order.detail_transaksis.map(item => `
                    <div class="flex items-start justify-between py-2 border-b border-slate-100 text-xs">
                        <div class="flex-1 min-w-0 pr-2">
                            <div class="font-bold text-slate-900">${item.produk?.nama_produk || 'Produk'} <span class="text-primary-600 font-semibold">x${item.QTY || item.qty}</span></div>
                            ${item.customization ? `<div class="text-[10px] text-slate-500 italic mt-0.5">${item.customization}</div>` : ''}
                        </div>
                        <div class="font-bold text-slate-800 shrink-0">
                            Rp ${formatNumber(item.subtotal)}
                        </div>
                    </div>
                `).join('');
            }

            const subtotal = order.detail_transaksis ? order.detail_transaksis.reduce((sum, item) => sum + Number(item.subtotal), 0) : 0;
            const tax = Math.round(order.total_harga - subtotal);

            bodyEl.innerHTML = `
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-semibold">ID Transaksi</span>
                        <span class="font-bold text-slate-900 font-mono">#${order.id}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-semibold">Status Pesanan</span>
                        <span class="font-bold text-amber-700 uppercase tracking-wider text-[10px] bg-amber-100 px-2 py-0.5 rounded border border-amber-200">${statusLabel}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-semibold">Kasir / Pelayan</span>
                        <span class="font-semibold text-slate-800">${order.karyawan?.nama_karyawan || order.sumber_pesanan || '-'}</span>
                    </div>
                </div>

                <div>
                    <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Item Pesanan</h5>
                    <div class="bg-white rounded-xl border border-slate-200 px-3 py-1 max-h-44 overflow-y-auto divide-y divide-slate-100">
                        ${itemsHtml}
                    </div>
                </div>

                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 text-xs space-y-1.5">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span>Rp ${formatNumber(subtotal)}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Pajak & Layanan</span>
                        <span>Rp ${formatNumber(tax > 0 ? tax : 0)}</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-slate-900 pt-1.5 border-t border-slate-200">
                        <span>Total Tagihan</span>
                        <span class="text-primary-700 font-black">Rp ${formatNumber(order.total_harga)}</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-2.5">
                    <form method="POST" action="${routeUpdateStatus}/${order.id}/status" class="flex gap-2">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="PATCH">
                        <select name="status_pesanan" class="flex-1 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-primary-500">
                            <option value="pending" ${order.status_pesanan === 'pending' ? 'selected' : ''}>Menunggu</option>
                            <option value="prosesed" ${order.status_pesanan === 'prosesed' ? 'selected' : ''}>Diproses</option>
                            <option value="ready" ${order.status_pesanan === 'ready' ? 'selected' : ''}>Siap</option>
                            <option value="completed" ${order.status_pesanan === 'completed' ? 'selected' : ''}>Selesai</option>
                        </select>
                        <button type="submit" class="px-3 py-1.5 bg-primary-600 text-white rounded-lg text-xs font-bold hover:bg-primary-700 transition cursor-pointer">
                            Update
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-2xl border-2 border-primary-200 p-3.5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v2a2 2 0 002 2h6a2 2 0 002-2v-1" /></svg>
                            Bayar Tagihan
                        </h5>
                        <span class="text-xs font-bold text-primary-700">Rp ${formatNumber(order.total_harga)}</span>
                    </div>

                    <form method="POST" action="${routeStorePembayaran}" class="space-y-3">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="transaksi_id" value="${order.id}">
                        <input type="hidden" name="redirect_to" value="{{ route('dashboard') }}#meja">

                        <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                                 <label class="flex items-center justify-center gap-1.5 py-2 px-3 border border-slate-200 rounded-xl cursor-pointer has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50 has-[:checked]:text-primary-700 transition">
                                     <input type="radio" name="metode_pembayaran" value="cash" checked class="hidden" onchange="togglePaymentInputs(${order.id}, 'cash', ${order.total_harga})">
                                     <span>💵 Tunai</span>
                                 </label>
                                 <label class="flex items-center justify-center gap-1.5 py-2 px-3 border border-slate-200 rounded-xl cursor-pointer has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50 has-[:checked]:text-primary-700 transition">
                                     <input type="radio" name="metode_pembayaran" value="QRIS" class="hidden" onchange="togglePaymentInputs(${order.id}, 'QRIS', ${order.total_harga})">
                                     <span>📱 QRIS</span>
                                 </label>
                        </div>

                        <div id="cash-input-group-${order.id}">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Uang Diterima (Rp)</label>
                            <input type="number" name="jumlah_bayar" id="payment-input-${order.id}" value="${order.total_harga}" min="${order.total_harga}" required
                                oninput="calcChange(${order.id}, ${order.total_harga})"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            
                            <div class="flex gap-1.5 mt-2">
                                <button type="button" onclick="setCashAmount(${order.id}, ${order.total_harga}, ${order.total_harga})" class="flex-1 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[10px] font-bold cursor-pointer">Pas</button>
                                <button type="button" onclick="setCashAmount(${order.id}, 50000, ${order.total_harga})" class="flex-1 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[10px] font-bold cursor-pointer">50k</button>
                                <button type="button" onclick="setCashAmount(${order.id}, 100000, ${order.total_harga})" class="flex-1 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[10px] font-bold cursor-pointer">100k</button>
                            </div>

                            <div class="flex justify-between items-center text-xs mt-2 pt-2 border-t border-slate-100">
                                <span class="text-slate-500">Kembalian:</span>
                                <strong class="text-emerald-700 font-bold" id="change-text-${order.id}">Rp 0</strong>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Selesaikan Pembayaran
                        </button>
                    </form>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <a href="{{ url('/transaksis') }}/${order.id}/receipt" target="_blank"
                        class="flex-1 py-2 px-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        Cetak Struk
                    </a>
                    <button type="button" onclick="selectMejaAndOrder(${meja.id})"
                        class="flex-1 py-2 px-3 bg-primary-50 hover:bg-primary-100 text-primary-700 rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Tambah Menu
                    </button>
                </div>
            `;
        }
    }

    function togglePaymentInputs(orderId, method, total) {
        const input = document.getElementById('payment-input-' + orderId);
        if (!input) return;
        if (method === 'QRIS') {
            input.value = total;
            calcChange(orderId, total);
        }
    }

    function setCashAmount(orderId, amount, total) {
        const input = document.getElementById('payment-input-' + orderId);
        if (!input) return;
        input.value = Math.max(amount, total);
        calcChange(orderId, total);
    }

    function calcChange(orderId, total) {
        const input = document.getElementById('payment-input-' + orderId);
        const changeText = document.getElementById('change-text-' + orderId);
        if (!input || !changeText) return;
        const val = Number(input.value) || 0;
        const change = Math.max(0, val - total);
        changeText.textContent = `Rp ${formatNumber(change)}`;
    }

    // ─────────────────────────────────────────────────────────
    // SELECT MEJA FROM DENAH & SWITCH TO KASIR TAB
    // ─────────────────────────────────────────────────────────
    function selectMejaAndOrder(mejaId) {
        switchTab('kasir');

        const dineInRadio = document.querySelector('input[name="tipe_pesanan"][value="dine-in"]');
        if (dineInRadio) {
            dineInRadio.checked = true;
            dineInRadio.dispatchEvent(new Event('change', { bubbles: true }));
        }

        const tableSelect = document.querySelector('[data-table-select]');
        if (tableSelect) {
            tableSelect.value = mejaId;
            tableSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    // ─────────────────────────────────────────────────────────
    // LIVE CLOCK & INITIALIZATION
    // ─────────────────────────────────────────────────────────
    (function updateClock() {
        const el = document.getElementById('clock-text');
        if (el) {
            const now = new Date();
            el.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Jakarta' });
        }
        setTimeout(updateClock, 1000);
    })();

    // Check URL hash on load
    window.addEventListener('DOMContentLoaded', () => {
        if (window.location.hash === '#meja') {
            switchTab('meja');
        } else if (window.location.hash === '#transaksi') {
            switchTab('transaksi');
        } else {
            switchTab('kasir');
        }
    });

    // ─────────────────────────────────────────────────────────
    // ACTIVE ORDERS (PESANAN AKTIF) TERMINAL LOGIC
    // ─────────────────────────────────────────────────────────
    let currentFilter = 'all';
    let currentSearch = '';
    let currentQpOrder = null;
    let isFetchingActive = false;

    // Toast Notification System
    function showToast(message, type = 'success') {
        const toast = document.getElementById('pos-toast');
        const icon = document.getElementById('pos-toast-icon');
        const msg = document.getElementById('pos-toast-msg');
        if (!toast || !msg) return;

        msg.textContent = message;
        if (type === 'success') {
            toast.className = 'fixed bottom-6 right-6 z-50 transform transition-all duration-300 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 border border-emerald-500/50 text-xs font-bold max-w-sm';
            icon.innerHTML = '<span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>';
        } else {
            toast.className = 'fixed bottom-6 right-6 z-50 transform transition-all duration-300 bg-red-950 text-white px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 border border-red-500/50 text-xs font-bold max-w-sm';
            icon.innerHTML = '<span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>';
        }

        toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
        clearTimeout(window.__toastTimer);
        window.__toastTimer = setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
        }, 3500);
    }

    // Filter Active Orders by Status
    function filterActiveOrders(filterType) {
        currentFilter = filterType;
        document.querySelectorAll('.order-filter-pill').forEach(pill => {
            if (pill.dataset.filter === filterType) {
                pill.classList.remove('inactive');
                pill.classList.add('active');
            } else {
                pill.classList.remove('active');
                pill.classList.add('inactive');
            }
        });
        applyOrderFilters();
    }

    // Search Active Orders
    function searchActiveOrders(val) {
        currentSearch = (val || '').trim().toLowerCase();
        applyOrderFilters();
    }

    // Apply Filter & Search Visibility
    function applyOrderFilters() {
        const cards = document.querySelectorAll('.active-order-card');
        let visibleCount = 0;
        cards.forEach(card => {
            const statusMatch = (currentFilter === 'all') || (card.dataset.status === currentFilter);
            const searchMatch = !currentSearch || (card.dataset.search && card.dataset.search.includes(currentSearch));
            const visible = statusMatch && searchMatch;
            card.classList.toggle('hidden', !visible);
            if (visible) visibleCount++;
        });

        const emptySearch = document.getElementById('active-orders-search-empty');
        const emptyNone = document.getElementById('active-orders-none');
        if (cards.length > 0) {
            if (emptySearch) emptySearch.classList.toggle('hidden', visibleCount > 0);
            if (emptyNone) emptyNone.classList.add('hidden');
        } else {
            if (emptySearch) emptySearch.classList.add('hidden');
            if (emptyNone) emptyNone.classList.remove('hidden');
        }
    }

    // Quick Update Order Status via AJAX (Instant, No Page Reload)
    async function quickUpdateStatus(orderId, newStatus) {
        try {
            const res = await fetch(`${routeUpdateStatus}/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    _method: 'PATCH',
                    status_pesanan: newStatus
                })
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                showToast(data.message || 'Gagal mengubah status pesanan.', 'error');
                return;
            }

            showToast(`Pesanan #${orderId}: ${statusLabelsMap[newStatus] || newStatus}`);

            if (newStatus === 'completed') {
                const card = document.getElementById(`order-card-${orderId}`);
                if (card) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        card.remove();
                        applyOrderFilters();
                        fetchActiveOrders(false);
                    }, 300);
                }
            } else {
                fetchActiveOrders(false);
            }
        } catch (err) {
            console.error(err);
            showToast('Terjadi gangguan koneksi.', 'error');
        }
    }

    // Quick Pay Modal Handlers
    function openQuickPayModal(orderId, totalHarga, label) {
        currentQpOrder = { id: orderId, total: Number(totalHarga) };
        const modal = document.getElementById('quick-pay-modal');
        if (!modal) return;

        document.getElementById('qp-order-id').value = orderId;
        document.getElementById('qp-total-harga').value = totalHarga;
        document.getElementById('qp-title').textContent = `Pembayaran - Pesanan #${orderId}`;
        document.getElementById('qp-subtitle').textContent = `${label} • Tagihan: Rp ${formatNumber(totalHarga)}`;
        document.getElementById('qp-total-display').textContent = `Rp ${formatNumber(totalHarga)}`;

                                 const cashRadio = document.querySelector('input[name="metode_pembayaran"][value="cash"]');
        if (cashRadio) cashRadio.checked = true;
                                 toggleQpMethod('tunai');

        const cashInput = document.getElementById('qp-cash-input');
        cashInput.value = totalHarga;
        cashInput.min = totalHarga;
        calcQpChange();

        modal.classList.remove('hidden');
    }

    function closeQuickPayModal() {
        document.getElementById('quick-pay-modal')?.classList.add('hidden');
        currentQpOrder = null;
    }

    function toggleQpMethod(method) {
        const cashGroup = document.getElementById('qp-cash-group');
        const cashInput = document.getElementById('qp-cash-input');
        if (!currentQpOrder) return;

        if (method === 'QRIS') {
            if (cashGroup) cashGroup.classList.add('hidden');
            if (cashInput) {
                cashInput.value = currentQpOrder.total;
                cashInput.required = false;
            }
        } else {
            if (cashGroup) cashGroup.classList.remove('hidden');
            if (cashInput) {
                cashInput.required = true;
                calcQpChange();
            }
        }
    }

    function setQpCash(amount) {
        if (!currentQpOrder) return;
        const cashInput = document.getElementById('qp-cash-input');
        if (amount === 'pas') {
            cashInput.value = currentQpOrder.total;
        } else {
            cashInput.value = Math.max(Number(amount), currentQpOrder.total);
        }
        calcQpChange();
    }

    function calcQpChange() {
        if (!currentQpOrder) return;
        const cashInput = document.getElementById('qp-cash-input');
        const changeDisplay = document.getElementById('qp-change-display');
        const val = Number(cashInput.value) || 0;
        const change = Math.max(0, val - currentQpOrder.total);
        changeDisplay.textContent = `Rp ${formatNumber(change)}`;
    }

    async function submitQuickPay(e) {
        e.preventDefault();
        if (!currentQpOrder) return;

        const btn = document.getElementById('qp-submit-btn');
        const form = document.getElementById('qp-form');
        const formData = new FormData(form);

        const initialText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...`;

        try {
            const res = await fetch(routeStorePembayaran, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                showToast(data.message || 'Pembayaran gagal.', 'error');
                btn.disabled = false;
                btn.innerHTML = initialText;
                return;
            }

            const paidId = currentQpOrder.id;
            closeQuickPayModal();
            showToast(`Pesanan #${paidId} lunas! Kembalian: Rp ${formatNumber(data.kembalian)}`);

            if (data.receipt_url) {
                window.open(data.receipt_url, '_blank', 'width=600,height=700');
            }

            const card = document.getElementById(`order-card-${paidId}`);
            if (card) {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.remove();
                    applyOrderFilters();
                    fetchActiveOrders(false);
                }, 300);
            } else {
                fetchActiveOrders(false);
            }
        } catch (err) {
            console.error(err);
            showToast('Terjadi kesalahan jaringan saat pembayaran.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = initialText;
        }
    }

    // Real-Time Background Synchronization (No Page Reload)
    async function fetchActiveOrders(manual = false) {
        if (isFetchingActive) return;
        isFetchingActive = true;

        const syncIcon = document.getElementById('sync-icon');
        if (manual && syncIcon) {
            syncIcon.classList.add('animate-spin');
        }

        try {
            const res = await fetch(routeActiveOrders, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                const countTotal = data.counts.total;
                const badgeEl = document.getElementById('tab-pesanan-badge');
                if (badgeEl) {
                    badgeEl.textContent = countTotal;
                    badgeEl.className = `ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold ${countTotal > 0 ? 'bg-amber-500 text-white animate-pulse' : 'bg-slate-200 text-slate-600'}`;
                }

                const countAllEl = document.getElementById('count-all');
                if (countAllEl) countAllEl.textContent = countTotal;
                const countPendingEl = document.getElementById('count-pending');
                if (countPendingEl) countPendingEl.textContent = data.counts.pending;
                const countProsesedEl = document.getElementById('count-prosesed');
                if (countProsesedEl) countProsesedEl.textContent = data.counts.prosesed;
                const countReadyEl = document.getElementById('count-ready');
                if (countReadyEl) countReadyEl.textContent = data.counts.ready;

                // Sync active tables for floor plan inspector
                const newActiveByMeja = {};
                data.orders.forEach(ord => {
                    if (ord.meja_id) {
                        newActiveByMeja[ord.meja_id] = ord;
                    }
                });
                activeOrdersData = newActiveByMeja;

                // Update floor plan table card badges (isi kartu ikut di-render ulang)
                let terisiCount = 0;
                Object.keys(mejaData).forEach((mejaId) => {
                    const ord = activeOrdersData[mejaId];
                    if (ord) terisiCount++;
                    renderTableCard(mejaId, ord);
                });

                // Update counter Tersedia / Terisi
                const totalMeja = Object.keys(mejaData).length;
                const countKosongEl = document.getElementById('count-kosong-tables');
                const countTerisiEl = document.getElementById('count-terisi-tables');
                if (countKosongEl) countKosongEl.textContent = Math.max(0, totalMeja - terisiCount);
                if (countTerisiEl) countTerisiEl.textContent = terisiCount;

                // Terapkan kembali filter denah (Tersedia / Terisi) sesuai status terbaru
                filterTables(currentTableFilter);

                // Refresh panel inspector bila meja yang sedang dibuka berubah statusnya
                if (currentInspectedMejaId !== null) {
                    const newestOrderId = activeOrdersData[currentInspectedMejaId]
                        ? activeOrdersData[currentInspectedMejaId].id
                        : null;

                    if (newestOrderId !== currentInspectedOrderId) {
                        inspectTable(currentInspectedMejaId);
                    }
                }

                // Re-render order cards if modal is not open
                if (!currentQpOrder) {
                    renderActiveOrderCards(data.orders);
                }

                if (manual) {
                    showToast('Pesanan berhasil disinkronkan.');
                }
            }
        } catch (err) {
            console.error('Failed to sync active orders', err);
        } finally {
            isFetchingActive = false;
            if (manual && syncIcon) {
                setTimeout(() => syncIcon.classList.remove('animate-spin'), 600);
            }
        }
    }

    function renderActiveOrderCards(orders) {
        const grid = document.getElementById('active-orders-grid');
        if (!grid) return;

        if (!orders || orders.length === 0) {
            grid.innerHTML = `
                <div id="active-orders-none" class="col-span-full py-16 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1">Semua Pesanan Selesai!</h3>
                    <p class="text-xs text-slate-500 mb-5">Tidak ada pesanan aktif saat ini. Pesanan baru akan otomatis muncul di sini.</p>
                    <button type="button" onclick="switchTab('kasir')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white rounded-xl font-bold text-xs hover:bg-primary-700 transition shadow-md shadow-primary-600/20 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Buat Pesanan Baru
                    </button>
                </div>
            `;
            applyOrderFilters();
            return;
        }

        grid.innerHTML = orders.map(order => {
            const isPaid = !!order.pembayaran;
            const status = order.status_pesanan;
            const statusAccent = {
                pending: 'bg-amber-400',
                prosesed: 'bg-purple-400',
                ready: 'bg-blue-400',
            }[status] || 'bg-slate-300';
            const tableLabel = order.meja ? `Meja ${order.meja.nomor_meja}` : 'Dibawa Pulang';
            const sourceBadge = order.sumber_pesanan === 'web'
                ? '<span class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-md text-[10px] font-bold">Web</span>'
                : '<span class="px-2 py-0.5 bg-purple-50 text-purple-700 border border-purple-200 rounded-md text-[10px] font-bold">POS</span>';

            let statusBadge = '';
            if (status === 'pending') {
                statusBadge = `
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                        Menunggu
                    </span>
                `;
            } else if (status === 'prosesed') {
                statusBadge = `
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                        Diproses
                    </span>
                `;
            } else if (status === 'ready') {
                statusBadge = `
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Siap
                    </span>
                `;
            }

            const typeBadge = order.meja
                ? `<span class="inline-flex items-center gap-1 font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">
                     <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                     Meja ${order.meja.nomor_meja}
                   </span>`
                : `<span class="inline-flex items-center gap-1 font-bold text-amber-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md">
                     <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                     Dibawa Pulang
                   </span>`;

            let itemsHtml = '';
            let searchItems = '';
            if (order.detail_transaksis) {
                itemsHtml = order.detail_transaksis.map(d => {
                    const prodName = d.produk ? d.produk.nama_produk : 'Menu';
                    searchItems += ' ' + prodName;
                    return `
                        <div class="pt-2 first:pt-0">
                            <div class="flex items-start justify-between text-xs gap-2">
                                <div class="font-bold text-slate-900 leading-snug flex items-start gap-2 min-w-0">
                                    <span class="inline-flex items-center justify-center min-w-6 h-6 px-1.5 rounded-lg bg-primary-50 border border-primary-100 text-primary-700 text-[10px] font-extrabold shrink-0">${d.QTY || d.qty}x</span>
                                    <span class="pt-0.5">${prodName}</span>
                                </div>
                                <div class="font-semibold text-slate-700 whitespace-nowrap pt-0.5">
                                    Rp ${formatNumber(d.subtotal)}
                                </div>
                            </div>
                            ${d.customization ? `
                                <div class="mt-1.5 pl-8">
                                    <span class="inline-block px-1.5 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-semibold italic">
                                        📝 ${d.customization}
                                    </span>
                                </div>
                            ` : ''}
                        </div>
                    `;
                }).join('');
            }

            const notesHtml = order.catatan ? `
                <div class="pt-2 text-[11px] text-slate-600 bg-slate-50 p-2 rounded-lg border border-slate-200 italic">
                    Catatan: ${order.catatan}
                </div>
            ` : '';

            const paidBadge = isPaid
                ? `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                     <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                     Lunas
                   </span>`
                : `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                     <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                     Belum Bayar
                   </span>`;

            let mainActionBtn = '';
            if (status === 'pending') {
                mainActionBtn = `
                    <button type="button" onclick="quickUpdateStatus(${order.id}, 'prosesed')"
                        class="flex-1 py-2 px-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /></svg>
                        Mulai Proses
                    </button>
                `;
            } else if (status === 'prosesed') {
                mainActionBtn = `
                    <button type="button" onclick="quickUpdateStatus(${order.id}, 'ready')"
                        class="flex-1 py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        Tandai Siap
                    </button>
                `;
            } else if (status === 'ready') {
                if (isPaid) {
                    mainActionBtn = `
                        <button type="button" onclick="quickUpdateStatus(${order.id}, 'completed')"
                            class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Selesaikan
                        </button>
                    `;
                } else {
                    mainActionBtn = `
                        <button type="button" onclick="openQuickPayModal(${order.id}, ${order.total_harga}, '${tableLabel}')"
                            class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v2a2 2 0 002 2h6a2 2 0 002-2v-1" /></svg>
                            Bayar & Selesai
                        </button>
                    `;
                }
            }

            const payBtnExtra = (!isPaid && status !== 'ready')
                ? `<button type="button" onclick="openQuickPayModal(${order.id}, ${order.total_harga}, '${tableLabel}')"
                     class="py-2 px-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer"
                     title="Bayar Langsung">
                     <span>💵 Bayar</span>
                   </button>`
                : '';

            const timeStr = order.waktu_transaksi ? new Date(order.waktu_transaksi).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta' }) : '-';
            const searchKeywords = `#${order.id} ${tableLabel.toLowerCase()} ${(order.sumber_pesanan || '')} ${searchItems} ${(order.catatan || '')}`.toLowerCase();

            return `
                <article class="active-order-card relative overflow-hidden bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col justify-between"
                    id="order-card-${order.id}"
                    data-order-id="${order.id}"
                    data-status="${status}"
                    data-search="${searchKeywords}">

                    <span class="absolute inset-x-0 top-0 h-1 ${statusAccent}"></span>

                    <div class="p-4 border-b border-slate-100">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="inline-flex items-center justify-center min-w-9 h-9 px-2 rounded-xl bg-primary-50 border border-primary-100 text-primary-700 font-black font-mono text-sm">#${order.id}</span>
                                ${sourceBadge}
                            </div>
                            <div class="status-badge-container">
                                ${statusBadge}
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5">
                                ${typeBadge}
                            </div>
                            <div class="text-[11px] text-slate-500 font-medium">
                                ${timeStr}
                            </div>
                        </div>
                    </div>

                    <div class="p-4 flex-1 overflow-y-auto max-h-48 divide-y divide-slate-100 space-y-2">
                        ${itemsHtml}
                        ${notesHtml}
                    </div>

                    <div class="p-4 bg-slate-50/80 border-t border-slate-100 space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Tagihan</span>
                                <span class="text-lg font-black text-slate-900 tracking-tight">Rp ${formatNumber(order.total_harga)}</span>
                            </div>
                            <div class="shrink-0">
                                ${paidBadge}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                ${mainActionBtn}
                                ${payBtnExtra}

                                <a href="${routeUpdateStatus}/${order.id}/receipt" target="_blank"
                                    class="p-2 bg-white hover:bg-slate-100 text-slate-600 rounded-xl border border-slate-200 text-xs font-bold transition flex items-center justify-center"
                                    title="Cetak Struk">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                </a>

                                <select onchange="quickUpdateStatus(${order.id}, this.value)"
                                    class="py-2 px-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary-500 cursor-pointer">
                                    <option value="pending" ${status === 'pending' ? 'selected' : ''}>Menunggu</option>
                                    <option value="prosesed" ${status === 'prosesed' ? 'selected' : ''}>Diproses</option>
                                    <option value="ready" ${status === 'ready' ? 'selected' : ''}>Siap</option>
                                    <option value="completed">Selesai</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </article>
            `;
        }).join('');

        applyOrderFilters();
    }

    // Auto-polling every 15 seconds (seamless background update, no page reload)
    setInterval(function() {
        fetchActiveOrders(false);
    }, 15000);

    // ─────────────────────────────────────────────────────────
    // POS CART LOGIC (full implementation)
    // ─────────────────────────────────────────────────────────
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
        const cart = new Map();
        let activeCategory = 'all';
        const currency = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

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
                
                const options = productsOptionsData[product.id]?.opsis || [];
                const extraPrice = options.reduce((sum, opt) => {
                    return (product.customization || '').includes(opt.nama) ? sum + Number(opt.harga) : sum;
                }, 0);
                
                const lineTotal = (Number(product.price) + extraPrice) * product.qty;
                subtotal += lineTotal;

                const line = document.createElement('div');
                line.className = 'pos-cart-item';

                const detail = document.createElement('div');
                detail.className = 'flex-1 min-w-0';
                const name = document.createElement('div');
                name.className = 'font-bold text-slate-900 text-sm truncate';
                name.textContent = product.name;
                const price = document.createElement('div');
                price.className = 'text-xs text-slate-500 mt-0.5';
                                price.textContent = currency.format(lineTotal);


                detail.append(name, price);

                 const customizationLabel = document.createElement('div');
                 customizationLabel.className = 'pos-cart-customization mt-2 pt-2 border-t border-slate-200/80';
                 const customTitle = document.createElement('div');
                 customTitle.className = 'text-[10px] font-bold text-slate-500 uppercase mb-1.5';
                 customTitle.textContent = 'Kustomisasi';
                 customizationLabel.appendChild(customTitle);

                  const presetsContainer = document.createElement('div');
                  presetsContainer.className = 'grid grid-cols-2 gap-1 mb-2';


                 options.forEach(option => {
                     const label = document.createElement('label');
                     label.className = 'pos-customization-preset';
                     const optText = option.harga > 0 ? `${option.nama} (+Rp ${formatNumber(option.harga)})` : option.nama;

                     const input = document.createElement('input');
                     input.type = 'checkbox';
                     input.dataset.option = option.nama;
                     input.checked = (product.customization || '').includes(option.nama);

                     const text = document.createTextNode(optText);
                     label.append(input, text);

                     presetsContainer.append(label);
                 });


                const customization = document.createElement('textarea');
                customization.className = 'w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs resize-none focus:outline-none focus:ring-1 focus:ring-primary-500';
                customization.maxLength = 250;
                customization.rows = 2;
                customization.placeholder = 'Catatan tambahan...';

                 const currentCustom = product.customization || '';
                 const optionNames = options.map(o => o.nama);
                 const noteOnly = currentCustom.split(', ').filter(p => !optionNames.includes(p)).join(', ');
                 customization.value = noteOnly;

                 const customizationInput = document.createElement('input');
                 customizationInput.type = 'hidden';
                 customizationInput.name = `items[${index}][customization]`;
                 customizationInput.value = product.customization || '';

                  const updateCustomization = () => {
                      const checked = [];
                      presetsContainer.querySelectorAll('input:checked').forEach((cb) => {
                         if (cb.dataset.option) checked.push(cb.dataset.option);
                     });
                     const note = customization.value.trim();
                     product.customization = [checked.join(', '), note].filter(Boolean).join(', ');
                     customizationInput.value = product.customization;
                     renderCart();
                 };

                 const updateNote = () => {
                     const checked = [];
                     presetsContainer.querySelectorAll('input:checked').forEach((cb) => {
                         if (cb.dataset.option) checked.push(cb.dataset.option);
                     });
                     const note = customization.value.trim();
                     product.customization = [checked.join(', '), note].filter(Boolean).join(', ');
                     customizationInput.value = product.customization;
                 };


                presetsContainer.querySelectorAll('input').forEach(cb => {
                    cb.addEventListener('change', updateCustomization);
                });
                customization.addEventListener('input', updateNote);

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
                remove.className = 'pos-cart-remove';
                remove.dataset.cartAction = 'remove';
                remove.dataset.id = product.id;
                remove.textContent = 'Hapus item';

                line.append(detail, controls);
                const lineWrapper = document.createElement('div');
                lineWrapper.className = 'pos-cart-line';
                lineWrapper.append(line, customizationLabel, remove);
                cartLines.append(lineWrapper);

                const productId = document.createElement('input');
                productId.type = 'hidden';
                productId.name = `items[${index}][produk_id]`;
                productId.value = product.id;
                const productQty = document.createElement('input');
                productQty.type = 'hidden';
                productQty.name = `items[${index}][qty]`;
                productQty.value = product.qty;
                cartInputs.append(productId, productQty, customizationInput);
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
                search.dispatchEvent(new Event('input'));
            });
        });

        function syncOrderType() {
            const dineIn = selectedOrderType() === 'dine-in';
            tableSelect.disabled = !dineIn;
            tableSelect.required = dineIn;
            takeawayHint.hidden = dineIn;
            renderCart();
        }

        orderTypeInputs.forEach((input) => input.addEventListener('change', syncOrderType));
        tableSelect.addEventListener('change', renderCart);
        form.addEventListener('submit', (event) => {
            const dineIn = selectedOrderType() === 'dine-in';
            if (cart.size === 0 || (dineIn && !tableSelect.value)) {
                event.preventDefault();
                return;
            }
            checkout.disabled = true;
            checkout.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan pesanan...`;
        });

        syncOrderType();
    })();
</script>
@endsection