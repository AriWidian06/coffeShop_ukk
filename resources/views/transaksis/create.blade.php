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
                    <p class="text-sm text-slate-500">Pilih menu, atur kustomisasi, lalu simpan pesanan.</p>
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

    <!-- PRODUCT CUSTOMIZATION MODAL (Gaya Web Pelanggan) -->
    <div id="product-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full mx-4 overflow-hidden border border-slate-200 flex flex-col md:flex-row max-h-[90vh]">
            <!-- Left: Image -->
            <div class="md:w-1/2 bg-slate-100 relative min-h-[220px] md:min-h-full">
                <img id="modal-product-img" src="" alt="" class="w-full h-full object-cover hidden">
                <div id="modal-product-placeholder" class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-primary-200 text-primary-400">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
                    </svg>
                </div>
            </div>
            <!-- Right: Details & Options -->
            <div class="md:w-1/2 p-6 flex flex-col justify-between overflow-y-auto">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span id="modal-product-category" class="text-xs font-bold text-primary-600 uppercase tracking-wider">Kategori</span>
                        <button type="button" id="modal-close-btn" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold">✕</button>
                    </div>
                    <h3 id="modal-product-name" class="text-xl font-bold text-slate-900 mb-1">Nama Produk</h3>
                    <p id="modal-product-desc" class="text-xs text-slate-500 mb-4 line-clamp-2">Deskripsi produk...</p>
                    
                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-100">
                        <span id="modal-product-price" class="text-lg font-black text-primary-600">Rp 0</span>
                        <span id="modal-product-stock" class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded">Stok: 0</span>
                    </div>

                    <!-- Dynamic Options -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Penyesuaian / Opsi Menu</label>
                        <div id="modal-options-container" class="grid grid-cols-2 gap-2">
                            <!-- Injected via JavaScript -->
                        </div>
                    </div>

                    <!-- Custom Notes -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan tambahan (opsional)</label>
                        <textarea id="modal-product-notes" rows="2" placeholder="Tulis permintaan lain untuk kasir..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                    </div>
                </div>

                <!-- Quantity & Add to Cart -->
                <div class="pt-4 border-t border-slate-100 flex items-center gap-3">
                    <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                        <button type="button" id="modal-qty-minus" class="px-3 py-2 text-slate-600 hover:bg-slate-200 font-bold">−</button>
                        <span id="modal-qty-val" class="w-8 text-center font-bold text-sm text-slate-900">1</span>
                        <button type="button" id="modal-qty-plus" class="px-3 py-2 text-slate-600 hover:bg-slate-200 font-bold">+</button>
                    </div>
                    <button type="button" id="modal-add-btn" class="flex-1 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold text-sm shadow-md shadow-primary-600/20 transition flex items-center justify-center gap-2">
                        Tambah ke keranjang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- SECTION KIRI: KATALOG MENU & DENAH MEJA -->
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
                                <article class="pos-product-card bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between hover:border-primary-400 hover:shadow-md transition cursor-pointer" 
                                         data-pos-product 
                                         data-search="{{ strtolower($produk->nama_produk . ' ' . ($produk->kategoriProduk->nama_kategori ?? '')) }}" 
                                         data-category="{{ strtolower($produk->kategoriProduk->nama_kategori ?? '') }}"
                                         data-id="{{ $produk->id }}"
                                         data-name="{{ $produk->nama_produk }}"
                                         data-price="{{ (float) $produk->harga_jual }}"
                                         data-stock="{{ $produk->stock }}"
                                         data-desc="{{ $produk->deskripsi ?? 'Tidak ada deskripsi.' }}"
                                         data-category-name="{{ $produk->kategoriProduk->nama_kategori ?? 'Menu' }}"
                                         data-image="{{ $produk->gambar ? asset('storage/' . $produk->gambar) : '' }}"
                                         data-options="{{ json_encode($produk->opsis ?? []) }}">
                                    
                                    <div>
                                        @if($produk->gambar)
                                            <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama_produk }}" class="w-full h-32 object-cover border-b border-slate-100">
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
                                        </div>
                                    </div>
                                    <div class="p-3 pt-0 flex items-center justify-between border-t border-slate-100 mt-2">
                                        <span class="font-bold text-primary-600 text-sm">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
                                        <span class="text-xs font-semibold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-lg">Pilih & kustomisasi</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        <p class="pos-no-results hidden text-center py-12 text-slate-500" data-pos-no-results>Menu tidak ditemukan. Coba kata pencarian lain.</p>
                    @endif
                </div>

                <!-- 2. VIEW DENAH MEJA -->
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

            <!-- PANEL ORDER / KERANJANG -->
            <aside class="pos-order-panel bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col h-fit sticky top-24" id="pos-order" aria-labelledby="pos-order-title">
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
                <div class="pos-cart-lines max-h-80 overflow-y-auto p-4 space-y-3" data-cart-lines aria-live="polite"></div>

                <form class="pos-order-form p-4 border-t border-slate-200" id="pos-form" method="post" action="{{ route('transaksis.store') }}">
                    @csrf
                    <div data-cart-inputs></div>

                    <!-- Tipe Pesanan -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Tipe Pesanan</label>
                        <div class="pos-type-selector grid grid-cols-2 gap-2">
                            <label class="flex items-center justify-center p-2.5 rounded-xl border border-slate-200 text-xs font-bold cursor-pointer bg-white hover:bg-slate-50">
                                <input type="radio" name="tipe_pesanan" value="dine-in" checked class="mr-1.5">
                                <span>🍽️ Makan di Tempat</span>
                            </label>
                            <label class="flex items-center justify-center p-2.5 rounded-xl border border-slate-200 text-xs font-bold cursor-pointer bg-white hover:bg-slate-50">
                                <input type="radio" name="tipe_pesanan" value="take-away" class="mr-1.5">
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
                        <p class="text-xs text-slate-500 mt-1.5" data-takeaway-hint hidden>Pesanan bawa pulang tidak memerlukan meja.</p>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Metode Pembayaran <span class="text-red-500">*</span></label>
                        <select name="metode_pembayaran" id="metode_pembayaran" required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="tunai">💵 Tunai (Cash)</option>
                            <option value="qris">📱 QRIS (Scan Otomatis)</option>
                        </select>
                    </div>

                    <!-- Catatan Umum -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Catatan Transaksi (Opsional)</label>
                        <textarea name="catatan" rows="2" placeholder="Catatan untuk seluruh pesanan..." class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
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

                    <button class="w-full mt-4 px-4 py-3 bg-primary-600 text-white rounded-xl font-bold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2" type="submit" data-checkout disabled>
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
        const productsContainer = document.querySelector('[data-pos-products]');
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

        // Modal Elements
        const productModal = document.querySelector('#product-modal');
        const modalCloseBtn = document.querySelector('#modal-close-btn');
        const modalProductImg = document.querySelector('#modal-product-img');
        const modalProductPlaceholder = document.querySelector('#modal-product-placeholder');
        const modalProductCategory = document.querySelector('#modal-product-category');
        const modalProductName = document.querySelector('#modal-product-name');
        const modalProductDesc = document.querySelector('#modal-product-desc');
        const modalProductPrice = document.querySelector('#modal-product-price');
        const modalProductStock = document.querySelector('#modal-product-stock');
        const modalOptionsContainer = document.querySelector('#modal-options-container');
        const modalProductNotes = document.querySelector('#modal-product-notes');
        const modalQtyVal = document.querySelector('#modal-qty-val');
        const modalQtyMinus = document.querySelector('#modal-qty-minus');
        const modalQtyPlus = document.querySelector('#modal-qty-plus');
        const modalAddBtn = document.querySelector('#modal-add-btn');

        // View Tabs Elements
        const viewMenu = document.querySelector('#pos-view-menu');
        const viewDenah = document.querySelector('#pos-view-denah');
        const tabBtnMenu = document.querySelector('#tab-btn-menu');
        const tabBtnDenah = document.querySelector('#tab-btn-denah');
        const denahLegendBar = document.querySelector('#denah-legend-bar');

        const cart = new Map();
        let activeCategory = 'all';
        let currentModalProduct = null;
        let modalQty = 1;

        const currency = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

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

        // SPA VIEW SWITCHER
        window.switchPosView = function(viewName) {
            if (viewName === 'denah') {
                viewMenu?.classList.add('hidden');
                viewDenah?.classList.remove('hidden');
                denahLegendBar?.classList.remove('hidden');
                denahLegendBar?.classList.add('flex');

                tabBtnMenu?.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
                tabBtnMenu?.classList.add('text-slate-600', 'hover:text-slate-900');
                tabBtnDenah?.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
                tabBtnDenah?.classList.remove('text-slate-600', 'hover:text-slate-900');

                history.replaceState?.(null, null, '#denah');
            } else {
                viewMenu?.classList.remove('hidden');
                viewDenah?.classList.add('hidden');
                denahLegendBar?.classList.add('hidden');
                denahLegendBar?.classList.remove('flex');

                tabBtnMenu?.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
                tabBtnMenu?.classList.remove('text-slate-600', 'hover:text-slate-900');
                tabBtnDenah?.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
                tabBtnDenah?.classList.add('text-slate-600', 'hover:text-slate-900');

                history.replaceState?.(null, null, '#menu');
            }
        };

        window.selectTableFromDenah = function(mejaId, mejaNomor) {
            if (tableSelect) tableSelect.value = String(mejaId);
            const dineInRadio = document.querySelector('input[name="tipe_pesanan"][value="dine-in"]');
            if (dineInRadio) {
                dineInRadio.checked = true;
                syncOrderType();
            }
            updateTableSelectionUI(mejaId, mejaNomor);
            window.switchPosView('menu');
            showToast(`Meja ${mejaNomor} terpilih untuk pesanan ini!`);
        };

        function updateTableSelectionUI(mejaId, mejaNomor) {
            if (!mejaId) {
                selectedTableIndicator?.classList.add('hidden');
                document.querySelectorAll('.pos-table-card').forEach(card => card.classList.remove('ring-4', 'ring-primary-400'));
                return;
            }
            if (selectedTableIndicator && selectedTableText) {
                selectedTableText.textContent = `Meja ${mejaNomor || mejaId} Terpilih`;
                selectedTableIndicator.classList.remove('hidden');
            }
            document.querySelectorAll('.pos-table-card').forEach(card => {
                if (card.dataset.mejaId === String(mejaId)) {
                    card.classList.add('ring-4', 'ring-primary-400');
                } else {
                    card.classList.remove('ring-4', 'ring-primary-400');
                }
            });
        }

        // ==========================================
        // MODAL KUSTOMISASI
        // ==========================================
        function openProductModal(card) {
            currentModalProduct = {
                id: card.dataset.id,
                name: card.dataset.name,
                price: Number(card.dataset.price),
                stock: Number(card.dataset.stock),
                desc: card.dataset.desc,
                category: card.dataset.categoryName,
                image: card.dataset.image,
                options: JSON.parse(card.dataset.options || '[]')
            };

            modalProductName.textContent = currentModalProduct.name;
            modalProductCategory.textContent = currentModalProduct.category;
            modalProductDesc.textContent = currentModalProduct.desc;
            modalProductPrice.textContent = currency.format(currentModalProduct.price);
            modalProductStock.textContent = `Stok ${currentModalProduct.stock}`;
            modalProductNotes.value = '';
            modalQty = 1;
            modalQtyVal.textContent = modalQty;

            if (currentModalProduct.image) {
                modalProductImg.src = currentModalProduct.image;
                modalProductImg.classList.remove('hidden');
                modalProductPlaceholder.classList.add('hidden');
            } else {
                modalProductImg.classList.add('hidden');
                modalProductPlaceholder.classList.remove('hidden');
            }

            // Render options from DB
            modalOptionsContainer.replaceChildren();
            if (currentModalProduct.options.length > 0) {
                currentModalProduct.options.forEach(opt => {
                    const label = document.createElement('label');
                    label.className = 'text-xs flex items-center justify-between p-2.5 rounded-xl border border-slate-200 cursor-pointer bg-slate-50 hover:bg-slate-100 transition';

                    const leftDiv = document.createElement('div');
                    leftDiv.className = 'flex items-center gap-2';

                    const input = document.createElement('input');
                    input.type = 'checkbox';
                    input.value = opt.nama_opsi;
                    input.dataset.extra = opt.harga_tambahan || 0;
                    input.className = 'rounded text-primary-600 focus:ring-primary-500';

                    const span = document.createElement('span');
                    span.className = 'font-semibold text-slate-800';
                    span.textContent = opt.nama_opsi;

                    leftDiv.append(input, span);

                    const priceSpan = document.createElement('span');
                    priceSpan.className = 'text-[11px] font-bold text-primary-600';
                    priceSpan.textContent = Number(opt.harga_tambahan) > 0 ? `+${currency.format(opt.harga_tambahan)}` : 'Gratis';

                    label.append(leftDiv, priceSpan);
                    modalOptionsContainer.append(label);
                });
            } else {
                modalOptionsContainer.innerHTML = '<span class="text-xs text-slate-400 italic col-span-2">Tidak ada opsi kustomisasi untuk menu ini.</span>';
            }

            modalAddBtn.disabled = currentModalProduct.stock < 1;
            productModal.classList.remove('hidden');
        }

        function closeProductModal() {
            productModal.classList.add('hidden');
            currentModalProduct = null;
        }

        modalCloseBtn?.addEventListener('click', closeProductModal);
        productModal?.addEventListener('click', (e) => {
            if (e.target === productModal) closeProductModal();
        });

        modalQtyMinus?.addEventListener('click', () => {
            if (modalQty > 1) {
                modalQty -= 1;
                modalQtyVal.textContent = modalQty;
            }
        });

        modalQtyPlus?.addEventListener('click', () => {
            if (currentModalProduct && modalQty < currentModalProduct.stock) {
                modalQty += 1;
                modalQtyVal.textContent = modalQty;
            }
        });

        modalAddBtn?.addEventListener('click', () => {
            if (!currentModalProduct) return;

            const selectedOpts = [];
            let extraPrice = 0;
            modalOptionsContainer.querySelectorAll('input:checked').forEach(cb => {
                selectedOpts.push(cb.value);
                extraPrice += Number(cb.dataset.extra || 0);
            });

            const note = modalProductNotes.value.trim();
            const customization = [...selectedOpts, note].filter(Boolean).join(', ');
            const finalUnitPrice = currentModalProduct.price + extraPrice;

            // Unique cart key based on product id + customization to handle different options of the same item
            const cartKey = `${currentModalProduct.id}_${customization}`;

            const existingItem = cart.get(cartKey) || {
                id: currentModalProduct.id,
                name: currentModalProduct.name,
                basePrice: currentModalProduct.price,
                price: finalUnitPrice,
                stock: currentModalProduct.stock,
                qty: 0,
                customization: customization
            };

            existingItem.qty = Math.min(existingItem.qty + modalQty, existingItem.stock);
            cart.set(cartKey, existingItem);

            renderCart();
            showToast(`${currentModalProduct.name} ditambahkan ke keranjang`);
            closeProductModal();
        });

        // ==========================================
        // RENDER CART
        // ==========================================
        function renderCart() {
            cartLines.replaceChildren();
            cartInputs.replaceChildren();
            let itemCount = 0;
            let subtotal = 0;
            let index = 0;

            for (const [key, item] of cart.entries()) {
                itemCount += item.qty;
                subtotal += item.price * item.qty;

                const line = document.createElement('div');
                line.className = 'p-3 bg-slate-50 rounded-xl border border-slate-200 flex flex-col gap-2';

                const topRow = document.createElement('div');
                topRow.className = 'flex items-start justify-between gap-2';

                const info = document.createElement('div');
                info.className = 'flex-1 min-w-0';
                const name = document.createElement('div');
                name.className = 'font-bold text-slate-900 text-sm truncate';
                name.textContent = item.name;
                
                const price = document.createElement('div');
                price.className = 'text-xs font-semibold text-primary-600 mt-0.5';
                price.textContent = currency.format(item.price * item.qty);
                info.append(name, price);

                if (item.customization) {
                    const badge = document.createElement('div');
                    badge.className = 'text-[11px] text-slate-600 bg-white px-2 py-1 rounded border border-slate-200 mt-1.5 italic';
                    badge.textContent = `Opsi/Catatan: ${item.customization}`;
                    info.appendChild(badge);
                }

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'text-slate-400 hover:text-red-600 text-xs font-bold';
                removeBtn.textContent = '✕';
                removeBtn.onclick = () => {
                    cart.delete(key);
                    renderCart();
                };

                topRow.append(info, removeBtn);

                const bottomRow = document.createElement('div');
                bottomRow.className = 'flex items-center justify-between pt-2 border-t border-slate-200';

                const qtyControls = document.createElement('div');
                qtyControls.className = 'flex items-center gap-2';

                const minusBtn = document.createElement('button');
                minusBtn.type = 'button';
                minusBtn.className = 'w-6 h-6 rounded bg-white border border-slate-300 font-bold text-xs flex items-center justify-center hover:bg-slate-100';
                minusBtn.textContent = '−';
                minusBtn.onclick = () => {
                    if (item.qty > 1) {
                        item.qty -= 1;
                    } else {
                        cart.delete(key);
                    }
                    renderCart();
                };

                const qtySpan = document.createElement('span');
                qtySpan.className = 'text-xs font-bold text-slate-800';
                qtySpan.textContent = `${item.qty}x`;

                const plusBtn = document.createElement('button');
                plusBtn.type = 'button';
                plusBtn.className = 'w-6 h-6 rounded bg-white border border-slate-300 font-bold text-xs flex items-center justify-center hover:bg-slate-100';
                plusBtn.textContent = '+';
                plusBtn.onclick = () => {
                    if (item.qty < item.stock) {
                        item.qty += 1;
                        renderCart();
                    }
                };

                qtyControls.append(minusBtn, qtySpan, plusBtn);
                bottomRow.appendChild(qtyControls);

                line.append(topRow, bottomRow);
                cartLines.appendChild(line);

                // Hidden Inputs for Form Submission
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = `items[${index}][produk_id]`;
                idInput.value = item.id;

                const qtyInput = document.createElement('input');
                qtyInput.type = 'hidden';
                qtyInput.name = `items[${index}][qty]`;
                qtyInput.value = item.qty;

                const customInput = document.createElement('input');
                customInput.type = 'hidden';
                customInput.name = `items[${index}][customization]`;
                customInput.value = item.customization || '';

                cartInputs.append(idInput, qtyInput, customInput);
                index += 1;
            }

            cartEmpty.hidden = cart.size > 0;
            cartCount.textContent = `${itemCount} item`;
            const tax = Math.round(subtotal * 0.1);
            cartSubtotal.textContent = currency.format(subtotal);
            cartTax.textContent = currency.format(tax);
            cartTotal.textContent = currency.format(subtotal + tax);
            
            const dineIn = form.querySelector('input[name="tipe_pesanan"]:checked').value === 'dine-in';
            checkout.disabled = cart.size === 0 || (dineIn && !tableSelect.value);
        }

        // Product Card Click Listener to Open Modal
        productsContainer?.addEventListener('click', (e) => {
            const card = e.target.closest('[data-pos-product]');
            if (!card) return;
            openProductModal(card);
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
                categoryTabs.forEach(t => t.classList.remove('active', 'bg-white', 'text-slate-900', 'shadow-sm') || t.classList.add('inactive', 'text-slate-600'));
                tab.classList.remove('inactive', 'text-slate-600');
                tab.classList.add('active', 'bg-white', 'text-slate-900', 'shadow-sm');
                activeCategory = tab.dataset.category;
                search?.dispatchEvent(new Event('input'));
            });
        });

        function syncOrderType() {
            const dineIn = form.querySelector('input[name="tipe_pesanan"]:checked').value === 'dine-in';
            tableSelect.disabled = !dineIn;
            tableSelect.required = dineIn;
            takeawayHint.hidden = dineIn;
            if (!dineIn) {
                selectedTableIndicator?.classList.add('hidden');
            }
            renderCart();
        }

        orderTypeInputs.forEach(input => input.addEventListener('change', syncOrderType));
        tableSelect?.addEventListener('change', () => {
            const opt = tableSelect.options[tableSelect.selectedIndex];
            updateTableSelectionUI(tableSelect.value, opt?.dataset?.nomor || opt?.text?.split('·')[0]);
            renderCart();
        });

        form?.addEventListener('submit', (e) => {
            const dineIn = form.querySelector('input[name="tipe_pesanan"]:checked').value === 'dine-in';
            if (cart.size === 0 || (dineIn && !tableSelect.value)) {
                e.preventDefault();
                return;
            }
            checkout.disabled = true;
            checkout.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan pesanan...`;
        });

        // INITIALIZATION
        syncOrderType();
        if (window.location.hash === '#denah') {
            switchPosView('denah');
        }
        if (tableSelect && tableSelect.value) {
            const opt = tableSelect.options[tableSelect.selectedIndex];
            updateTableSelectionUI(tableSelect.value, opt?.dataset?.nomor || opt?.text?.split('·')[0]);
        }
    })();
</script>

@endsection