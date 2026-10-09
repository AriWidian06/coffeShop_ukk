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

    <!-- Header Desktop/Mobile Responsive -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between gap-4">
                
                <!-- Logo -->
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

                <!-- Search Bar (Desktop: Tengah, Mobile: Bawah) -->
                <div class="hidden md:flex flex-1 max-w-xl relative">
                    <input type="text" id="menu-search" onkeyup="filterMenu()" 
                        placeholder="Cari kopi, pastry, atau manual brew..."
                        class="w-full pl-11 pr-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Right Actions: Meja & Cart -->
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

            <!-- Mobile Search (Hanya muncul di layar kecil) -->
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

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Category Pills -->
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

        <!-- Section Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900">Katalog Pilihan</h2>
            <p class="text-slate-500">Disangrai teliti di roastery harian Perkoci</p>
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

                <div class="menu-card product-card bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-primary-200 overflow-hidden flex flex-col" 
                     data-category="{{ $categorySlug }}" data-name="{{ $dataName }}">
                    
                    <!-- Gambar Produk (Aspect Ratio 4:3) -->
                    <div class="relative aspect-4/3 bg-slate-100 overflow-hidden group pointer-events-none">
                        <img src="{{ $imageUrl }}" alt="{{ $produk->nama_produk }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 pointer-events-none">
                    </div>
                    
                    <!-- Info Produk -->
                    <div class="p-5 flex flex-col flex-1">
                        <div class="mb-4 flex-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600 bg-primary-50 px-2 py-1 rounded-md mb-2 inline-block">
                                {{ $produk->kategoriProduk->nama_kategori ?? 'Umum' }}
                            </span>
                            <h3 class="font-bold text-slate-900 text-lg leading-tight mb-2">{{ $produk->nama_produk }}</h3>
                            <p class="text-sm text-slate-500 line-clamp-2">
                                {{ $produk->deskripsi ?? 'Produk berkualitas dari Perkoci Eatery.' }}
                            </p>
                        </div>
                        
                        <!-- Harga & Tombol Add -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-auto">
                            <span class="font-extrabold text-primary-700 text-lg">Rp {{ $hargaFormatted }}</span>
                                <button type="button" data-web-cart-item data-product-id="{{ $produk->id }}"
                                    data-name="{{ $produk->nama_produk }}" data-price="{{ $produk->harga_jual }}"
                                    data-category="{{ $produk->kategoriProduk->nama_kategori ?? 'Lainnya' }}"
                                    data-stock="{{ $produk->stock }}" aria-label="Tambah {{ $produk->nama_produk }}"
                                    @disabled($produk->stock < 1)
                                    class="w-11 h-11 rounded-xl bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 active:scale-95 transition-all shadow-md shadow-primary-600/20 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
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

        document.getElementById('menu-grid').addEventListener('click', (event) => {
            const button = event.target.closest('[data-web-cart-item]');
            if (!button || button.disabled) return;

            addToCart(
                Number(button.dataset.productId),
                button.dataset.name,
                Number(button.dataset.price),
                button.dataset.category,
                Number(button.dataset.stock)
            );
        });

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
            document.querySelectorAll('[data-web-cart-item]').forEach(button => {
                const item = cart.find(product => product.produkId === Number(button.dataset.productId));
                button.disabled = Number(button.dataset.stock) < 1 || (item && item.qty >= item.stock);
            });
        }

        function syncCheckoutState() {
            const orderType = document.getElementById('order-type').value;
            const tableId = document.getElementById('table-number').value;
            document.getElementById('checkout-btn').disabled = cart.length === 0 || (orderType === 'dine-in' && !tableId);
        }

        function addToCart(produkId, name, price, category, stock) {
            const item = cart.find(product => product.produkId === produkId);
            if (item) {
                if (item.qty >= item.stock) return;
                item.qty += 1;
            } else if (stock > 0) {
                cart.push({ produkId, name, price, category, stock, qty: 1 });
            }
            renderCart();
            updateHeaderCart();
        }

        function updateQty(produkId, delta) {
            const item = cart.find(product => product.produkId === produkId);
            if (!item) return;
            item.qty = Math.min(item.qty + delta, item.stock);
            if (item.qty <= 0) { cart = cart.filter(product => product.produkId !== produkId); }
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
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border border-slate-300 rounded-lg bg-white overflow-hidden">
                                <button type="button" aria-label="Kurangi ${escapeHtml(item.name)}" onclick="updateQty(${item.produkId}, -1)" class="min-w-11 min-h-11 px-3 py-1.5 hover:bg-slate-50 font-bold text-slate-700">-</button>
                                <span class="px-2 font-bold text-slate-900 text-sm">${item.qty}</span>
                                <button type="button" aria-label="Tambah ${escapeHtml(item.name)}" onclick="updateQty(${item.produkId}, 1)" ${item.qty >= item.stock ? 'disabled' : ''} class="min-w-11 min-h-11 px-3 py-1.5 hover:bg-slate-50 font-bold text-slate-700 disabled:opacity-40 disabled:cursor-not-allowed">+</button>
                            </div>
                            <span class="text-sm font-bold text-slate-900 w-20 text-right">${formatRupiah(total)}</span>
                        </div>
                    </div>`;
            });

            container.innerHTML = html;
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
                        items: cart.map(item => ({ produk_id: item.produkId, qty: item.qty })),
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
                    itemsList += `<div class="flex justify-between"><span>${item.qty}x ${escapeHtml(item.name)}</span><span>${formatRupiah(item.price * item.qty)}</span></div>`;
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