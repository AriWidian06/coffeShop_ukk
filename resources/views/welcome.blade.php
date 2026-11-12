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
<body class="antialiased text-slate-800 bg-slate-50 pb-24">

    <!-- Mobile Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200">
        <div class="px-4 py-3 max-w-md mx-auto">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-primary-600 text-white flex items-center justify-center shadow-md shadow-primary-600/20 overflow-hidden shrink-0">
                        <img
                            src="{{ asset('images/logo.jpeg') }}"
                            alt="Logo Bagi Kopi"
                            class="w-full h-full object-cover"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >
                        <svg class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 10h-1.26A8 8 0 109 20h9a2 2 0 002-2v-6a2 2 0 00-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 id="header-title" class="font-extrabold text-base text-slate-900 leading-tight">Bagi Kopi</h1>
                        <p id="header-subtitle" class="text-[10px] text-slate-500 font-bold tracking-wider">ARTISANAL BREW • CATALOG</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if(isset($mejaAktif))
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-primary-50 rounded-xl border border-primary-200">
                            <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            <span class="text-xs font-bold text-primary-700">Meja #{{ $mejaAktif->nomor_meja }}</span>
                        </div>
                    @endif
                    <button class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Search (Hanya tampil di tab Pesan) -->
            <div id="search-container" class="relative">
                <input type="text" id="menu-search-mobile" onkeyup="filterMenu()"
                    placeholder="Cari kopi, pastry, atau manual brew..."
                    class="w-full pl-10 pr-10 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <button class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="max-w-md mx-auto px-4 py-5 space-y-5">

        <!-- ================= TAB PESAN (KATALOG MENU) ================= -->
        <div id="tab-pesan" class="space-y-5">
            @if(isset($mejaAktif))
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-slate-900">Meja #{{ $mejaAktif->nomor_meja }}</h3>
                            <span class="px-2 py-0.5 bg-primary-600 text-white text-[10px] font-extrabold rounded-md">DINE-IN</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Bagi Kopi Main Hall • Lantai 1</p>
                        <p class="text-xs text-slate-500 font-semibold mt-1">
                            Tempat: {{ $mejaAktif->nomor_meja <= 10 ? 'Lt. 1' : 'Lt. 2 Slow Bar' }} &nbsp;•&nbsp; Kapasitas: {{ $mejaAktif->kapasitas }} orang
                        </p>
                    </div>
                </div>

            </div>
            @endif

            <!-- Category Pills -->
            <div>
                <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar pb-1">
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
            <div class="flex items-center justify-between pt-1">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Katalog Pilihan</h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">Disangrai teliti di roastery harian Bagi Kopi</p>
                </div>
                <span class="px-2.5 py-1 bg-slate-200/70 text-slate-700 text-xs font-extrabold rounded-lg">{{ $produks->count() }} SAJIAN</span>
            </div>

            <!-- Menu List -->
            <div id="menu-grid" class="space-y-3">
                @forelse($produks as $produk)
                    @php
                        $categorySlug = \Illuminate\Support\Str::slug($produk->kategoriProduk->nama_kategori ?? 'lainnya', '-');
                        $dataName = strtolower($produk->nama_produk);
                        $hargaFormatted = number_format($produk->harga_jual, 0, ',', '.');
                        $imageUrl = $produk->gambar ? asset('storage/' . $produk->gambar) : 'https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=600&q=80';

                        $badge = null;
                        $badgeColor = 'bg-blue-600';
                        if (str_contains(strtolower($produk->nama_produk), 'aren latte')) {
                            $badge = 'BEST SELLER';
                            $badgeColor = 'bg-blue-600';
                        } elseif (str_contains(strtolower($produk->kategoriProduk->nama_kategori ?? ''), 'manual')) {
                            $badge = 'MANUAL BREW';
                            $badgeColor = 'bg-amber-600';
                        } elseif ($produk->stock < 5 && $produk->stock > 0) {
                            $badge = 'SISA ' . $produk->stock . ' BOTOL';
                            $badgeColor = 'bg-orange-600';
                        } elseif ($produk->stock < 1) {
                            $badge = 'HABIS';
                            $badgeColor = 'bg-red-500';
                        }
                    @endphp

                    <article class="menu-card product-card bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-3"
                        data-category="{{ $categorySlug }}" data-name="{{ $dataName }}"
                        data-product-id="{{ $produk->id }}" data-product-name="{{ $produk->nama_produk }}"
                        data-product-description="{{ $produk->deskripsi ?? '' }}"
                        data-product-category="{{ $produk->kategoriProduk->nama_kategori ?? 'Umum' }}"
                        data-product-price="{{ $produk->harga_jual }}" data-product-stock="{{ $produk->stock }}"
                        data-product-customizable="{{ $produk->is_customizable ? 'true' : 'false' }}"
                        data-product-options="{{ json_encode($produk->opsis) }}"
                        data-product-image="{{ $imageUrl }}">

                        <div class="flex gap-3">
                            <div class="w-24 h-24 rounded-xl bg-slate-100 overflow-hidden shrink-0 relative shadow-inner">
                                <img src="{{ $imageUrl }}" alt="{{ $produk->nama_produk }}" class="w-full h-full object-cover">
                                @if($badge)
                                    <span class="absolute top-1 left-1 px-1.5 py-0.5 {{ $badgeColor }} text-white text-[9px] font-extrabold rounded-md shadow-sm">{{ $badge }}</span>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0 flex flex-col justify-between">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-sm leading-tight mb-1">{{ $produk->nama_produk }}</h3>
                                    <p class="text-[11px] text-slate-500 italic line-clamp-2 leading-relaxed">{{ $produk->deskripsi ?? 'Produk berkualitas dari Bagi Kopi.' }}</p>
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="font-extrabold text-primary-600 text-sm">Rp {{ $hargaFormatted }}</span>
                                    @if($produk->stock > 0)
                                        <button type="button" onclick="quickAddToCart({{ $produk->id }}, '{{ addslashes($produk->nama_produk) }}', {{ $produk->harga_jual }}, {{ $produk->stock }})"
                                            class="w-8 h-8 rounded-xl bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 active:scale-95 shadow-md shadow-primary-600/20 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    @else
                                        <span class="text-[10px] font-bold px-2 py-1 bg-red-50 text-red-600 rounded-lg">Habis</span>
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

            <!-- Banner Customization Info Card -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-xs">Pilihan Susu Nabati & Level Es</h4>
                    <p class="text-[11px] text-slate-500 leading-snug">Kustomisasi oatside, almond milk, dan gula aren tersedia di keranjang.</p>
                </div>
            </div>
        </div>

        <!-- ================= TAB STATUS PESANAN ================= -->
        @include('user.status')

    </main>

    <!-- FLOATING CART BOTTOM BAR -->
    <div id="floating-cart-bar" class="fixed bottom-16 left-4 right-4 max-w-md mx-auto z-40 hidden transition-all duration-300 transform translate-y-2 opacity-0">
        <div onclick="toggleCart()" class="bg-primary-600 hover:bg-primary-700 text-white rounded-2xl p-3.5 shadow-xl shadow-primary-600/30 flex items-center justify-between cursor-pointer transition-all active:scale-95">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center relative">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span id="floating-cart-badge" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-amber-500 text-white text-[10px] font-extrabold rounded-full flex items-center justify-center border-2 border-primary-600">0</span>
                </div>
                <div>
                    <div id="floating-cart-title" class="text-[11px] font-bold text-primary-100 tracking-wide">0 MENU DIPILIH</div>
                    <div id="floating-cart-total" class="text-base font-extrabold">Rp 0</div>
                </div>
            </div>
            <div class="flex items-center gap-1.5 bg-white/15 px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-white/25 transition">
                <span>Lihat Keranjang</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 px-6 py-2 z-50 safe-area-pb max-w-md mx-auto">
        <div class="flex items-center justify-around">
            <button onclick="switchTab('pesan')" id="nav-pesan" class="flex flex-col items-center gap-1 py-1 px-3 text-primary-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-[10px] font-bold">Pesan</span>
            </button>
            <button onclick="toggleCart()" class="flex flex-col items-center gap-1 py-1 px-3 text-slate-400 relative transition hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span class="text-[10px] font-bold">Keranjang</span>
                <span id="bottom-nav-badge" class="absolute -top-1 right-2 w-4 h-4 bg-amber-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center hidden">0</span>
            </button>
            <button onclick="switchTab('status')" id="nav-status" class="flex flex-col items-center gap-1 py-1 px-3 text-slate-400 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-[10px] font-bold">Status</span>
            </button>
        </div>
    </nav>

    <!-- ================= KERANJANG & CHECKOUT PAGE ================= -->
    @include('user.keranjang')
    <!-- ================= QRIS PAYMENT PAGE ================= -->
    @include('user.qr')
    <!-- ================= Mobile Customization Modal ================= -->
    @include('user.costume')
    <!-- Mobile Customization Modal -->


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
        let mobileCustomOptions = { dynamicOptions: {} };
        let currentOrderType = 'dine-in';
        let currentCategory = 'semua';
        let activeTab = 'pesan';
        let appliedPromo = null;
        let promoSessionId = localStorage.getItem('bk_guest_session');
        if (!promoSessionId) {
            promoSessionId = 'BK-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
            localStorage.setItem('bk_guest_session', promoSessionId);
        }
        const qrLockedMejaId = {{ $mejaAktif ? $mejaAktif->id : 'null' }};

        // --- LIVE ORDER STATUS ---
        let myOrderPollTimer = null;
        let myOrderLoading = false;
        let lastOrderWasCompleted = false;
        const COMPLETED_MESSAGE = 'Pesanan sebelumnya sudah selesai. Silakan buat pesanan baru.';
        const CANCELLED_MESSAGE = 'Pesanan Anda dibatalkan karena pembayaran tidak berhasil. Silakan buat pesanan baru.';

        function stopMyOrderPolling() {
            if (myOrderPollTimer) {
                clearInterval(myOrderPollTimer);
                myOrderPollTimer = null;
            }
        }

        function startMyOrderPolling() {
            stopMyOrderPolling();
            myOrderPollTimer = setInterval(() => {
                if (activeTab !== 'status' || document.hidden) return;
                refreshMyOrder(false);
            }, 5000);
        }

        function setStatusRefreshSpinning(spinning) {
            const icon = document.getElementById('status-refresh-icon');
            if (icon) icon.classList.toggle('animate-spin', spinning);
            const btn = document.getElementById('status-refresh-btn');
            if (btn) btn.disabled = spinning;
        }

        async function refreshMyOrder(showLoading = false) {
            if (myOrderLoading) return;
            myOrderLoading = true;
            setStatusRefreshSpinning(true);

            const loadingEl = document.getElementById('status-loading');
            const contentEl = document.getElementById('status-content');
            const emptyEl = document.getElementById('status-empty');

            if (showLoading && contentEl.classList.contains('hidden')) {
                loadingEl.classList.remove('hidden');
                emptyEl.classList.add('hidden');
            }

            try {
                const params = new URLSearchParams();
                if (promoSessionId) params.set('session_id', promoSessionId);
                if (qrLockedMejaId) params.set('meja_id', qrLockedMejaId);

                const response = await fetch('/api/pesanan-saya?' + params.toString(), {
                    headers: { 'Accept': 'application/json' },
                });
                const result = await response.json().catch(() => ({}));

                if (response.status === 404) {
                    renderMyOrder(null, lastOrderWasCompleted ? COMPLETED_MESSAGE : undefined);
                    return;
                }

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Gagal memuat status pesanan.');
                }

                // Backend mengirim data = null ketika pesanan terakhir sudah selesai.
                // Halaman status langsung di-reset ke tampilan "belum ada pesanan aktif".
                if (!result.data) {
                    lastOrderWasCompleted = !!result.completed;
                    renderMyOrder(null, result.message || (lastOrderWasCompleted ? COMPLETED_MESSAGE : undefined));
                    return;
                }

                renderMyOrder(result.data);
            } catch (error) {
                if (contentEl.classList.contains('hidden')) {
                    renderMyOrder(null, error.message || 'Gagal memuat status. Periksa koneksi Anda.');
                }
            } finally {
                myOrderLoading = false;
                setStatusRefreshSpinning(false);
            }
        }

        function applyStatusStep(step, status) {
            for (let i = 1; i <= 4; i++) {
                const dot = document.getElementById('status-dot-' + i);
                const label = document.getElementById('status-step-label-' + i);
                if (!dot || !label) continue;

                if (i < step || (i === step && status === 'completed')) {
                    dot.className = 'w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-md shadow-primary-600/30 font-bold';
                    label.className = 'text-xs font-bold text-slate-900 mt-2';
                } else if (i === step) {
                    dot.className = 'w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-md shadow-primary-600/30 font-bold ring-4 ring-primary-100 animate-pulse';
                    label.className = 'text-xs font-extrabold text-primary-600 mt-2';
                } else {
                    dot.className = 'w-10 h-10 rounded-full bg-slate-100 border-2 border-slate-200 text-slate-400 flex items-center justify-center font-bold';
                    label.className = 'text-xs font-medium text-slate-400 mt-2';
                }
            }

            const fill = document.getElementById('status-progress-fill');
            if (fill) fill.style.width = (((step - 1) / 3) * 100) + '%';
            const badge = document.getElementById('status-step-badge');
            if (badge) badge.textContent = 'Langkah ' + Math.min(step, 4) + ' dari 4';
        }

        function clearStatusOrderDetails() {
            const itemsEl = document.getElementById('status-items');
            if (itemsEl) itemsEl.innerHTML = '';

            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value;
            };

            setText('status-item-count', '0 Item');
            setText('status-total', 'Rp 0');
            setText('status-order-number', '#-');
            setText('status-order-time', '-');
            setText('status-order-table', '-');
            setText('status-payment-tag', 'Menunggu Pembayaran');
            setText('status-current-label', 'Memuat...');
            setText('status-current-desc', 'Mohon tunggu sebentar.');

            document.getElementById('status-notes')?.classList.add('hidden');
            document.getElementById('status-live-dot')?.classList.remove('hidden');
        }

        function renderMyOrder(order, emptyMessage) {
            const loadingEl = document.getElementById('status-loading');
            const contentEl = document.getElementById('status-content');
            const emptyEl = document.getElementById('status-empty');
            const titleEl = document.getElementById('status-page-title');
            const subtitleEl = document.getElementById('status-page-subtitle');
            const emptyMessageEl = document.getElementById('status-empty-message');

            loadingEl.classList.add('hidden');

            if (!order) {
                contentEl.classList.add('hidden');
                emptyEl.classList.remove('hidden');
                titleEl.textContent = 'Status Pesanan';
                subtitleEl.textContent = 'Belum ada pesanan aktif';
                emptyMessageEl.textContent = emptyMessage || 'Pesanan yang sedang diproses akan muncul di sini secara otomatis.';
                clearStatusOrderDetails();
                return;
            }

            // Pesanan sudah selesai -> halaman status langsung di-reset otomatis
            // supaya pelanggan berikutnya tidak melihat status pesanan lama.
            // Polling tetap jalan supaya pesanan baru langsung muncul otomatis.
            if (order.status_pesanan === 'completed') {
                lastOrderWasCompleted = true;
                renderMyOrder(null, COMPLETED_MESSAGE);
                return;
            }

            // Pesanan dibatalkan (mis. QRIS expired/ditolak) -> tampilkan kondisi
            // batal, bukan "Menunggu Konfirmasi" yang menyesatkan pelanggan.
            if (order.status_pesanan === 'cancelled') {
                lastOrderWasCompleted = false;
                renderMyOrder(null, CANCELLED_MESSAGE);
                return;
            }

            lastOrderWasCompleted = false;

            emptyEl.classList.add('hidden');
            contentEl.classList.remove('hidden');

            const statusMap = {
                pending: {
                    desc: 'Pesanan diterima dan menunggu konfirmasi kasir.',
                    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                },
                prosesed: {
                    desc: 'Pesanan sedang diracik oleh barista kami.',
                    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                },
                ready: {
                    desc: 'Pesanan siap diantar ke meja Anda.',
                    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
                },
                completed: {
                    desc: 'Pesanan telah selesai. Terima kasih sudah mampir!',
                    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>',
                },
            };
            const meta = statusMap[order.status_pesanan] || statusMap.pending;
            const isTakeAway = order.tipe_pesanan === 'take-away';
            const tableLabel = order.nomor_meja ? 'Meja ' + order.nomor_meja : 'Take Away';

            titleEl.textContent = 'Status Pesanan (' + tableLabel + ')';
            subtitleEl.textContent = '#' + order.id + ' • ' + order.status_label;
            document.getElementById('status-current-label').textContent = order.status_label;
            document.getElementById('status-current-desc').textContent = meta.desc;
            document.getElementById('status-current-icon').innerHTML = meta.icon;
            document.getElementById('status-order-type').textContent = isTakeAway ? 'Take Away' : 'Dine-in';
            document.getElementById('status-live-dot').classList.toggle('hidden', order.status_pesanan === 'completed');

            applyStatusStep(order.status_step || 1, order.status_pesanan);

            const items = order.items || [];
            const totalQty = items.reduce((total, item) => total + Number(item.qty || 0), 0);
            document.getElementById('status-item-count').textContent = totalQty + ' Item';
            document.getElementById('status-total').textContent = formatRupiah(order.total_harga);
            document.getElementById('status-order-number').textContent = '#' + order.id;
            document.getElementById('status-order-time').textContent = order.waktu_transaksi
                ? new Date(order.waktu_transaksi).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta' }) + ' WIB'
                : '-';
            document.getElementById('status-order-table').textContent = tableLabel;

            const payment = order.pembayaran;
            let paymentText = 'Menunggu Pembayaran';
            if (payment && payment.status_pembayaran === 'paid') {
                paymentText = 'Lunas via ' + (payment.metode_pembayaran || 'Kasir');
            } else if (payment && payment.metode_pembayaran) {
                paymentText = 'Menunggu Pembayaran ' + payment.metode_pembayaran;
            } else if (order.status_pesanan === 'completed') {
                paymentText = 'Pembayaran Selesai';
            }
            document.getElementById('status-payment-tag').textContent = paymentText;

            document.getElementById('status-items').innerHTML = items.length
                ? items.map((item, index) => {
                    const image = item.gambar
                        ? '<img src="' + escapeHtml(item.gambar) + '" alt="' + escapeHtml(item.nama_produk) + '" class="w-14 h-14 rounded-xl object-cover shrink-0 border border-slate-100">'
                        : '<div class="w-14 h-14 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M6 8v10a2 2 0 002 2h8a2 2 0 002-2V8M9 8V6a3 3 0 016 0v2"/></svg></div>';
                    return '<div class="flex items-center gap-3' + (index > 0 ? ' border-t border-slate-100 pt-3' : '') + '">' + image +
                        '<div class="flex-1 min-w-0"><h4 class="font-bold text-slate-900 text-xs leading-tight">' + item.qty + 'x ' + escapeHtml(item.nama_produk) + '</h4>' +
                        (item.customization ? '<p class="text-[11px] text-slate-500 mt-0.5">' + escapeHtml(item.customization) + '</p>' : '') +
                        '</div><span class="text-xs font-bold text-slate-900 shrink-0">' + formatRupiah(item.subtotal) + '</span></div>';
                }).join('')
                : '<p class="text-xs text-slate-500">Rincian menu tidak tersedia.</p>';

            const notesEl = document.getElementById('status-notes');
            if (order.catatan) {
                document.getElementById('status-notes-text').textContent = order.catatan;
                notesEl.classList.remove('hidden');
            } else {
                notesEl.classList.add('hidden');
            }

            document.getElementById('status-last-update').textContent = 'Status diperbarui ' +
                new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: 'Asia/Jakarta' }) +
                ' WIB • otomatis setiap 5 detik';
        }

        // --- TAB SWITCHING FUNCTIONS ---
        function switchTab(tabName) {
            activeTab = tabName;
            const tabPesan = document.getElementById('tab-pesan');
            const tabStatus = document.getElementById('tab-status');
            const searchContainer = document.getElementById('search-container');
            const headerTitle = document.getElementById('header-title');
            const headerSubtitle = document.getElementById('header-subtitle');

            const navPesan = document.getElementById('nav-pesan');
            const navStatus = document.getElementById('nav-status');

            if (tabName === 'status') {
                tabPesan.classList.add('hidden');
                tabStatus.classList.remove('hidden');
                searchContainer.classList.add('hidden');
                headerTitle.textContent = 'Bagi Kopi';
                headerSubtitle.textContent = 'ARTISANAL BREW • ORDER STATUS';

                navStatus.classList.remove('text-slate-400');
                navStatus.classList.add('text-primary-600');
                navPesan.classList.remove('text-primary-600');
                navPesan.classList.add('text-slate-400');
                window.scrollTo({ top: 0, behavior: 'smooth' });
                startMyOrderPolling();
                refreshMyOrder(true);
            } else {
                tabStatus.classList.add('hidden');
                tabPesan.classList.remove('hidden');
                searchContainer.classList.remove('hidden');
                headerTitle.textContent = 'Bagi Kopi';
                headerSubtitle.textContent = 'ARTISANAL BREW • CATALOG';

                navPesan.classList.remove('text-slate-400');
                navPesan.classList.add('text-primary-600');
                navStatus.classList.remove('text-primary-600');
                navStatus.classList.add('text-slate-400');
                window.scrollTo({ top: 0, behavior: 'smooth' });
                stopMyOrderPolling();
            }
        }

        // --- MOBILE CUSTOMIZATION FUNCTIONS ---
        function openMobileCustomization(produkId, name, price, stock, image, description, tags = [], options = []) {
            mobileCustomProduct = { produkId, name, basePrice: price, stock, image, description, options: options };
            mobileCustomQty = 1;
            mobileCustomOptions = { selectedOptions: {} };

            document.getElementById('mobile-custom-image').src = image;
            document.getElementById('mobile-custom-name').textContent = name;
            document.getElementById('mobile-custom-description').textContent = description || 'Produk berkualitas dari Bagi Kopi.';
            document.getElementById('mobile-custom-price').textContent = formatRupiah(price);
            document.getElementById('mobile-custom-notes').value = '';

            renderCustomizationGroups();
            updateMobileTotal();
            document.getElementById('mobile-customization-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function renderCustomizationGroups() {
            const container = document.getElementById('customization-groups-container');
            container.innerHTML = '';

            const options = mobileCustomProduct.options || [];
            const groups = {};

            options.forEach(opt => {
                const cat = opt.kategori || 'Pilihan Tambahan';
                if (!groups[cat]) groups[cat] = { tipe: opt.tipe || 'multiple', is_required: opt.is_required || false, items: [] };
                groups[cat].items.push(opt);
            });

            Object.keys(groups).forEach(catName => {
                const group = groups[catName];
                const groupDiv = document.createElement('div');
                groupDiv.className = 'space-y-3';

                const header = document.createElement('div');
                header.className = 'flex items-center justify-between mb-1';
                header.innerHTML = `
                    <label class="text-sm font-bold text-slate-900">${catName}</label>
                    <span class="text-[10px] font-bold ${group.is_required ? 'text-primary-600 bg-primary-50' : 'text-slate-500 bg-slate-100'} px-2 py-0.5 rounded-full border ${group.is_required ? 'border-primary-100' : 'border-slate-200'}">
                        ${group.is_required ? 'Wajib' : group.tipe === 'single' ? 'Pilih 1' : 'Bisa Lebih dari 1'}
                    </span>
                `;
                groupDiv.appendChild(header);

                const itemsDiv = document.createElement('div');
                if (group.tipe === 'single' && group.items.length === 2) {
                    itemsDiv.className = 'grid grid-cols-2 gap-2';
                } else {
                    itemsDiv.className = 'space-y-2';
                }

                group.items.forEach(opt => {
                    const isSelected = mobileCustomOptions.selectedOptions[catName] === opt.id || (group.tipe === 'multiple' && mobileCustomOptions.selectedOptions[`opt-${opt.id}`]);
                    const priceText = opt.harga_tambahan > 0 ? `+${formatRupiah(opt.harga_tambahan)}` : '';

                    const btn = document.createElement('button');
                    btn.type = 'button';

                    if (group.tipe === 'single' && group.items.length === 2) {
                        btn.className = `py-3 px-4 rounded-xl border-2 font-bold text-sm transition-all ${isSelected ? 'border-primary-600 bg-primary-50 text-primary-700' : 'border-slate-200 bg-white text-slate-600'}`;
                        btn.innerHTML = `<span class="block text-center w-full">${opt.nama_opsi}</span>`;
                    } else {
                        btn.className = `w-full py-3 px-4 rounded-xl border-2 text-left flex items-center justify-between transition-all ${isSelected ? 'border-primary-600 bg-primary-50 text-primary-700' : 'border-slate-200 bg-white text-slate-600'}`;
                        btn.innerHTML = `
                            <span class="font-bold text-sm">${opt.nama_opsi}</span>
                            <div class="flex items-center gap-2">
                                ${priceText ? `<span class="text-xs font-bold">${priceText}</span>` : ''}
                                ${isSelected ? '<svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>' : ''}
                            </div>
                        `;
                    }

                    btn.onclick = () => toggleOption(catName, opt);
                    itemsDiv.appendChild(btn);
                });

                groupDiv.appendChild(itemsDiv);
                container.appendChild(groupDiv);
            });
        }

        function toggleOption(catName, opt) {
            const groupTipe = mobileCustomProduct.options.find(o => o.kategori === catName)?.tipe || 'multiple';

            if (groupTipe === 'single') {
                if (mobileCustomOptions.selectedOptions[catName] === opt.id) {
                    delete mobileCustomOptions.selectedOptions[catName];
                } else {
                    mobileCustomOptions.selectedOptions[catName] = opt.id;
                }
            } else {
                const optId = `opt-${opt.id}`;
                if (mobileCustomOptions.selectedOptions[optId]) {
                    delete mobileCustomOptions.selectedOptions[optId];
                } else {
                    mobileCustomOptions.selectedOptions[optId] = opt.id;
                }
            }

            renderCustomizationGroups();
            updateMobileTotal();
        }

        function closeMobileCustomization() {
            document.getElementById('mobile-customization-modal').classList.add('hidden');
            document.body.style.overflow = '';
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
            let optionsTotal = 0;
            const options = mobileCustomProduct.options || [];

            Object.keys(mobileCustomOptions.selectedOptions).forEach(key => {
                const optId = mobileCustomOptions.selectedOptions[key];
                const opt = options.find(o => o.id == optId);
                if (opt) optionsTotal += parseFloat(opt.harga_tambahan || 0);
            });

            const total = (mobileCustomProduct.basePrice + optionsTotal) * mobileCustomQty;
            document.getElementById('mobile-custom-total').textContent = formatRupiah(total);
        }

        function addToCartFromMobile() {
            if (!mobileCustomProduct) return;

            // 1. Validate Mandatory Customizations
            const options = mobileCustomProduct.options || [];
            const groups = {};
            options.forEach(opt => {
                const cat = opt.kategori || 'Pilihan Tambahan';
                if (!groups[cat]) groups[cat] = { is_required: false, items: [] };
                if (opt.is_required) groups[cat].is_required = true;
                groups[cat].items.push(opt);
            });

            for (const catName in groups) {
                if (groups[catName].is_required) {
                    const hasSelection = Object.keys(mobileCustomOptions.selectedOptions).some(key => {
                        const optId = mobileCustomOptions.selectedOptions[key];
                        return options.find(o => o.id == optId)?.kategori === catName || key === catName;
                    });

                    if (!hasSelection) {
                        alert(`Mohon pilih opsi untuk ${catName} terlebih dahulu.`);
                        return;
                    }
                }
            }

            const notes = document.getElementById('mobile-custom-notes').value.trim();
            const parts = [];

            Object.keys(mobileCustomOptions.selectedOptions).forEach(key => {
                const optId = mobileCustomOptions.selectedOptions[key];
                const opt = options.find(o => o.id == optId);
                if (opt) parts.push(opt.nama_opsi);
            });

            if (notes) parts.push(notes);

            const customization = parts.join(', ');
            const available = mobileCustomProduct.stock - cartQtyForProduct(mobileCustomProduct.produkId);

            if (available < 1) {
                alert('Stok produk ini sudah habis di keranjang.');
                closeMobileCustomization();
                return;
            }

            const addQty = Math.min(mobileCustomQty, available);
            let optionsTotal = 0;
            Object.keys(mobileCustomOptions.selectedOptions).forEach(key => {
                const optId = mobileCustomOptions.selectedOptions[key];
                const opt = options.find(o => o.id == optId);
                if (opt) optionsTotal += parseFloat(opt.harga_tambahan || 0);
            });
            const finalPrice = mobileCustomProduct.basePrice + optionsTotal;

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
            currentOrderType = type === 'take-away' ? 'take-away' : 'dine-in';
            const btnDineIn = document.getElementById('btn-dine-in');
            const btnTakeAway = document.getElementById('btn-take-away');
            const tableContainer = document.getElementById('table-selection-container');
            const tableSelect = document.getElementById('table-number');

            const activeClasses = 'py-3 px-4 rounded-xl border-2 border-primary-600 bg-primary-600 text-white font-bold text-sm shadow-md shadow-primary-600/25 transition-all flex items-center justify-center gap-2';
            const inactiveClasses = 'py-3 px-4 rounded-xl border-2 border-slate-200 bg-white text-slate-600 font-bold text-sm hover:border-primary-300 transition-all flex items-center justify-center gap-2';

            if (currentOrderType === 'dine-in') {
                if (btnDineIn) btnDineIn.className = activeClasses;
                if (btnTakeAway) btnTakeAway.className = inactiveClasses;
                if (btnDineIn) btnDineIn.innerHTML = '<span>Dine-in</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
                if (btnTakeAway) btnTakeAway.innerHTML = '<span>Takeaway</span>';
                if (tableContainer) tableContainer.classList.remove('hidden');
                if (tableSelect) {
                    tableSelect.disabled = false;
                    if (qrLockedMejaId) {
                        tableSelect.value = qrLockedMejaId;
                        tableSelect.disabled = true;
                    }
                }
            } else {
                if (btnTakeAway) btnTakeAway.className = activeClasses;
                if (btnDineIn) btnDineIn.className = inactiveClasses;
                if (btnTakeAway) btnTakeAway.innerHTML = '<span>Takeaway</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
                if (btnDineIn) btnDineIn.innerHTML = '<span>Dine-in</span>';
                if (tableContainer) tableContainer.classList.add('hidden');
                if (tableSelect) {
                    tableSelect.value = '';
                    tableSelect.disabled = true;
                }
            }

            const typeLabel = document.getElementById('active-order-type-label');
            if (typeLabel) typeLabel.textContent = currentOrderType === 'dine-in' ? 'Dine-in' : 'Takeaway';
            typeLabel?.parentElement?.classList.toggle('hidden', false);

            updatePaymentMethodUI();
            syncCheckoutState();
        }

        function updatePaymentMethodUI() {
            const radioQris = document.querySelector('input[name="payment_method"][value="QRIS"]');
            const radioCash = document.querySelector('input[name="payment_method"][value="tunai"]');
            const qrisCard = radioQris?.closest('label');
            const cashCard = radioCash?.closest('label');
            const checkoutBtn = document.getElementById('checkout-btn');

            const qrisSelected = radioQris?.checked;
            if (qrisCard) {
                qrisCard.className = qrisSelected
                    ? 'flex items-center gap-3 p-3 rounded-xl border-2 border-primary-600 bg-primary-50 cursor-pointer'
                    : 'flex items-center gap-3 p-3 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-slate-300';
            }
            if (cashCard) {
                cashCard.className = qrisSelected
                    ? 'flex items-center gap-3 p-3 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-slate-300'
                    : 'flex items-center gap-3 p-3 rounded-xl border-2 border-primary-600 bg-primary-50 cursor-pointer';
            }

            if (checkoutBtn) {
                const orderText = qrisSelected ? 'Bayar Sekarang - QRIS Instan' : 'Bayar Sekarang - Tunai di Kasir';
                const titleEl = checkoutBtn.querySelector('.text-sm');
                if (titleEl) titleEl.textContent = orderText;
            }
        }

        function formatRupiah(num) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num).replace('IDR', 'Rp').trim();
        }

        function escapeHtml(value) {
            return String(value).replace(/[&<>"']/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[character]);
        }

        function syncCheckoutState() {
            const tableId = document.getElementById('table-number').value;
            const checkoutBtn = document.getElementById('checkout-btn');
            if (checkoutBtn) {
                checkoutBtn.disabled = cart.length === 0 || (currentOrderType === 'dine-in' && !tableId);
            }
        }

        function updateHeaderCart() {
            const bottomBadge = document.getElementById('bottom-nav-badge');
            const floatingBar = document.getElementById('floating-cart-bar');
            const floatingBadge = document.getElementById('floating-cart-badge');
            const floatingTitle = document.getElementById('floating-cart-title');
            const floatingTotal = document.getElementById('floating-cart-total');

            const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);

            if (totalQty > 0) {
                bottomBadge.textContent = totalQty;
                bottomBadge.classList.remove('hidden');
                bottomBadge.classList.add('flex');

                floatingBadge.textContent = totalQty;
                floatingTitle.textContent = `${totalQty} MENU DIPILIH`;
                floatingTotal.textContent = formatRupiah(subtotal);

                floatingBar.classList.remove('hidden');
                setTimeout(() => {
                    floatingBar.classList.remove('translate-y-2', 'opacity-0');
                }, 10);
            } else {
                bottomBadge.classList.add('hidden');
                bottomBadge.classList.remove('flex');

                floatingBar.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => {
                    floatingBar.classList.add('hidden');
                }, 300);
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
            const promoDiscount = appliedPromo && appliedPromo.diskon ? Number(appliedPromo.diskon) : 0;
            updateTotals(subtotal, totalItems, promoDiscount);
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

        function updateTotals(subtotal = 0, totalItems = 0, promoDiscount = 0) {
            const pb1 = Math.round(subtotal * 0.10);
            const total = Math.max(0, subtotal + pb1 - promoDiscount);

            document.getElementById('subtotal-count').textContent = totalItems;
            document.getElementById('summary-subtotal').textContent = formatRupiah(subtotal);
            document.getElementById('pb1-amount').textContent = formatRupiah(pb1);
            document.getElementById('summary-total').textContent = formatRupiah(total);
            document.getElementById('bottom-total').textContent = formatRupiah(total);

            syncCheckoutState();
        }

        function removePromo() {
            appliedPromo = null;
            const codeInput = document.getElementById('promo-code');
            if (codeInput) codeInput.value = '';
            const statusEl = document.getElementById('promo-status');
            if (statusEl) statusEl.textContent = '';
            const summaryEl = document.getElementById('promo-summary');
            if (summaryEl) summaryEl.classList.add('hidden');
            const removeBtn = document.getElementById('remove-promo-btn');
            if (removeBtn) removeBtn.classList.add('hidden');
            renderCart();
        }

        async function applyPromo() {
            const codeInput = document.getElementById('promo-code');
            const statusEl = document.getElementById('promo-status');
            const summaryEl = document.getElementById('promo-summary');
            const btn = document.querySelector('button[onclick="applyPromo()"]');
            const code = (codeInput?.value || '').trim();
            if (!code) {
                statusEl.textContent = 'Masukkan kode promo terlebih dahulu.';
                statusEl.className = 'text-xs text-red-600 mt-2';
                return;
            }

            let subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const tableId = currentOrderType === 'dine-in' ? Number(document.getElementById('table-number').value) : null;

            btn.disabled = true;
            try {
                const response = await fetch('/api/promo/validate', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        kode: code,
                        subtotal,
                        meja_id: tableId,
                        session_id: promoSessionId,
                    }),
                });
                const result = await response.json().catch(() => ({}));

                if (!response.ok) {
                    const apiMessage = result.errors?.kode?.[0] || result.message || 'Kode promo tidak valid.';
                    throw new Error(apiMessage);
                }

                appliedPromo = {
                    kode: code.toUpperCase(),
                    diskon: Number(result.data.diskon || 0),
                    nama: result.data.nama_promo || code,
                };

                if (codeInput) codeInput.disabled = true;
                btn.classList.add('hidden');
                document.getElementById('remove-promo-btn')?.classList.remove('hidden');
                summaryEl.textContent = `Promo ${appliedPromo.kode} diterapkan: diskon ${formatRupiah(appliedPromo.diskon)}`;
                summaryEl.classList.remove('hidden');
                statusEl.textContent = 'Kode promo aktif.';
                statusEl.className = 'text-xs text-emerald-600 mt-2';
                renderCart();
            } catch (error) {
                removePromo();
                statusEl.textContent = error.message;
                statusEl.className = 'text-xs text-red-600 mt-2';
            } finally {
                btn.disabled = false;
            }
        }

        async function processOrder() {
            if (cart.length === 0) return;

            const tableSelect = document.getElementById('table-number');
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const notes = document.getElementById('order-notes')?.value.trim() || '';
            const checkout = document.getElementById('checkout-btn');
            const orderMejaId = currentOrderType === 'dine-in' ? Number(tableSelect.value) : null;

            checkout.disabled = true;
            checkout.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...`;

            try {
                const response = await fetch('/api/transaksis', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        meja_id: orderMejaId,
                        tipe_pesanan: currentOrderType,
                        metode_pembayaran: paymentMethod,
                        catatan: notes || null,
                        session_id: promoSessionId,
                        kode_promo: appliedPromo ? appliedPromo.kode : null,
                        items: cart.map(item => ({ produk_id: item.produkId, qty: item.qty, customization: item.customization || null })),
                    }),
                });
                const result = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(Object.values(result.errors || {}).flat()[0] || result.message || 'Pesanan gagal dikirim.');
                }

                const order = result.data || result.transaksi;
                document.getElementById('receipt-date').textContent = new Date(order.waktu_transaksi).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta' }) + ' WIB';
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
                } catch (pollError) {}
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
            switchTab('status');
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
        updatePaymentMethodUI();

        document.addEventListener('visibilitychange', function () {
            if (!document.hidden && activeTab === 'status') {
                refreshMyOrder(false);
                startMyOrderPolling();
            }
        });

        // Lock table selection if coming from QR scan
        @if(isset($mejaAktif))
            const qrTableSelect = document.getElementById('table-number');
            const tableContainer = document.getElementById('table-selection-container');
            if (qrTableSelect) {
                qrTableSelect.value = {{ $mejaAktif->id }};
                qrTableSelect.disabled = true;
                qrTableSelect.classList.add('opacity-50', 'cursor-not-allowed');
                // Hide the selection label if table is locked from QR
                const tableLabel = tableContainer?.querySelector('label');
                if (tableLabel) tableLabel.style.display = 'none';
                // Show locked info
                const infoP = tableContainer?.querySelector('p');
                if (infoP) infoP.innerHTML = '<strong>Meja Anda (dari QR):</strong> Meja #' + {{ $mejaAktif->nomor_meja }} + ' (Terkunci dari scan QR)';
            }
        @endif
    </script>
</body>
</html>