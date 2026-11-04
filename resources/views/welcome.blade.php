<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Bagi Kopi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased text-slate-800 bg-slate-50">

    <!-- Mobile Header -->
    <header class="sticky top-0 z-40 bg-white border-b border-slate-200">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-primary-600 text-white flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 1v3M10 1v3M14 1v3" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-base text-slate-900 leading-tight">Bagi Kopi</h1>
                        <p class="text-[10px] text-slate-500 font-medium">ARTISANAL BREW • CATALOG</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if(isset($mejaAktif))
                        <div class="flex items-center gap-1.5 px-2.5 py-1.5 bg-primary-50 rounded-lg border border-primary-200">
                            <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            <span class="text-xs font-bold text-primary-700">Meja #{{ $mejaAktif->nomor_meja }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Search -->
            <div class="relative">
                <input type="text" id="menu-search-mobile" onkeyup="filterMenu()" 
                    placeholder="Cari kopi, pastry, atau manual brew..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white">
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-md mx-auto px-4 py-6">
        <!-- Category Pills -->
        <div class="mb-6">
            <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar pb-2">
                <button onclick="setCategory('semua', this)" class="category-btn active px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap transition-all bg-primary-600 text-white shadow-md shadow-primary-600/20">
                    Semua
                </button>
                @foreach($kategoriProduks as $kategori)
                    @php $categorySlug = \Illuminate\Support\Str::slug($kategori->nama_kategori, '-'); @endphp
                    <button onclick="setCategory('{{ $categorySlug }}', this)" class="category-btn px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">
                        {{ $kategori->nama_kategori }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Section Header -->
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Katalog Pilihan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Disangrai teliti di roastery harian Bagi Kopi</p>
            </div>
            <span class="px-2.5 py-1 bg-primary-50 text-primary-700 text-xs font-bold rounded-lg">{{ $produks->count() }} SAJIAN</span>
        </div>

        <!-- Menu List -->
        <div id="menu-grid" class="space-y-3">
            @forelse($produks as $produk)
                @php
                    $categorySlug = \Illuminate\Support\Str::slug($produk->kategoriProduk->nama_kategori ?? 'lainnya', '-');
                    $dataName = strtolower($produk->nama_produk);
                    $hargaFormatted = number_format($produk->harga_jual, 0, ',', '.');
                    $imageUrl = $produk->gambar ? asset('images/' . $produk->gambar) : 'https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=600&q=80';
                    
                    $badge = null;
                    $badgeColor = 'bg-blue-500';
                    if (str_contains(strtolower($produk->nama_produk), 'aren latte')) {
                        $badge = 'BEST SELLER';
                        $badgeColor = 'bg-blue-500';
                    } elseif (str_contains(strtolower($produk->kategoriProduk->nama_kategori ?? ''), 'manual')) {
                        $badge = 'MANUAL BREW';
                        $badgeColor = 'bg-amber-500';
                    } elseif ($produk->stock < 5 && $produk->stock > 0) {
                        $badge = 'SISA ' . $produk->stock . ' BOTOL';
                        $badgeColor = 'bg-orange-500';
                    } elseif ($produk->stock < 1) {
                        $badge = 'HABIS';
                        $badgeColor = 'bg-red-500';
                    }
                @endphp

                <article class="menu-card product-card bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
                    data-category="{{ $categorySlug }}" data-name="{{ $dataName }}"
                    data-product-id="{{ $produk->id }}" data-product-name="{{ $produk->nama_produk }}"
                    data-product-description="{{ $produk->deskripsi ?? '' }}"
                    data-product-category="{{ $produk->kategoriProduk->nama_kategori ?? 'Umum' }}"
                    data-product-price="{{ $produk->harga_jual }}" data-product-stock="{{ $produk->stock }}"
                    data-product-customizable="{{ $produk->is_customizable ? 'true' : 'false' }}"
                    data-product-options="{{ json_encode($produk->opsis) }}"
                    data-product-image="{{ $imageUrl }}">
                    
                    <div class="flex p-3 gap-3">
                        <div class="w-24 h-24 rounded-xl bg-slate-100 overflow-hidden shrink-0 relative">
                            <img src="{{ $imageUrl }}" alt="{{ $produk->nama_produk }}" class="w-full h-full object-cover">
                            @if($badge)
                                <span class="absolute top-1 left-1 px-1.5 py-0.5 {{ $badgeColor }} text-white text-[9px] font-bold rounded-md">{{ $badge }}</span>
                            @endif
                        </div>
                        
                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm leading-tight mb-1">{{ $produk->nama_produk }}</h3>
                                <p class="text-[11px] text-slate-500 line-clamp-2">{{ $produk->deskripsi ?? 'Produk berkualitas dari Bagi Kopi.' }}</p>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <span class="font-bold text-primary-700 text-sm">Rp {{ $hargaFormatted }}</span>
                                @if($produk->stock > 0)
                                    <button type="button" onclick="quickAddToCart({{ $produk->id }}, '{{ addslashes($produk->nama_produk) }}', {{ $produk->harga_jual }}, {{ $produk->stock }})" 
                                        class="w-7 h-7 rounded-full bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 active:scale-95 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                @else
                                    <span class="text-[10px] font-bold text-red-600">Habis</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <p class="font-bold text-slate-900 mb-0.5">Belum ada menu tersedia</p>
                    <p class="text-slate-500 text-sm">Silakan tambahkan produk melalui dashboard admin.</p>
                </div>
            @endforelse
        </div>

        <div id="no-menu" class="hidden text-center py-12">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <p class="font-bold text-slate-900 mb-0.5">Menu tidak ditemukan</p>
            <p class="text-slate-500 text-sm">Coba gunakan kata kunci lain</p>
        </div>     
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 px-6 py-2 z-40 safe-area-pb max-w-md mx-auto">
        <div class="flex items-center justify-around">
            <a href="{{ route('welcome') }}" class="flex flex-col items-center gap-1 py-1 px-3 text-primary-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-[10px] font-bold">Pesan</span>
            </a>
            <button onclick="toggleCart()" class="flex flex-col items-center gap-1 py-1 px-3 text-slate-400 relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span class="text-[10px] font-bold">Keranjang</span>
                <span id="bottom-nav-badge" class="absolute -top-1 right-2 w-4 h-4 bg-amber-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center hidden">0</span>
            </button>
            <button class="flex flex-col items-center gap-1 py-1 px-3 text-slate-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-[10px] font-bold">Status</span>
            </button>
        </div>
    </nav>

    <!-- ========================================== -->
    <!-- CART PAGE (FULL PAGE CHECKOUT - SCROLLABLE) -->
    <!-- ========================================== -->
    <div id="cart-page" class="fixed inset-0 z-50 bg-slate-50 hidden flex flex-col">
        <!-- Cart Header (Sticky) -->
        <div class="bg-white border-b border-slate-200 shrink-0">
            <div class="max-w-3xl mx-auto px-4 py-4">
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">Keranjang Pesanan</h2>
                            <p class="text-xs text-slate-500">Konfirmasi sajian racikan barista sebelum proses seduh.</p>
                        </div>
                    </div>
                    <button onclick="toggleCart()" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Scrollable Content Area -->
        <div class="flex-1 overflow-y-auto pb-4">
            <div class="max-w-3xl mx-auto px-4 py-6 space-y-4">
                
                <!-- Cart Items -->
                <div id="cart-items">
                    <!-- Items akan di-render oleh JavaScript -->
                </div>

                <!-- Opsi Layanan & Meja Section (DATABASE CONNECTED - SYNTAX FIXED) -->
                <div class="bg-white rounded-2xl border border-slate-200 p-4">
                    <h3 class="text-sm font-bold text-slate-900 mb-3">Opsi Layanan & Meja</h3>
                    <div class="space-y-3">
                        <!-- Order Type Selection -->
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="setOrderType('dine-in')" id="btn-dine-in" class="order-type-btn active py-3 px-4 rounded-xl border-2 border-primary-600 bg-primary-50 text-primary-700 font-bold text-sm shadow-sm transition-all">
                                Dine-in
                            </button>
                            <button type="button" onclick="setOrderType('take-away')" id="btn-take-away" class="order-type-btn py-3 px-4 rounded-xl border-2 border-slate-200 text-slate-600 font-bold text-sm bg-white hover:border-primary-300 transition-all">
                                Takeaway
                            </button>
                        </div>

                        <!-- Table Selection (Only for Dine-in) -->
                        <div id="table-selection-container">
                            <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Meja:</label>
                            <select id="table-number" class="w-full text-sm font-semibold bg-white border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                                @if(isset($mejas) && $mejas->isEmpty())
                                    <option value="">Tidak ada meja tersedia</option>
                                @elseif(isset($mejas))
                                    @php
                                        $mejaTerisi = $mejaTerisi ?? collect();
                                        $semuaTerisi = $mejaAktif === null && $mejas->count() > 0 && $mejas->every(fn ($m) => $mejaTerisi->contains($m->id));
                                    @endphp
                                    @if($semuaTerisi)
                                        <option value="">Semua meja sudah terisi</option>
                                    @endif
                                    @foreach($mejas as $meja)
                                        @php
                                            $dipakai = $mejaTerisi->contains($meja->id);
                                            $dipilihSendiri = isset($mejaAktif) && $mejaAktif->id === $meja->id;
                                            $terisi = $dipakai && !$dipilihSendiri;
                                        @endphp
                                        <option value="{{ $meja->id }}" {{ $dipilihSendiri ? 'selected' : '' }} {{ $terisi ? 'disabled' : '' }}>
                                            Meja {{ $meja->nomor_meja }} (Kapasitas: {{ $meja->kapasitas }}){{ $terisi ? ' - Terisi' : '' }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">Pilih meja...</option>
                                @endif
                            </select>
                            @if(isset($mejaAktif))
                                <p class="text-xs text-slate-500 mt-2">Anda sedang menggunakan Meja #{{ $mejaAktif->nomor_meja }}</p>
                            @endif
                        </div>

                        <div>
                            <label for="order-notes" class="block text-xs font-bold text-slate-700 mb-2">Catatan Pesanan (Opsional)</label>
                            <textarea id="order-notes" rows="2" placeholder="Contoh: Es sedikit, gula aren dipisah..." class="w-full text-sm bg-white border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-primary-500 focus:outline-none resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-2xl border border-slate-200 p-4">
                    <h3 class="text-sm font-bold text-slate-900 mb-3">METODE PEMBAYARAN</h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-primary-600 bg-primary-50 cursor-pointer">
                            <input type="radio" name="payment_method" value="QRIS" checked class="w-5 h-5 text-primary-600">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    <span class="font-bold text-slate-900 text-sm">QRIS Instan</span>
                                    <span class="text-[10px] font-bold text-white bg-primary-600 px-1.5 py-0.5 rounded">Cepat</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">BCA, GoPay, OVO, Dana, ShopeePay</p>
                            </div>
                            <div class="w-5 h-5 rounded-full bg-primary-600 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-slate-300">
                            <input type="radio" name="payment_method" value="tunai" class="w-5 h-5 text-primary-600">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 3.666V14m-6.118 4.014c.696 1.285 2.056 2.154 3.618 2.154s2.922-.869 3.618-2.154M19 10.25c0 .455-.066.893-.188 1.308M7.5 14c0 .69.105 1.354.3 1.972m9.4-1.972c.195-.618.3-1.282.3-1.972M12 2a8 8 0 100 16 8 8 0 000-16z" />
                                    </svg>
                                    <span class="font-bold text-slate-900 text-sm">Bayar di Kasir / Tunai</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">Tunjukkan nomor meja saat ke bar</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="bg-white rounded-2xl border border-slate-200 p-4">
                    <h3 class="text-sm font-bold text-slate-900 mb-3">RINCIAN PEMBAYARAN</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal Pesanan (<span id="subtotal-count">0</span> Item)</span>
                            <span id="summary-subtotal" class="font-semibold">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>PB1 Resto (10%)</span>
                            <span id="pb1-amount">Rp 0</span>
                        </div>
                        <div class="border-t border-slate-200 pt-2 mt-2">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="font-bold text-slate-900">Total Tagihan</span>
                                    <p class="text-xs text-slate-500 mt-0.5">Termasuk PPN & Pelayanan</p>
                                </div>
                                <span id="summary-total" class="text-2xl font-bold text-primary-600">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estimated Time -->
                <div class="flex items-start gap-2 text-xs text-slate-600 pb-4">
                    <svg class="w-4 h-4 mt-0.5 text-primary-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>Estimasi waktu peracikan: <span class="font-bold text-primary-600">10-14 menit</span>. Pesanan langsung diantar ke meja Anda.</p>
                </div>
            </div>
        </div>

        <!-- Fixed Bottom Payment Button -->
        <div class="shrink-0 bg-white border-t border-slate-200 p-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
            <div class="max-w-3xl mx-auto">
                <button onclick="processOrder()" id="checkout-btn" disabled class="w-full bg-primary-600 text-white rounded-xl py-4 px-6 flex items-center justify-between font-bold shadow-lg shadow-primary-600/25 active:scale-95 transition disabled:bg-slate-300 disabled:cursor-not-allowed">
                    <div>
                        <div class="text-sm">BAYAR SEKARANG</div>
                        <div class="text-xs opacity-90">QRIS Instan</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="bottom-total" class="text-lg">Rp 0</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- RECEIPT MODAL -->
    <div id="receipt-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white max-w-sm w-full rounded-3xl border border-slate-200 p-6 shadow-2xl space-y-4">
            <div class="text-center space-y-1">
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center text-3xl font-bold mb-3">✓</div>
                <h3 class="font-bold text-xl text-slate-900">Pesanan Diterima!</h3>
                <p class="text-sm text-slate-500">Tunjukkan struk digital ini ke kasir atau tunggu di meja Anda.</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-dashed border-slate-300 text-xs space-y-2 font-mono">
                <div class="flex justify-between border-b border-slate-200 pb-2">
                    <span class="font-bold">BAGI KOPI</span>
                    <span id="receipt-date"></span>
                </div>
                <div class="flex justify-between text-slate-700 font-bold">
                    <span>NO. PESANAN</span>
                    <span id="receipt-number"></span>
                </div>
                <div class="flex justify-between text-slate-700 font-bold">
                    <span id="receipt-order-type"></span>
                    <span id="receipt-target" class="text-primary-600"></span>
                </div>
                <div id="receipt-items-list" class="space-y-1.5 py-2 border-b border-slate-200"></div>
                <div class="flex justify-between font-bold pt-1 text-sm text-slate-900">
                    <span>TOTAL:</span>
                    <span id="receipt-grand-total"></span>
                </div>
                <div id="receipt-notes-display" class="text-[11px] text-slate-600 italic pt-1"></div>
            </div>

            <div id="receipt-qris-section" class="hidden text-center p-3 bg-amber-50 rounded-2xl border border-amber-200 space-y-2">
                <p class="text-xs font-bold text-amber-800">Scan QRIS untuk Membayar Tagihan:</p>
                <div class="p-2 bg-white rounded-xl shadow-inner inline-block border border-amber-100">
                    <img id="receipt-qris-img" src="" alt="QRIS Code" class="w-40 h-40 mx-auto rounded-lg">
                </div>
                <p id="receipt-qris-status" class="text-[10px] font-bold text-amber-700">Nominal otomatis sesuai total belanja.</p>
            </div>

            <button onclick="closeReceipt()" class="w-full py-3.5 rounded-2xl bg-primary-600 text-white text-sm font-bold hover:bg-primary-700 transition-all shadow-lg">
                Tutup & Pesan Baru
            </button>
        </div>
    </div>

    <!-- Mobile Customization Modal -->
    <div id="mobile-customization-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeMobileCustomization()"></div>
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl max-h-[90vh] overflow-y-auto animate-slide-up">
            <div class="sticky top-0 bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between">
                <button onclick="closeMobileCustomization()" class="p-2 -ml-2 text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <h3 class="font-bold text-slate-900">Kustomisasi Pesanan</h3>
                <div class="w-10"></div>
            </div>

            <div class="px-4 py-4">
                <div class="rounded-2xl overflow-hidden mb-4">
                    <img id="mobile-custom-image" src="" alt="" class="w-full h-48 object-cover">
                </div>
                <div class="flex items-center gap-2 mb-2" id="mobile-custom-tags"></div>
                <h2 id="mobile-custom-name" class="text-xl font-bold text-slate-900 mb-1"></h2>
                <p id="mobile-custom-description" class="text-sm text-slate-500 mb-3"></p>
                <div class="flex items-center gap-2 mb-4">
                    <span id="mobile-custom-price" class="text-2xl font-bold text-primary-600"></span>
                </div>

                <!-- Dynamic Options from Database -->
                <div id="dynamic-options-container" class="mb-5 hidden">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            Pilihan Tambahan
                        </label>
                    </div>
                    <div class="space-y-2" id="dynamic-options-list">
                        <!-- Opsi dari database akan di-render di sini oleh JavaScript -->
                    </div>
                </div>

                <!-- Temperature -->
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-sm font-bold text-slate-900 flex items-center gap-2">Penyajian Suhu</label>
                        <span class="text-[10px] font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full">Wajib</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="selectTemperature('iced')" id="temp-iced" class="temp-btn active py-3 px-4 rounded-xl border-2 border-primary-600 bg-primary-50 text-primary-700 font-bold text-sm">Dingin / Iced</button>
                        <button type="button" onclick="selectTemperature('hot')" id="temp-hot" class="temp-btn py-3 px-4 rounded-xl border-2 border-slate-200 text-slate-600 font-bold text-sm">Hangat / Hot</button>
                    </div>
                </div>

                <!-- Milk -->
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-sm font-bold text-slate-900 flex items-center gap-2">Pilihan Susu</label>
                        <span class="text-[10px] font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full">Pilih 1</span>
                    </div>
                    <div class="space-y-2">
                        <button type="button" onclick="selectMilk('fresh', 0)" id="milk-fresh" class="milk-btn active w-full py-3 px-4 rounded-xl border-2 border-primary-600 bg-primary-50 text-left flex items-center justify-between">
                            <span class="font-bold text-slate-900 text-sm">Fresh Milk (Standar)</span>
                            <div class="milk-check flex items-center gap-2"><span class="text-xs font-bold text-slate-600">+Rp 0</span><svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
                        </button>
                        <button type="button" onclick="selectMilk('oat', 8000)" id="milk-oat" class="milk-btn w-full py-3 px-4 rounded-xl border-2 border-slate-200 text-left flex items-center justify-between">
                            <span class="font-bold text-slate-900 text-sm">Oat Milk</span>
                            <div class="milk-check"><span class="text-xs font-bold text-slate-600">+Rp 8.000</span></div>
                        </button>
                        <button type="button" onclick="selectMilk('almond', 8000)" id="milk-almond" class="milk-btn w-full py-3 px-4 rounded-xl border-2 border-slate-200 text-left flex items-center justify-between">
                            <span class="font-bold text-slate-900 text-sm">Almond Milk</span>
                            <div class="milk-check"><span class="text-xs font-bold text-slate-600">+Rp 8.000</span></div>
                        </button>
                    </div>
                </div>

                <!-- Sugar -->
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-sm font-bold text-slate-900 flex items-center gap-2">Tingkat Manis Aren</label>
                        <span class="text-[10px] font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full">Pilih 1</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="selectSugar('normal')" id="sugar-normal" class="sugar-btn active py-3 px-2 rounded-xl border-2 border-primary-600 bg-primary-50 text-primary-700 font-bold text-xs text-center">Normal</button>
                        <button type="button" onclick="selectSugar('less')" id="sugar-less" class="sugar-btn py-3 px-2 rounded-xl border-2 border-slate-200 text-slate-600 font-bold text-xs text-center">Less Sweet</button>
                        <button type="button" onclick="selectSugar('no')" id="sugar-no" class="sugar-btn py-3 px-2 rounded-xl border-2 border-slate-200 text-slate-600 font-bold text-xs text-center">No Sugar</button>
                    </div>
                </div>

                <!-- Extra Shot -->
                <div class="mb-5">
                    <label class="flex items-center justify-between p-4 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-primary-300 transition">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="extra-shot" onchange="toggleExtraShot()" class="w-5 h-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                            <div>
                                <div class="font-bold text-slate-900 text-sm">Ekstra Espresso Shot</div>
                                <div class="text-xs text-slate-500">Ekstra intensitas & body kopi</div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-primary-600">+Rp 6.000</span>
                    </label>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-2">Catatan untuk Barista</label>
                    <textarea id="mobile-custom-notes" rows="3" placeholder="Cth: Less ice batu..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                </div>
            </div>

            <!-- Fixed Bottom Bar -->
            <div class="sticky bottom-0 bg-white border-t border-slate-200 px-4 py-3 flex items-center gap-3">
                <div class="flex items-center gap-3 bg-slate-100 rounded-xl px-3 py-2">
                    <button type="button" onclick="decrementQuantity()" class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-slate-700 font-bold shadow-sm">-</button>
                    <span id="mobile-custom-qty" class="font-bold text-slate-900 w-6 text-center">1</span>
                    <button type="button" onclick="incrementQuantity()" class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-slate-700 font-bold shadow-sm">+</button>
                </div>
                <button type="button" onclick="addToCartFromMobile()" class="flex-1 bg-primary-600 text-white font-bold py-3.5 px-6 rounded-xl flex items-center justify-between shadow-lg shadow-primary-600/25 active:scale-95 transition">
                    <span>Pesan</span>
                    <span id="mobile-custom-total">Rp 0</span>
                </button>
            </div>
        </div>
    </div>

    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .safe-area-pb { padding-bottom: env(safe-area-inset-bottom); }
        @keyframes slide-up { from { transform: translateY(100%); } to { transform: translateY(0); } }
        .animate-slide-up { animation: slide-up 0.3s ease-out; }
        .temp-btn.active, .milk-btn.active, .sugar-btn.active { @apply border-primary-600 bg-primary-50 text-primary-700; }
        .temp-btn:not(.active), .milk-btn:not(.active), .sugar-btn:not(.active) { @apply border-slate-200 text-slate-600; }
        .order-type-btn.active { @apply border-primary-600 bg-primary-50 text-primary-700 shadow-sm; }
        .order-type-btn:not(.active) { @apply border-slate-200 text-slate-600 bg-white; }
    </style>

    <script>
        // Global Variables
        let cart = [];
        let nextCartLineId = 1;
        let mobileCustomProduct = null;
        let mobileCustomQty = 1;
        let mobileCustomOptions = { temperature: 'iced', milk: 'fresh', milkPrice: 0, sugar: 'normal', extraShot: false, dynamicOptions: {} };
        let currentOrderType = 'dine-in';
        let currentCategory = 'semua';

        // --- MOBILE CUSTOMIZATION FUNCTIONS ---
        function openMobileCustomization(produkId, name, price, stock, image, description, tags = [], options = []) {
            mobileCustomProduct = { produkId, name, basePrice: price, stock, image, description, options: options };
            mobileCustomQty = 1;
            mobileCustomOptions = { temperature: 'iced', milk: 'fresh', milkPrice: 0, sugar: 'normal', extraShot: false, dynamicOptions: {} };
            
            document.getElementById('mobile-custom-image').src = image;
            document.getElementById('mobile-custom-name').textContent = name;
            document.getElementById('mobile-custom-description').textContent = description || 'Produk berkualitas dari Bagi Kopi.';
            document.getElementById('mobile-custom-price').textContent = formatRupiah(price);
            document.getElementById('mobile-custom-notes').value = '';
            document.getElementById('extra-shot').checked = false;
            
            // Render opsi dinamis dari database
            const dynamicContainer = document.getElementById('dynamic-options-container');
            const dynamicList = document.getElementById('dynamic-options-list');
            dynamicList.innerHTML = '';
            
            if (options && options.length > 0) {
                dynamicContainer.classList.remove('hidden');
                options.forEach(opt => {
                    const optId = `opt-${opt.id}`;
                    const priceText = opt.harga_tambahan > 0 ? `+${formatRupiah(opt.harga_tambahan)}` : 'Gratis';
                    
                    const label = document.createElement('label');
                    label.className = 'flex items-center justify-between p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-primary-300 transition bg-white';
                    label.innerHTML = `
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="${optId}" data-option-id="${opt.id}" data-option-price="${opt.harga_tambahan}" onchange="toggleDynamicOption(this)" class="w-5 h-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                            <div>
                                <div class="font-bold text-slate-900 text-sm">${opt.nama_opsi}</div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-primary-600">${priceText}</span>
                    `;
                    dynamicList.appendChild(label);
                });
            } else {
                dynamicContainer.classList.add('hidden');
            }
            
            updateCustomizationUI();
            updateMobileTotal();
            document.getElementById('mobile-customization-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function toggleDynamicOption(checkbox) {
            const optId = checkbox.dataset.optionId;
            const optPrice = parseFloat(checkbox.dataset.optionPrice) || 0;
            
            if (checkbox.checked) {
                mobileCustomOptions.dynamicOptions[optId] = optPrice;
            } else {
                delete mobileCustomOptions.dynamicOptions[optId];
            }
            updateMobileTotal();
        }

        function closeMobileCustomization() {
            document.getElementById('mobile-customization-modal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function selectTemperature(temp) {
            mobileCustomOptions.temperature = temp;
            document.getElementById('temp-iced').classList.toggle('active', temp === 'iced');
            document.getElementById('temp-hot').classList.toggle('active', temp === 'hot');
        }

        function selectMilk(type, price) {
            mobileCustomOptions.milk = type;
            mobileCustomOptions.milkPrice = price;
            ['fresh', 'oat', 'almond'].forEach(m => {
                const btn = document.getElementById(`milk-${m}`);
                const priceText = m === 'fresh' ? '+Rp 0' : '+Rp 8.000';
                if (m === type) {
                    btn.classList.add('active');
                    btn.querySelector('.milk-check').innerHTML = `<span class="text-xs font-bold text-slate-600">${priceText}</span><svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>`;
                } else {
                    btn.classList.remove('active');
                    btn.querySelector('.milk-check').innerHTML = `<span class="text-xs font-bold text-slate-600">${priceText}</span>`;
                }
            });
            updateMobileTotal();
        }

        function selectSugar(level) {
            mobileCustomOptions.sugar = level;
            ['normal', 'less', 'no'].forEach(s => {
                document.getElementById(`sugar-${s}`).classList.toggle('active', s === level);
            });
        }

        function toggleExtraShot() {
            mobileCustomOptions.extraShot = document.getElementById('extra-shot').checked;
            updateMobileTotal();
        }

        function cartQtyForProduct(produkId) {
            return cart
                .filter(item => item.produkId === produkId)
                .reduce((sum, item) => sum + item.qty, 0);
        }

        function incrementQuantity() {
            const available = mobileCustomProduct.stock - cartQtyForProduct(mobileCustomProduct.produkId);
            if (mobileCustomQty < available) {
                mobileCustomQty++;
                document.getElementById('mobile-custom-qty').textContent = mobileCustomQty;
                updateMobileTotal();
            }
        }

        function decrementQuantity() {
            if (mobileCustomQty > 1) {
                mobileCustomQty--;
                document.getElementById('mobile-custom-qty').textContent = mobileCustomQty;
                updateMobileTotal();
            }
        }

        function updateMobileTotal() {
            let dynamicOptionsTotal = 0;
            for (const key in mobileCustomOptions.dynamicOptions) {
                dynamicOptionsTotal += mobileCustomOptions.dynamicOptions[key];
            }
            
            const total = (mobileCustomProduct.basePrice + mobileCustomOptions.milkPrice + (mobileCustomOptions.extraShot ? 6000 : 0) + dynamicOptionsTotal) * mobileCustomQty;
            document.getElementById('mobile-custom-total').textContent = formatRupiah(total);
        }

        function updateCustomizationUI() {
            selectTemperature('iced');
            selectMilk('fresh', 0);
            selectSugar('normal');
        }

        function addToCartFromMobile() {
            if (!mobileCustomProduct) return;

            const notes = document.getElementById('mobile-custom-notes').value.trim();
            const parts = [
                mobileCustomOptions.temperature === 'hot' ? 'Hot' : 'Iced',
                { fresh: 'Fresh Milk', oat: 'Oat Milk', almond: 'Almond Milk' }[mobileCustomOptions.milk] || 'Fresh Milk',
                { normal: 'Normal Sugar', less: 'Less Sugar', no: 'No Sugar' }[mobileCustomOptions.sugar] || 'Normal Sugar',
            ];

            if (mobileCustomOptions.extraShot) parts.push('Extra Shot');

            const selectedDynamicOptions = [];
            document.querySelectorAll('#dynamic-options-list input[type="checkbox"]:checked').forEach(cb => {
                const name = cb.parentElement.querySelector('.font-bold').textContent.trim();
                if (name) selectedDynamicOptions.push(name);
            });
            parts.push(...selectedDynamicOptions);

            if (notes) parts.push(notes);

            const customization = parts.join(', ');
            const available = mobileCustomProduct.stock - cartQtyForProduct(mobileCustomProduct.produkId);

            if (available < 1) {
                alert('Stok produk ini sudah habis di keranjang.');
                closeMobileCustomization();
                return;
            }

            const addQty = Math.min(mobileCustomQty, available);
            let dynamicOptionsTotal = 0;
            for (const key in mobileCustomOptions.dynamicOptions) {
                dynamicOptionsTotal += mobileCustomOptions.dynamicOptions[key];
            }
            const finalPrice = mobileCustomProduct.basePrice + mobileCustomOptions.milkPrice + (mobileCustomOptions.extraShot ? 6000 : 0) + dynamicOptionsTotal;

            const item = cart.find(item => item.produkId === mobileCustomProduct.produkId && item.customization === customization);
            if (item) {
                item.qty += addQty;
            } else {
                cart.push({ cartLineId: nextCartLineId++, produkId: mobileCustomProduct.produkId, name: mobileCustomProduct.name, price: finalPrice, category: 'Menu', stock: mobileCustomProduct.stock, qty: addQty, customization: customization, image: mobileCustomProduct.image });
            }
            closeMobileCustomization();
            renderCart();
            updateHeaderCart();
        }

        function addSimpleToCart(produkId, name, price, stock, image) {
            const available = stock - cartQtyForProduct(produkId);
            if (available < 1) {
                alert('Stok produk ini sudah habis di keranjang.');
                return;
            }

            const item = cart.find(item => item.produkId === produkId && item.customization === '');
            if (item) {
                item.qty += 1;
            } else {
                cart.push({ cartLineId: nextCartLineId++, produkId: produkId, name: name, price: price, category: 'Menu', stock: stock, qty: 1, customization: '', image: image });
            }
            renderCart();
            updateHeaderCart();
        }

        function quickAddToCart(produkId, name, price, stock) {
            const card = document.querySelector(`[data-product-id="${produkId}"]`);
            if (!card) return;

            if (card.dataset.productCustomizable === 'false') {
                addSimpleToCart(produkId, name, price, stock, card.dataset.productImage);
                return;
            }

            let options = [];
            try {
                options = JSON.parse(card.dataset.productOptions || '[]') || [];
            } catch (parseError) {
                options = [];
            }
            openMobileCustomization(produkId, name, price, stock, card.dataset.productImage, card.dataset.productDescription, [], options);
        }

        // --- CART & CHECKOUT FUNCTIONS ---
        function toggleCart() {
            const cartPage = document.getElementById('cart-page');
            cartPage.classList.toggle('hidden');
            document.body.style.overflow = cartPage.classList.contains('hidden') ? '' : 'hidden';
        }

        function setOrderType(type) {
            currentOrderType = type;
            const btnDineIn = document.getElementById('btn-dine-in');
            const btnTakeAway = document.getElementById('btn-take-away');
            const tableContainer = document.getElementById('table-selection-container');
            const tableSelect = document.getElementById('table-number');
            
            if (type === 'dine-in') {
                btnDineIn.classList.add('active');
                btnTakeAway.classList.remove('active');
                tableContainer.classList.remove('hidden');
                tableSelect.disabled = false;
            } else {
                btnTakeAway.classList.add('active');
                btnDineIn.classList.remove('active');
                tableContainer.classList.add('hidden');
                tableSelect.disabled = true;
                tableSelect.value = '';
            }
            syncCheckoutState();
        }

        function formatRupiah(num) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num).replace('IDR', 'Rp').trim();
        }

        function escapeHtml(value) {
            return String(value).replace(/[&<>"']/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[character]);
        }

        function syncCheckoutState() {
            const tableId = document.getElementById('table-number').value;
            document.getElementById('checkout-btn').disabled = cart.length === 0 || (currentOrderType === 'dine-in' && !tableId);
        }

        function updateHeaderCart() {
            const bottomBadge = document.getElementById('bottom-nav-badge');
            const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
            
            if (totalQty > 0) {
                bottomBadge.textContent = totalQty;
                bottomBadge.classList.remove('hidden');
                bottomBadge.classList.add('flex');
            } else {
                bottomBadge.classList.add('hidden');
                bottomBadge.classList.remove('flex');
            }
        }

        function renderCart() {
            const container = document.getElementById('cart-items');
            if (cart.length === 0) {
                container.innerHTML = `<div class="text-center py-16 text-slate-400 space-y-2"><div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg></div><p class="font-bold text-slate-900">Keranjang masih kosong</p><p class="text-sm text-slate-500">Total: 0 item</p></div>`;
                updateTotals(0, 0);
                return;
            }

            let html = '';
            let subtotal = 0;
            let totalItems = 0;

            cart.forEach((item, index) => {
                const itemTotal = item.price * item.qty;
                subtotal += itemTotal;
                totalItems += item.qty;

                html += `
                    <div class="bg-white rounded-2xl border border-slate-200 p-4">
                        <div class="flex gap-3">
                            <img src="${item.image || 'https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=200&q=80'}" alt="${item.name}" class="w-20 h-20 rounded-xl object-cover">
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-1">
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-sm">${escapeHtml(item.name)}</h4>
                                        <p class="text-sm font-bold text-primary-600">${formatRupiah(item.price)}</p>
                                    </div>
                                </div>
                                ${item.customization ? `<div class="flex flex-wrap gap-1 mb-2">${item.customization.split(',').map(tag => `<span class="text-[10px] px-2 py-0.5 bg-slate-100 text-slate-600 rounded-md">${escapeHtml(tag.trim())}</span>`).join('')}</div>` : ''}
                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex items-center gap-2">
                                        <button onclick="updateQuantity(${index}, -1)" class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-200">-</button>
                                        <span class="text-sm font-bold text-slate-900 w-6 text-center">${item.qty}</span>
                                        <button onclick="updateQuantity(${index}, 1)" class="w-7 h-7 rounded-full bg-primary-600 flex items-center justify-center text-white hover:bg-primary-700">+</button>
                                    </div>
                                    <button onclick="removeItem(${index})" class="p-1.5 text-slate-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
            });

            container.innerHTML = html;
            updateTotals(subtotal, totalItems);
        }

        function updateQuantity(index, change) {
            const item = cart[index];
            const otherQty = cart
                .filter((cartItem, cartIndex) => cartIndex !== index && cartItem.produkId === item.produkId)
                .reduce((sum, cartItem) => sum + cartItem.qty, 0);
            item.qty = Math.max(1, Math.min(item.stock - otherQty, item.qty + change));
            renderCart();
            updateHeaderCart();
        }

        function removeItem(index) {
            if (confirm('Hapus item dari keranjang?')) {
                cart.splice(index, 1);
                renderCart();
                updateHeaderCart();
            }
        }

        function updateTotals(subtotal = 0, totalItems = 0) {
            const pb1 = Math.round(subtotal * 0.10);
            const total = subtotal + pb1;

            document.getElementById('subtotal-count').textContent = totalItems;
            document.getElementById('summary-subtotal').textContent = formatRupiah(subtotal);
            document.getElementById('pb1-amount').textContent = formatRupiah(pb1);
            document.getElementById('summary-total').textContent = formatRupiah(total);
            document.getElementById('bottom-total').textContent = formatRupiah(total);
            
            syncCheckoutState();
        }

        async function processOrder() {
            if (cart.length === 0) return;

            const tableSelect = document.getElementById('table-number');
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const notes = document.getElementById('order-notes')?.value.trim() || '';
            const checkout = document.getElementById('checkout-btn');
            
            checkout.disabled = true;
            checkout.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...`;

            try {
                const response = await fetch('/api/transaksis', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        meja_id: currentOrderType === 'dine-in' ? Number(tableSelect.value) : null,
                        tipe_pesanan: currentOrderType,
                        metode_pembayaran: paymentMethod,
                        catatan: notes || null,
                        items: cart.map(item => ({ produk_id: item.produkId, qty: item.qty, customization: item.customization || null })),
                    }),
                });
                const result = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(Object.values(result.errors || {}).flat()[0] || result.message || 'Pesanan gagal dikirim.');
                }

                const order = result.data || result.transaksi;
                document.getElementById('receipt-date').textContent = new Date(order.waktu_transaksi).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                document.getElementById('receipt-number').textContent = `#${order.id}`;
                document.getElementById('receipt-order-type').textContent = currentOrderType === 'dine-in' ? 'Dine-in' : 'Takeaway';
                
                let targetText = 'BAWA PULANG';
                if (currentOrderType === 'dine-in' && tableSelect.value) {
                    const selectedOption = tableSelect.options[tableSelect.selectedIndex];
                    targetText = selectedOption.text.split('(')[0].trim();
                }
                document.getElementById('receipt-target').textContent = targetText;

                let itemsList = '';
                cart.forEach(item => {
                    itemsList += `<div class="flex justify-between gap-3"><span>${item.qty}x ${escapeHtml(item.name)}${item.customization ? `<br><small class="text-slate-500">${escapeHtml(item.customization)}</small>` : ''}</span><span>${formatRupiah(item.price * item.qty)}</span></div>`;
                });
                document.getElementById('receipt-items-list').innerHTML = itemsList;
                document.getElementById('receipt-grand-total').textContent = formatRupiah(order.total_harga);

                const qrisSection = document.getElementById('receipt-qris-section');
                const qrisStatus = document.getElementById('receipt-qris-status');
                if (paymentMethod === 'QRIS' && order.qris && order.qris.qr_url) {
                    document.getElementById('receipt-qris-img').src = order.qris.qr_url;
                    qrisSection.classList.remove('hidden');
                    startQrisPolling(order.id, order.qris.order_id);
                } else if (paymentMethod === 'QRIS') {
                    document.getElementById('receipt-qris-img').src = '';
                    qrisSection.classList.remove('hidden');
                    qrisStatus.textContent = order.qris_error || 'QRIS otomatis tidak tersedia. Silakan bayar di kasir.';
                    qrisStatus.className = 'text-[10px] font-bold text-red-700';
                } else {
                    qrisSection.classList.add('hidden');
                }

                const notesDisplay = document.getElementById('receipt-notes-display');
                if (notes) {
                    notesDisplay.textContent = 'Catatan: "' + notes + '"';
                    notesDisplay.classList.remove('hidden');
                } else {
                    notesDisplay.classList.add('hidden');
                }

                toggleCart();
                document.getElementById('receipt-modal').classList.remove('hidden');
                document.getElementById('receipt-modal').classList.add('flex');
            } catch (requestError) {
                alert(requestError.message);
            } finally {
                checkout.disabled = false;
                checkout.innerHTML = `<div><div class="text-sm">BAYAR SEKARANG</div><div class="text-xs opacity-90">QRIS Instan</div></div><div class="flex items-center gap-2"><span id="bottom-total" class="text-lg">Rp 0</span><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></div>`;
                renderCart();
            }
        }

        let qrisPollTimer = null;

        function stopQrisPolling() {
            if (qrisPollTimer) {
                clearInterval(qrisPollTimer);
                qrisPollTimer = null;
            }
        }

        function startQrisPolling(transaksiId, orderId) {
            stopQrisPolling();
            const statusEl = document.getElementById('receipt-qris-status');
            statusEl.textContent = 'Menunggu pembayaran...';
            statusEl.className = 'text-[10px] font-bold text-amber-700';

            qrisPollTimer = setInterval(async () => {
                try {
                    const response = await fetch(`/api/transaksis/${transaksiId}/check-status`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ order_id: orderId }),
                    });
                    const data = await response.json();

                    if (data.paid) {
                        statusEl.textContent = 'Pembayaran Lunas ✓';
                        statusEl.className = 'text-[10px] font-bold text-emerald-700';
                        stopQrisPolling();
                    } else {
                        statusEl.textContent = 'Menunggu pembayaran...';
                    }
                } catch (pollError) {
                    // diamkan, coba lagi pada interval berikutnya
                }
            }, 3000);
        }

        function closeReceipt() {
            stopQrisPolling();
            document.getElementById('receipt-modal').classList.add('hidden');
            document.getElementById('receipt-modal').classList.remove('flex');
            document.getElementById('order-notes').value = '';
            cart = [];
            nextCartLineId = 1;
            renderCart();
            updateHeaderCart();
        }

        function setCategory(category, activeBtn) {
            currentCategory = category;
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('bg-primary-600', 'text-white', 'shadow-md');
                btn.classList.add('bg-white', 'text-slate-600', 'border', 'border-slate-200');
            });
            if (activeBtn) {
                activeBtn.classList.remove('bg-white', 'text-slate-600', 'border', 'border-slate-200');
                activeBtn.classList.add('bg-primary-600', 'text-white', 'shadow-md');
            }

            let visibleCount = 0;
            document.querySelectorAll('.menu-card').forEach(card => {
                if (category === 'semua' || card.getAttribute('data-category') === category) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            document.getElementById('no-menu').classList.toggle('hidden', visibleCount > 0);
        }

        function filterMenu() {
            const query = document.getElementById('menu-search-mobile')?.value.toLowerCase() || '';
            let visibleCount = 0;
            document.querySelectorAll('.menu-card').forEach(card => {
                const matchesCategory = currentCategory === 'semua' || card.getAttribute('data-category') === currentCategory;
                if (matchesCategory && card.getAttribute('data-name').includes(query)) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            document.getElementById('no-menu').classList.toggle('hidden', visibleCount > 0);
        }

        // Initialize
        renderCart();
        updateHeaderCart();
        setOrderType('dine-in');
    </script>
</body>
</html>