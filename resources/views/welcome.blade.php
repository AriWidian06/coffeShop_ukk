<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bagi Kopi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased text-slate-800 bg-slate-50">

    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between gap-4">
                
                <a href="/" class="flex items-center gap-3 shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-primary-600 text-white flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 1v3M10 1v3M14 1v3" />
                        </svg>
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="font-bold text-xl text-slate-900 leading-tight">Bagi Kopi</h1>
                        <p class="text-xs text-slate-500 font-medium">Coffee & Eatery</p>
                    </div>
                </a>

                <div class="hidden md:flex flex-1 max-w-xl relative">
                    <input type="text" id="menu-search" onkeyup="filterMenu()" 
                        placeholder="Cari kopi, pastry, atau manual brew..."
                        class="w-full pl-11 pr-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <div class="flex items-center gap-3">
                    @if(isset($mejaAktif))
                        <span class="hidden sm:flex px-3 py-2 bg-primary-50 text-primary-700 text-xs font-bold rounded-xl border border-primary-200 items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            Meja #{{ $mejaAktif->nomor_meja }}
                        </span>
                    @endif

                    <button onclick="toggleCart()" class="relative flex items-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span class="hidden sm:inline">Keranjang</span>
                        <span id="header-cart-badge" class="absolute -top-2 -right-2 w-6 h-6 bg-amber-500 text-white text-xs font-bold rounded-full items-center justify-center border-2 border-white hidden">0</span>
                    </button>
                </div>
            </div>

            <div class="mt-3 md:hidden relative">
                <input type="text" id="menu-search-mobile" onkeyup="filterMenu()" 
                    placeholder="Cari menu..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-8">
            <div class="flex items-center gap-3 overflow-x-auto hide-scrollbar pb-2">
                <button onclick="setCategory('semua')" class="category-btn active px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all bg-primary-600 text-white shadow-md shadow-primary-600/20">
                    Semua Menu
                </button>
                @foreach($kategoriProduks as $kategori)
                    @php $categorySlug = \Illuminate\Support\Str::slug($kategori->nama_kategori, '-'); @endphp
                    <button onclick="setCategory('{{ $categorySlug }}')" class="category-btn px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:border-slate-300">
                        {{ $kategori->nama_kategori }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900">Menu</h2>
            <p class="text-slate-500">Menu yang ada di Bagi Kopi eatery</p>
        </div>

        <!-- Menu Grid (Responsive: 1 kolom mobile, 2 tablet, 3 laptop, 4 desktop besar) -->
        <div id="menu-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($produks as $produk)
                @php
                    $categorySlug = \Illuminate\Support\Str::slug($produk->kategoriProduk->nama_kategori ?? 'lainnya', '-');
                    $dataName = strtolower($produk->nama_produk);
                    $hargaFormatted = number_format($produk->harga_jual, 0, ',', '.');
                    $imageUrl = $produk->gambar ? asset('storage/' . $produk->gambar) : 'https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=600&q=80';
                @endphp

                <article class="menu-card product-card bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-primary-200 overflow-hidden flex flex-col"
                    data-category="{{ $categorySlug }}" data-name="{{ $dataName }}"
                    data-product-id="{{ $produk->id }}" data-product-name="{{ $produk->nama_produk }}"
                    data-product-description="{{ $produk->deskripsi ?? '' }}"
                    data-product-category="{{ $produk->kategoriProduk->nama_kategori ?? 'Umum' }}"
                    data-product-price="{{ $produk->harga_jual }}" data-product-stock="{{ $produk->stock }}"
                    data-product-image="{{ $imageUrl }}">
                    
                    <button type="button" data-product-open aria-label="Lihat detail {{ $produk->nama_produk }}"
                        class="product-detail-trigger text-left">
                        <div class="relative aspect-4/3 bg-slate-100 overflow-hidden group pointer-events-none">
                            <img src="{{ $imageUrl }}" alt="{{ $produk->nama_produk }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 pointer-events-none">
                        </div>
                        <div class="p-5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600 bg-primary-50 px-2 py-1 rounded-md mb-2 inline-block">
                                {{ $produk->kategoriProduk->nama_kategori ?? 'Umum' }}
                            </span>
                            <h3 class="font-bold text-slate-900 text-lg leading-tight mb-2">{{ $produk->nama_produk }}</h3>
                            <p class="text-sm text-slate-500 line-clamp-2">
                                {{ $produk->deskripsi ?? 'Produk berkualitas dari Perkoci Eatery.' }}
                            </p>
                        </div>
                    </button>

                    <div class="px-5 pb-5 flex flex-col flex-1">
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3 mt-auto">
                            <span class="font-extrabold text-primary-700 text-lg">Rp {{ $hargaFormatted }}</span>
                            <button type="button" data-product-open @disabled($produk->stock < 1)
                                class="product-customize-button min-h-11 rounded-xl bg-primary-600 px-4 py-2 text-white text-sm font-bold hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                Pilih & kustomisasi
                            </button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full text-center py-20">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <p class="font-bold text-slate-900 text-lg mb-1">Belum ada menu tersedia</p>
                    <p class="text-slate-500">Silakan tambahkan produk melalui dashboard admin.</p>
                </div>
            @endforelse
        </div>

        <!-- Empty State Search -->
        <div id="no-menu" class="hidden col-span-full text-center py-20">
            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <p class="font-bold text-slate-900 text-lg mb-1">Menu tidak ditemukan</p>
            <p class="text-slate-500">Coba gunakan kata kunci lain</p>
        </div>     
    </main>

    <style>
        .product-detail-trigger { display: block; width: 100%; padding: 0; border: 0; background: #fff; color: inherit; font: inherit; cursor: pointer; }
        .product-detail-trigger:focus-visible, .product-customize-button:focus-visible, .product-dialog button:focus-visible, .product-dialog textarea:focus-visible { outline: 3px solid #2563eb; outline-offset: 3px; }
        .product-customize-button { flex: 0 0 auto; cursor: pointer; }
        .product-dialog { position: fixed; inset: 0; width: min(760px, calc(100vw - 32px)); height: fit-content; max-width: none; max-height: min(90dvh, 820px); margin: auto; padding: 0; overflow-x: hidden; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; color: #0f172a; box-shadow: 0 20px 60px rgba(15, 23, 42, .28); }
        .product-dialog::backdrop { background: rgba(25, 18, 14, .62); backdrop-filter: blur(2px); }
        .product-dialog-shell { position: relative; display: grid; grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr); min-height: 450px; }
        .product-dialog-image-wrap { min-height: 100%; background: #f1f5f9; pointer-events: none; }
        .product-dialog-image { display: block; width: 100%; height: 100%; min-height: 450px; object-fit: cover; pointer-events: none; }
        .product-dialog-content { display: flex; flex-direction: column; padding: 2rem; }
        .product-dialog-close { position: absolute; z-index: 1; top: 12px; right: 12px; min-height: 44px; padding: .5rem .75rem; border: 1px solid #64748b; border-radius: 4px; background: #fff; color: #0f172a; font: inherit; font-weight: 700; cursor: pointer; }
        .product-dialog-category { margin: 0 0 .45rem; color: #1d4ed8; font-size: .82rem; font-weight: 700; }
        .product-dialog-content h2 { margin: 0; color: #0f172a; font-size: 1.55rem; line-height: 1.25; overflow-wrap: anywhere; }
        .product-dialog-description { min-height: 3rem; margin: .7rem 0 1rem; color: #475569; line-height: 1.55; white-space: pre-line; }
        .product-dialog-facts { display: flex; align-items: baseline; justify-content: space-between; gap: .75rem; padding: .8rem 0; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1; }
        .product-dialog-facts strong { color: #1d4ed8; font-size: 1.2rem; white-space: nowrap; }
        .product-dialog-facts span { color: #475569; font-size: .82rem; text-align: right; }
        .product-customization-options { display: flex; flex-wrap: wrap; gap: .5rem; margin: 1rem 0 .35rem; padding: 0; border: 0; }
        .product-customization-options legend { width: 100%; margin-bottom: .35rem; color: #334155; font-size: .9rem; font-weight: 700; }
        .product-customization-choice { display: flex; align-items: center; margin: 0; cursor: pointer; }
        .product-customization-choice input { position: absolute; opacity: 0; width: 0; height: 0; }
        .product-customization-choice span { display: inline-block; padding: .4rem .8rem; border: 1px solid #cbd5e1; border-radius: 20px; color: #475569; font-size: .8rem; font-weight: 500; transition: all 0.2s; }
        .product-customization-choice input:checked + span { background: #2563eb; color: #fff; border-color: #2563eb; box-shadow: 0 2px 4px rgba(37, 99, 235, .2); }
        .product-customization-choice:hover span { border-color: #2563eb; color: #2563eb; }
        .product-customization-choice input:checked:hover + span { background: #1d4ed8; }
        .product-custom-note-label { display: block; margin: .75rem 0 .4rem; color: #334155; font-size: .9rem; font-weight: 700; }
        .product-dialog textarea { width: 100%; min-height: 92px; resize: vertical; padding: .7rem .8rem; border: 1px solid #94a3b8; border-radius: 4px; background: #fff; color: #0f172a; font: inherit; line-height: 1.45; }
        .product-dialog-hint { margin: .35rem 0 0; color: #475569; font-size: .78rem; }
        .product-dialog-footer { display: flex; align-items: center; gap: .75rem; margin-top: auto; padding-top: 1.25rem; }
        .product-dialog-quantity { display: flex; flex: 0 0 auto; align-items: center; gap: .55rem; }
        .product-dialog-quantity button { width: 44px; height: 44px; border: 1px solid #94a3b8; border-radius: 4px; background: #fff; color: #0f172a; font: inherit; font-size: 1.1rem; font-weight: 700; cursor: pointer; }
        .product-dialog-quantity output { min-width: 1.4rem; text-align: center; font-weight: 700; }
        .product-dialog-add { min-height: 44px; flex: 1 1 auto; padding: .65rem .85rem; border: 1px solid #2563eb; border-radius: 4px; background: #2563eb; color: #fff; font: inherit; font-weight: 700; cursor: pointer; }
        .product-dialog-add:disabled { cursor: not-allowed; opacity: .55; }
        .cart-item-customization { display: block; margin-top: .35rem; color: #59483c; font-size: .78rem; white-space: pre-line; }
        @media (max-width: 620px) {
            .product-dialog { width: calc(100vw - 20px); max-height: calc(100dvh - 20px); }
            .product-dialog-shell { grid-template-columns: minmax(0, 1fr); }
            .product-dialog-image-wrap, .product-dialog-image { height: 190px; min-height: 190px; max-height: 190px; }
            .product-dialog-content { padding: 1rem 1.1rem 1.2rem; }
            .product-dialog-description { min-height: 0; }
            .product-dialog-footer { position: sticky; bottom: -1.2rem; margin: 1rem -1.1rem -1.2rem; padding: .8rem 1.1rem calc(.8rem + env(safe-area-inset-bottom)); border-top: 1px solid #cbd5e1; background: #fff; }
        }
        @media (max-width: 370px) {
            .product-dialog-footer { align-items: stretch; flex-direction: column; }
            .product-dialog-quantity { justify-content: center; }
        }
        @media (prefers-reduced-motion: reduce) {
            .product-card, .product-card *, #cart-drawer, #cart-drawer-backdrop { scroll-behavior: auto !important; transition-duration: .01ms !important; animation-duration: .01ms !important; }
        }
    </style>

    <dialog id="product-dialog" class="product-dialog" aria-labelledby="dialog-product-name">
        <div class="product-dialog-shell">
            <button type="button" id="product-dialog-close" class="product-dialog-close" data-dialog-close aria-label="Tutup detail produk">Tutup</button>
            <div class="product-dialog-image-wrap">
                <img id="dialog-product-image" class="product-dialog-image" alt="">
            </div>
            <div class="product-dialog-content">
                <p id="dialog-product-category" class="product-dialog-category"></p>
                <h2 id="dialog-product-name"></h2>
                <p id="dialog-product-description" class="product-dialog-description"></p>
                <div class="product-dialog-facts">
                    <strong id="dialog-product-price"></strong>
                    <span id="dialog-product-stock"></span>
                </div>
                <fieldset id="drink-customization-options" class="product-customization-options" hidden>
                    <legend>Penyesuaian minuman</legend>
                    <label class="product-customization-choice">
                        <input type="checkbox" name="drink_customization" value="Es sedikit">
                        <span>Es sedikit</span>
                    </label>
                    <label class="product-customization-choice">
                        <input type="checkbox" name="drink_customization" value="Gula aren dipisah">
                        <span>Gula aren dipisah</span>
                    </label>
                    <label class="product-customization-choice">
                        <input type="checkbox" name="drink_customization" value="Sirup dipisah">
                        <span>Sirup dipisah</span>
                    </label>
                </fieldset>
                <label class="product-custom-note-label" for="dialog-product-note">Catatan tambahan (opsional)</label>
                <textarea id="dialog-product-note" maxlength="180" rows="3" placeholder="Tulis permintaan lain untuk kasir"></textarea>
                <p class="product-dialog-hint">Pilihan dan catatan diteruskan ke kasir untuk item ini.</p>
                <div class="product-dialog-footer">
                    <div class="product-dialog-quantity" aria-label="Jumlah produk">
                        <button type="button" data-dialog-quantity="-1" aria-label="Kurangi jumlah">-</button>
                        <output id="dialog-product-quantity" aria-live="polite">1</output>
                        <button type="button" data-dialog-quantity="1" aria-label="Tambah jumlah">+</button>
                    </div>
                    <button type="button" class="product-dialog-add" id="dialog-add-product">Tambah ke keranjang</button>
                </div>
            </div>
        </div>
    </dialog>

    <!-- Cart Drawer (Slide Over) -->
    <div id="cart-drawer-backdrop" onclick="toggleCart()" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden transition-opacity duration-300"></div>
    <aside id="cart-drawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-white z-50 shadow-2xl flex flex-col translate-x-full transition-transform duration-300 ease-in-out">
        <!-- Header Cart -->
        <div class="p-5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary-600 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                </div>
                <div>
                    <h2 class="font-bold text-lg text-slate-900">Keranjang Saya</h2>
                    <p class="text-xs text-slate-500">Periksa daftar item sebelum konfirmasi</p>
                </div>
            </div>
            <button onclick="toggleCart()" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Cart Items List -->
        <div id="cart-items" class="flex-1 overflow-y-auto p-5 space-y-3"></div>

        <!-- Order Options & Summary Footer -->
        <div class="p-5 bg-slate-50 border-t border-slate-200 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Opsi Layanan & Meja:</label>
                <div class="grid grid-cols-2 gap-2">
                    <select id="order-type" onchange="toggleTableInput()" class="text-sm font-semibold bg-white border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="dine-in">Dine-in</option>
                        <option value="take-away">Takeaway</option>
                    </select>
                    <select id="table-number" required class="text-sm font-semibold bg-white border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-primary-500 focus:outline-none {{ $mejaAktif ? 'opacity-60 cursor-not-allowed' : '' }}" {{ $mejaAktif ? 'disabled' : '' }}>
                        @if(isset($mejas) && $mejas->isEmpty())
                            <option value="">Tidak ada meja</option>
                        @elseif(isset($mejas))
                            @foreach($mejas as $meja)
                                <option value="{{ $meja->id }}" {{ $mejaAktif && $mejaAktif->id === $meja->id ? 'selected' : '' }}>
                                    Meja {{ $meja->nomor_meja }} (Kap: {{ $meja->kapasitas }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Catatan Khusus (Opsional):</label>
                <input type="text" id="order-notes" placeholder="Contoh: Es sedikit, gula aren dipisah..."
                    class="w-full text-sm bg-white border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </div>

            <div class="pt-3 border-t border-slate-200 space-y-2 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal</span>
                    <span id="summary-subtotal" class="font-bold text-slate-900">Rp 0</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Pajak & Layanan (10%)</span>
                    <span id="summary-tax" class="font-bold text-slate-900">Rp 0</span>
                </div>
                <div class="flex justify-between text-lg font-bold text-slate-900 pt-2 border-t border-slate-200">
                    <span>Total Bayar</span>
                    <span id="summary-total" class="text-primary-600">Rp 0</span>
                </div>
            </div>

            <p id="checkout-error" class="hidden text-sm font-semibold text-red-700" role="alert"></p>
            <button id="checkout-btn" type="button" onclick="processOrder()" disabled
                class="w-full py-4 rounded-2xl bg-primary-600 text-white font-bold hover:bg-primary-700 disabled:bg-slate-300 disabled:cursor-not-allowed transition-all shadow-lg shadow-primary-600/25 active:scale-95">
                Kirim Pesanan ke Kasir
            </button>
        </div>
    </aside>

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
                    <span class="font-bold">PERKOCI EATERY</span>
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
            <button onclick="closeReceipt()" class="w-full py-3.5 rounded-2xl bg-primary-600 text-white text-sm font-bold hover:bg-primary-700 transition-all shadow-lg">
                Tutup & Pesan Baru
            </button>
        </div>
    </div>

    <!-- CLIENT JAVASCRIPT LOGIC -->
    <script>
        let cart = [];
        let nextCartLineId = 1;
        let activeProduct = null;
        let activeProductTrigger = null;

        document.getElementById('menu-grid').addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-product-open]');
            if (!trigger || trigger.disabled) return;

            const card = trigger.closest('.menu-card');
            activeProductTrigger = trigger;
            activeProduct = {
                produkId: Number(card.dataset.productId),
                name: card.dataset.productName,
                description: card.dataset.productDescription || 'Deskripsi belum tersedia.',
                category: card.dataset.productCategory,
                price: Number(card.dataset.productPrice),
                stock: Number(card.dataset.productStock),
                qty: 1,
            };

            document.getElementById('dialog-product-image').src = card.dataset.productImage;
            document.getElementById('dialog-product-image').alt = activeProduct.name;
            document.getElementById('dialog-product-category').textContent = activeProduct.category;
            document.getElementById('dialog-product-name').textContent = activeProduct.name;
            document.getElementById('dialog-product-description').textContent = activeProduct.description;
            document.getElementById('dialog-product-price').textContent = formatRupiah(activeProduct.price);
            document.getElementById('dialog-product-stock').textContent = `Stok ${activeProduct.stock}`;
            document.getElementById('dialog-product-note').value = '';
            document.querySelectorAll('input[name="drink_customization"]').forEach(option => {
                option.checked = false;
            });
            document.getElementById('drink-customization-options').hidden = !activeProduct.category.toLowerCase().includes('minuman');
            document.getElementById('dialog-product-quantity').value = '1';
            document.getElementById('dialog-product-quantity').textContent = '1';
            updateDialogQuantityControls();
            document.getElementById('product-dialog').showModal();
            document.getElementById('product-dialog-close').focus();
        });

        const productDialog = document.getElementById('product-dialog');

        function closeProductDialog() {
            if (productDialog.open) productDialog.close();
            activeProductTrigger?.focus();
        }

        document.querySelectorAll('[data-dialog-close]').forEach(button => {
            button.addEventListener('click', closeProductDialog);
        });

        document.querySelectorAll('[data-dialog-quantity]').forEach(button => {
            button.addEventListener('click', () => {
                if (!activeProduct) return;
                const productQtyInCart = cart
                    .filter(item => item.produkId === activeProduct.produkId)
                    .reduce((sum, item) => sum + item.qty, 0);
                const available = activeProduct.stock - productQtyInCart;
                activeProduct.qty = Math.max(1, Math.min(available, activeProduct.qty + Number(button.dataset.dialogQuantity)));
                document.getElementById('dialog-product-quantity').value = String(activeProduct.qty);
                document.getElementById('dialog-product-quantity').textContent = String(activeProduct.qty);
                updateDialogQuantityControls();
            });
        });

        document.getElementById('dialog-add-product').addEventListener('click', () => {
            if (!activeProduct) return;
            const customization = [
                ...Array.from(document.querySelectorAll('input[name="drink_customization"]:checked'), option => option.value),
                document.getElementById('dialog-product-note').value.trim(),
            ].filter(Boolean).join(', ');
            addToCart(activeProduct, activeProduct.qty, customization);
            closeProductDialog();
        });

        productDialog.addEventListener('click', event => {
            if (event.target === event.currentTarget) closeProductDialog();
        });

        productDialog.addEventListener('cancel', event => {
            event.preventDefault();
            closeProductDialog();
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && productDialog.open) {
                event.preventDefault();
                closeProductDialog();
            }
        }, true);

        productDialog.addEventListener('close', () => {
            activeProduct = null;
        });

        function updateDialogQuantityControls() {
            if (!activeProduct) return;
            const inCart = cart
                .filter(item => item.produkId === activeProduct.produkId)
                .reduce((sum, item) => sum + item.qty, 0);
            const available = activeProduct.stock - inCart;
            document.querySelector('[data-dialog-quantity="-1"]').disabled = activeProduct.qty <= 1;
            document.querySelector('[data-dialog-quantity="1"]').disabled = activeProduct.qty >= available;
            document.getElementById('dialog-add-product').disabled = available < 1 || activeProduct.qty < 1;
        }

        function toggleCart() {
            const drawer = document.getElementById('cart-drawer');
            const backdrop = document.getElementById('cart-drawer-backdrop');
            const isOpen = !drawer.classList.contains('translate-x-full');
            if (isOpen) {
                drawer.classList.add('translate-x-full');
                backdrop.classList.add('hidden');
            } else {
                drawer.classList.remove('translate-x-full');
                backdrop.classList.remove('hidden');
            }
        }

        function toggleTableInput() {
            const type = document.getElementById('order-type').value;
            const tableSelect = document.getElementById('table-number');
            if (type === 'take-away') {
                tableSelect.disabled = true;
                tableSelect.classList.add('opacity-40');
            } else {
                tableSelect.disabled = false;
                tableSelect.classList.remove('opacity-40');
            }
            renderCart();
        }

        function formatRupiah(num) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
        }

        function escapeHtml(value) {
            return String(value).replace(/[&<>"']/g, character => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            })[character]);
        }

        function syncMenuStockButtons() {
            document.querySelectorAll('.menu-card [data-product-open]').forEach(button => {
                const card = button.closest('.menu-card');
                const inCart = cart
                    .filter(product => product.produkId === Number(card.dataset.productId))
                    .reduce((sum, product) => sum + product.qty, 0);
                button.disabled = Number(card.dataset.productStock) <= inCart;
            });
        }

        function syncCheckoutState() {
            const orderType = document.getElementById('order-type').value;
            const tableId = document.getElementById('table-number').value;
            document.getElementById('checkout-btn').disabled = cart.length === 0 || (orderType === 'dine-in' && !tableId);
        }

        function addToCart(product, quantity, customization) {
            const inCart = cart
                .filter(item => item.produkId === product.produkId)
                .reduce((sum, item) => sum + item.qty, 0);
            const available = product.stock - inCart;
            const addQuantity = Math.min(quantity, available);
            if (addQuantity < 1) return;

            const item = cart.find(item => item.produkId === product.produkId && item.customization === customization);
            if (item) {
                item.qty += addQuantity;
            } else {
                cart.push({
                    cartLineId: nextCartLineId++,
                    produkId: product.produkId,
                    name: product.name,
                    price: product.price,
                    category: product.category,
                    stock: product.stock,
                    qty: addQuantity,
                    customization,
                });
            }
            renderCart();
            updateHeaderCart();
        }

        function updateQty(cartLineId, delta) {
            const item = cart.find(product => product.cartLineId === cartLineId);
            if (!item) return;
            const otherQuantity = cart
                .filter(product => product.produkId === item.produkId && product.cartLineId !== item.cartLineId)
                .reduce((sum, product) => sum + product.qty, 0);
            item.qty = Math.min(item.qty + delta, item.stock - otherQuantity);
            if (item.qty <= 0) { cart = cart.filter(product => product.cartLineId !== cartLineId); }
            renderCart();
            updateHeaderCart();
        }

        function updateHeaderCart() {
            const badge = document.getElementById('header-cart-badge');
            const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
            if (totalQty > 0) {
                badge.textContent = totalQty;
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        }

        function renderCart() {
            const container = document.getElementById('cart-items');
            const checkoutBtn = document.getElementById('checkout-btn');

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-16 text-slate-400 space-y-2">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        </div>
                        <p class="font-bold text-slate-900">Keranjang masih kosong</p>
                        <p class="text-sm">Pilih menu favorit Anda dari katalog</p>
                    </div>`;
                document.getElementById('summary-subtotal').textContent = 'Rp 0';
                document.getElementById('summary-tax').textContent = 'Rp 0';
                document.getElementById('summary-total').textContent = 'Rp 0';
                syncMenuStockButtons();
                syncCheckoutState();
                return;
            }

            let html = '';
            let subtotal = 0;

            cart.forEach(item => {
                const total = item.price * item.qty;
                subtotal += total;
                html += `
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200">
                        <div class="flex-1 min-w-0 pr-3">
                            <h4 class="font-bold text-slate-900 text-sm truncate">${escapeHtml(item.name)}</h4>
                            <span class="text-xs text-slate-500">${formatRupiah(item.price)}</span>
                            ${item.customization ? `<span class="cart-item-customization">${escapeHtml(item.customization)}</span>` : ''}
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border border-slate-300 rounded-lg bg-white overflow-hidden">
                                <button type="button" aria-label="Kurangi ${escapeHtml(item.name)}" data-cart-quantity="-1" data-cart-line="${item.cartLineId}" class="min-w-11 min-h-11 px-3 py-1.5 hover:bg-slate-50 font-bold text-slate-700">-</button>
                                <span class="px-2 font-bold text-slate-900 text-sm">${item.qty}</span>
                                <button type="button" aria-label="Tambah ${escapeHtml(item.name)}" data-cart-quantity="1" data-cart-line="${item.cartLineId}" ${item.qty >= item.stock ? 'disabled' : ''} class="min-w-11 min-h-11 px-3 py-1.5 hover:bg-slate-50 font-bold text-slate-700 disabled:opacity-40 disabled:cursor-not-allowed">+</button>
                            </div>
                            <span class="text-sm font-bold text-slate-900 w-20 text-right">${formatRupiah(total)}</span>
                        </div>
                    </div>`;
            });

            container.innerHTML = html;
            container.querySelectorAll('[data-cart-quantity]').forEach(button => {
                button.addEventListener('click', () => updateQty(Number(button.dataset.cartLine), Number(button.dataset.cartQuantity)));
            });
            const tax = Math.round(subtotal * 0.1);
            const grandTotal = subtotal + tax;

            document.getElementById('summary-subtotal').textContent = formatRupiah(subtotal);
            document.getElementById('summary-tax').textContent = formatRupiah(tax);
            document.getElementById('summary-total').textContent = formatRupiah(grandTotal);
            syncMenuStockButtons();
            syncCheckoutState();
        }

        async function processOrder() {
            if (cart.length === 0) return;

            const orderType = document.getElementById('order-type').value;
            const tableSelect = document.getElementById('table-number');
            const notes = document.getElementById('order-notes').value.trim();
            const checkout = document.getElementById('checkout-btn');
            const error = document.getElementById('checkout-error');
            error.classList.add('hidden');
            checkout.disabled = true;
            checkout.textContent = 'Mengirim pesanan...';

            try {
                const response = await fetch('/api/transaksis', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        meja_id: orderType === 'dine-in' ? Number(tableSelect.value) : null,
                        tipe_pesanan: orderType,
                        catatan: notes || null,
                        items: cart.map(item => ({
                            produk_id: item.produkId,
                            qty: item.qty,
                            customization: item.customization || null,
                        })),
                    }),
                });
                const result = await response.json().catch(() => ({}));

                if (!response.ok) {
                    const validationMessage = Object.values(result.errors || {}).flat()[0];
                    throw new Error(validationMessage || result.message || 'Pesanan gagal dikirim. Coba lagi.');
                }

                const order = result.data;
                document.getElementById('receipt-date').textContent = new Date(order.waktu_transaksi).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                document.getElementById('receipt-number').textContent = `#${order.id}`;
                document.getElementById('receipt-order-type').textContent = orderType === 'dine-in' ? 'Dine-in' : 'Takeaway';
                document.getElementById('receipt-target').textContent = orderType === 'dine-in'
                    ? tableSelect.options[tableSelect.selectedIndex].textContent.trim()
                    : 'BAWA PULANG';

                let itemsList = '';
                cart.forEach(item => {
                    itemsList += `<div class="flex justify-between gap-3"><span>${item.qty}x ${escapeHtml(item.name)}${item.customization ? `<br><small>${escapeHtml(item.customization)}</small>` : ''}</span><span>${formatRupiah(item.price * item.qty)}</span></div>`;
                });
                document.getElementById('receipt-items-list').innerHTML = itemsList;
                document.getElementById('receipt-grand-total').textContent = formatRupiah(order.total_harga);

                const notesDisplay = document.getElementById('receipt-notes-display');
                if (notes) {
                    notesDisplay.textContent = 'Catatan: "' + notes + '"';
                    notesDisplay.classList.remove('hidden');
                } else {
                    notesDisplay.classList.add('hidden');
                }

                const itemCustomizations = cart.map(item => item.customization).filter(Boolean);
                if (itemCustomizations.length) {
                    notesDisplay.textContent = [notes ? `Catatan pesanan: ${notes}` : '', ...itemCustomizations.map((value, index) => `Kustomisasi item ${index + 1}: ${value}`)].filter(Boolean).join(' | ');
                    notesDisplay.classList.remove('hidden');
                }

                toggleCart();
                const receiptModal = document.getElementById('receipt-modal');
                receiptModal.classList.remove('hidden');
                receiptModal.classList.add('flex');
            } catch (requestError) {
                error.textContent = requestError.message;
                error.classList.remove('hidden');
            } finally {
                checkout.textContent = 'Kirim Pesanan ke Kasir';
                syncCheckoutState();
            }
        }

        function closeReceipt() {
            const receiptModal = document.getElementById('receipt-modal');
            receiptModal.classList.add('hidden');
            receiptModal.classList.remove('flex');
            cart = [];
            document.getElementById('order-notes').value = '';
            nextCartLineId = 1;
            renderCart();
            updateHeaderCart();
        }

        function setCategory(category) {
            const buttons = document.querySelectorAll('.category-btn');
            buttons.forEach(btn => {
                btn.classList.remove('bg-primary-600', 'text-white', 'shadow-md');
                btn.classList.add('bg-white', 'text-slate-600', 'border', 'border-slate-200');
            });
            event.currentTarget.classList.remove('bg-white', 'text-slate-600', 'border', 'border-slate-200');
            event.currentTarget.classList.add('bg-primary-600', 'text-white', 'shadow-md');

            const cards = document.querySelectorAll('.menu-card');
            let visibleCount = 0;
            cards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                if (category === 'semua' || cardCategory === category) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            document.getElementById('no-menu').classList.toggle('hidden', visibleCount > 0);
        }

        function filterMenu() {
            // Support both desktop and mobile search inputs
            const queryDesktop = document.getElementById('menu-search')?.value.toLowerCase() || '';
            const queryMobile = document.getElementById('menu-search-mobile')?.value.toLowerCase() || '';
            const query = queryDesktop || queryMobile;

            // Sync inputs
            if(document.getElementById('menu-search')) document.getElementById('menu-search').value = query;
            if(document.getElementById('menu-search-mobile')) document.getElementById('menu-search-mobile').value = query;

            const cards = document.querySelectorAll('.menu-card');
            let visibleCount = 0;
            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                if (name.includes(query)) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            document.getElementById('no-menu').classList.toggle('hidden', visibleCount > 0);
        }

        // Initialize
        document.getElementById('table-number').addEventListener('change', syncCheckoutState);
        renderCart();
        updateHeaderCart();
    </script>
</body>
</html>