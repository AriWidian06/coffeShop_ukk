<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perkoci Eatery — Kopi Seduh & Santapan Hangat</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                    },
                    colors: {
                        brand: {
                            50: '#FDFBF7',
                            100: '#F7F2E9',
                            200: '#ECE1D0',
                            300: '#DCC8B0',
                            400: '#C8A98B',
                            500: '#B08865',
                            600: '#926A47',
                            700: '#724F33',
                            800: '#4D3320',
                            900: '#2C1B10',
                            950: '#1A0E08',
                        },
                        amber: {
                            600: '#D97706',
                            700: '#B45309',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #FDFBF7;
            color: #2C1B10;
        }
        .glass-nav {
            background: rgba(253, 251, 247, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 7px;
        }
        ::-webkit-scrollbar-track {
            background: #F7F2E9;
        }
        ::-webkit-scrollbar-thumb {
            background: #C8A98B;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #926A47;
        }
    </style>
</head>
<body class="font-sans antialiased min-h-screen flex flex-col selection:bg-brand-200 selection:text-brand-900">

    <!-- TOP ANNOUNCEMENT BAR -->
    <div class="bg-brand-950 text-brand-100 text-xs py-2.5 px-4 border-b border-brand-900">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="font-medium">Kedai Buka Hari Ini: <strong class="text-white">08.00 - 22.00 WIB</strong></span>
            </div>
            <div class="hidden md:flex items-center gap-6 text-brand-300 text-[11px]">
                <span>📍 Jl. Perkoci No. 18</span>
                <span>⚡ High-speed Wi-Fi 100Mbps</span>
                <span>🔌 Stopkontak Setiap Meja</span>
            </div>
            <div class="text-brand-300 text-[11px] font-medium">
                Dine-in & Takeaway
            </div>
        </div>
    </div>

    <!-- STICKY NAVBAR -->
    <header class="sticky top-0 z-40 glass-nav border-b border-brand-200/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="#hero" class="flex items-center gap-3.5 group">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-brand-900 to-brand-800 text-brand-50 flex items-center justify-center font-serif font-bold text-2xl shadow-md group-hover:scale-105 transition-transform duration-300 border border-brand-700">
                    P
                </div>
                <div>
                    <span class="block font-serif font-bold text-2xl tracking-tight text-brand-950 leading-none">
                        Perkoci
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-600">
                        Coffee & Eatery
                    </span>
                </div>
            </a>

            <!-- Nav Links (Desktop) -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-brand-800">
                <a href="#menu" class="hover:text-amber-700 transition-colors py-1 relative group">
                    Daftar Menu
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-700 transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="#tentang" class="hover:text-amber-700 transition-colors py-1 relative group">
                    Tentang Kami
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-700 transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="#fasilitas" class="hover:text-amber-700 transition-colors py-1 relative group">
                    Fasilitas
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-700 transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="#kontak" class="hover:text-amber-700 transition-colors py-1 relative group">
                    Lokasi
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-700 transition-all duration-300 group-hover:w-full"></span>
                </a>
            </nav>

            <!-- Cart Trigger Button -->
            <div class="flex items-center gap-3">
                <button 
                    onclick="toggleCart()" 
                    aria-label="Buka Keranjang Pesanan"
                    class="relative inline-flex items-center gap-3 px-5 py-2.5 rounded-2xl bg-brand-900 text-brand-50 font-semibold text-sm hover:bg-brand-950 transition-all duration-200 active:scale-95 shadow-md shadow-brand-900/10 border border-brand-800"
                >
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span>Pesanan Saya</span>
                    <span id="cart-badge" class="bg-amber-600 text-white text-xs font-extrabold px-2.5 py-0.5 rounded-full min-w-[20px] text-center shadow-sm">0</span>
                </button>
            </div>
        </div>
    </header>

    <main class="flex-1">
        <!-- HERO SECTION -->
        <section id="hero" class="relative overflow-hidden py-16 lg:py-24 border-b border-brand-200/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                    
                    <!-- Left: Headline & Introduction -->
                    <div class="lg:col-span-7 space-y-6">
                        <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-brand-100/80 border border-brand-200 text-brand-800 text-xs font-semibold tracking-wide">
                            <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                            Kedai Kopi & Dapur Ramah Harian
                        </div>

                        <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-brand-950 leading-[1.12]">
                            Ruang teduh untuk kopi pagi & santapan hangat.
                        </h1>

                        <p class="text-base sm:text-lg text-brand-700 max-w-2xl leading-relaxed">
                            Diseduh dari biji kopi pilihan tanah nusantara, dipadu racikan menu dapur yang jujur dan nyaman. Tempat pas untuk bekerja, berdiskusi, atau sekadar rehat sejenak.
                        </p>

                        <div class="flex flex-wrap items-center gap-4 pt-3">
                            <a href="#menu" class="px-7 py-4 rounded-2xl bg-brand-900 text-white font-bold text-sm hover:bg-brand-800 transition-all shadow-lg shadow-brand-900/15 inline-flex items-center gap-2.5 active:scale-95">
                                <span>Pilih Menu Sekarang</span>
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </a>
                            <a href="#kontak" class="px-7 py-4 rounded-2xl bg-white border border-brand-300/80 text-brand-900 font-bold text-sm hover:bg-brand-100/60 transition-all inline-flex items-center gap-2 shadow-sm">
                                <span>Lokasi & Jam Kedai</span>
                            </a>
                        </div>

                        <!-- Highlight Badges -->
                        <div class="grid grid-cols-3 gap-6 pt-8 border-t border-brand-200/80">
                            <div>
                                <span class="block text-2xl font-bold text-brand-950 font-serif">100%</span>
                                <span class="text-xs text-brand-600 font-medium">Biji Kopi Asli Indonesia</span>
                            </div>
                            <div>
                                <span class="block text-2xl font-bold text-brand-950 font-serif">30+</span>
                                <span class="text-xs text-brand-600 font-medium">Menu Minuman & Makan</span>
                            </div>
                            <div>
                                <span class="block text-2xl font-bold text-brand-950 font-serif">Nyaman</span>
                                <span class="text-xs text-brand-600 font-medium">Indoor AC & Outdoor</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Cafe Showcase Card -->
                    <div class="lg:col-span-5">
                        <div class="bg-white p-7 sm:p-8 rounded-3xl border border-brand-200/90 shadow-xl shadow-brand-950/5 relative space-y-6">
                            <div class="flex items-center justify-between pb-4 border-b border-brand-100">
                                <div>
                                    <span class="text-[11px] font-bold text-brand-500 uppercase tracking-widest block">Rekomendasi Hari Ini</span>
                                    <h2 class="font-serif font-bold text-xl text-brand-950">Favorit Pengunjung</h2>
                                </div>
                                <span class="bg-amber-100 text-amber-900 text-xs px-3 py-1 rounded-xl font-bold border border-amber-200/80">
                                    Top Seller
                                </span>
                            </div>

                            <!-- Showcase Item 1 -->
                            <div class="group flex items-center gap-4 p-3.5 rounded-2xl bg-brand-50/60 border border-brand-100/80 hover:bg-brand-50 transition-colors">
                                <img 
                                    src="https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=160&q=80" 
                                    alt="Es Kopi Perkoci Aren"
                                    class="w-16 h-16 rounded-xl object-cover shrink-0 shadow-sm border border-brand-200"
                                >
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-bold text-brand-950 text-sm truncate">Es Kopi Perkoci Aren</h3>
                                        <span class="font-extrabold text-brand-900 text-sm ml-2">Rp 22rb</span>
                                    </div>
                                    <p class="text-xs text-brand-600 mt-0.5 line-clamp-1">Espresso double shot, susu segar, gula aren Sukabumi.</p>
                                    <button onclick="addToCart('Es Kopi Perkoci Aren', 22000, 'Kopi')" class="mt-1.5 text-xs font-bold text-amber-700 hover:text-amber-800 inline-flex items-center gap-1 transition-colors">
                                        <span>+ Tambah Pesanan</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Showcase Item 2 -->
                            <div class="group flex items-center gap-4 p-3.5 rounded-2xl bg-brand-50/60 border border-brand-100/80 hover:bg-brand-50 transition-colors">
                                <img 
                                    src="https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=160&q=80" 
                                    alt="Nasi Goreng Perkoci"
                                    class="w-16 h-16 rounded-xl object-cover shrink-0 shadow-sm border border-brand-200"
                                >
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-bold text-brand-950 text-sm truncate">Nasi Goreng Spesial</h3>
                                        <span class="font-extrabold text-brand-900 text-sm ml-2">Rp 32rb</span>
                                    </div>
                                    <p class="text-xs text-brand-600 mt-0.5 line-clamp-1">Bumbu gurih kedai, suwiran ayam, telur mata sapi & kerupuk.</p>
                                    <button onclick="addToCart('Nasi Goreng Spesial Perkoci', 32000, 'Makanan')" class="mt-1.5 text-xs font-bold text-amber-700 hover:text-amber-800 inline-flex items-center gap-1 transition-colors">
                                        <span>+ Tambah Pesanan</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Order Info Banner -->
                            <div class="p-3.5 bg-stone-50 rounded-2xl border border-stone-200/80 text-xs text-stone-600 flex items-center gap-3">
                                <svg class="w-5 h-5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="leading-relaxed">Pesan via web tanpa antre. Pilih meja untuk makan di tempat atau bungkus untuk dibawa pulang.</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- MENU SECTION -->
        <section id="menu" class="py-16 lg:py-24 bg-white border-b border-brand-200/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Section Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-brand-600 block mb-1">Daftar Hidangan</span>
                        <h2 class="font-serif text-3xl sm:text-4xl font-bold text-brand-950">
                            Menu Pilihan Perkoci
                        </h2>
                        <p class="text-brand-600 text-sm sm:text-base mt-2">
                            Pilih makanan atau minuman kesukaan Anda untuk langsung dimasukkan ke daftar pesanan.
                        </p>
                    </div>

                    <!-- Search Input -->
                    <div class="w-full md:w-80">
                        <div class="relative">
                            <input 
                                type="text" 
                                id="menu-search"
                                onkeyup="filterMenu()"
                                placeholder="Cari nama menu..." 
                                class="w-full px-4 py-3 pl-11 text-sm bg-brand-50/70 border border-brand-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-amber-600 focus:border-amber-600 transition-all font-medium text-brand-950 placeholder:text-brand-400 shadow-sm"
                            >
                            <svg class="w-5 h-5 text-brand-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Category Tabs -->
                <div class="flex items-center gap-2.5 overflow-x-auto pb-4 mb-10 border-b border-brand-100 scrollbar-none">
                    <button onclick="setCategory('semua')" class="category-btn active px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all bg-brand-900 text-white shrink-0 shadow-sm">
                        Semua Menu
                    </button>
                    <button onclick="setCategory('kopi')" class="category-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all bg-brand-100/70 text-brand-800 hover:bg-brand-200/60 shrink-0">
                        ☕ Kopi (Coffee)
                    </button>
                    <button onclick="setCategory('non-kopi')" class="category-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all bg-brand-100/70 text-brand-800 hover:bg-brand-200/60 shrink-0">
                        🍵 Non-Kopi & Teh
                    </button>
                    <button onclick="setCategory('makanan')" class="category-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all bg-brand-100/70 text-brand-800 hover:bg-brand-200/60 shrink-0">
                        🍛 Makanan Utama
                    </button>
                    <button onclick="setCategory('camilan')" class="category-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all bg-brand-100/70 text-brand-800 hover:bg-brand-200/60 shrink-0">
                        🥐 Camilan & Pastry
                    </button>
                </div>

                <!-- Menu Grid -->
                <div id="menu-grid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7">
                    
                    <!-- Item: Es Kopi Perkoci Aren -->
                    <div class="menu-card bg-brand-50/40 hover:bg-white border border-brand-200/80 rounded-3xl p-5 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-brand-950/5 hover:-translate-y-1 group" data-category="kopi" data-name="es kopi perkoci aren">
                        <div>
                            <div class="relative overflow-hidden rounded-2xl mb-4 aspect-[4/3] border border-brand-200/60 bg-brand-100">
                                <img 
                                    src="https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=600&q=80" 
                                    alt="Es Kopi Perkoci Aren" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                >
                                <span class="absolute top-3 right-3 bg-amber-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-xl shadow-md uppercase tracking-wider">
                                    Signature
                                </span>
                            </div>
                            <h3 class="font-bold text-brand-950 text-base group-hover:text-amber-700 transition-colors">Es Kopi Perkoci Aren</h3>
                            <p class="text-xs text-brand-600 mt-1.5 leading-relaxed">
                                Double espresso robusta-arabika, susu segar cair, dan gula aren organik asli Sukabumi.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-brand-100 flex items-center justify-between">
                            <span class="font-extrabold text-brand-950 text-base">Rp 22.000</span>
                            <button onclick="addToCart('Es Kopi Perkoci Aren', 22000, 'Kopi')" class="px-4 py-2 rounded-xl bg-brand-900 text-white text-xs font-bold hover:bg-brand-800 transition-all shadow-sm active:scale-95 inline-flex items-center gap-1">
                                <span>+ Pesan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Item: Manual Brew V60 Gayo -->
                    <div class="menu-card bg-brand-50/40 hover:bg-white border border-brand-200/80 rounded-3xl p-5 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-brand-950/5 hover:-translate-y-1 group" data-category="kopi" data-name="manual brew v60 aceh gayo filter">
                        <div>
                            <div class="relative overflow-hidden rounded-2xl mb-4 aspect-[4/3] border border-brand-200/60 bg-brand-100">
                                <img 
                                    src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=600&q=80" 
                                    alt="Manual Brew V60 Gayo" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                >
                                <span class="absolute top-3 right-3 bg-brand-800 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-xl shadow-md uppercase tracking-wider">
                                    Single Origin
                                </span>
                            </div>
                            <h3 class="font-bold text-brand-950 text-base group-hover:text-amber-700 transition-colors">Manual Brew V60 (Gayo)</h3>
                            <p class="text-xs text-brand-600 mt-1.5 leading-relaxed">
                                Arabika Aceh Gayo proses washed. Aroma buah segar, keasaman lembut, dan aftertaste bersih.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-brand-100 flex items-center justify-between">
                            <span class="font-extrabold text-brand-950 text-base">Rp 26.000</span>
                            <button onclick="addToCart('Manual Brew V60 (Gayo)', 26000, 'Kopi')" class="px-4 py-2 rounded-xl bg-brand-900 text-white text-xs font-bold hover:bg-brand-800 transition-all shadow-sm active:scale-95 inline-flex items-center gap-1">
                                <span>+ Pesan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Item: Americano -->
                    <div class="menu-card bg-brand-50/40 hover:bg-white border border-brand-200/80 rounded-3xl p-5 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-brand-950/5 hover:-translate-y-1 group" data-category="kopi" data-name="americano klasik black coffee">
                        <div>
                            <div class="relative overflow-hidden rounded-2xl mb-4 aspect-[4/3] border border-brand-200/60 bg-brand-100">
                                <img 
                                    src="https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=600&q=80" 
                                    alt="Classic Americano" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                >
                            </div>
                            <h3 class="font-bold text-brand-950 text-base group-hover:text-amber-700 transition-colors">Classic Americano</h3>
                            <p class="text-xs text-brand-600 mt-1.5 leading-relaxed">
                                Double shot espresso tanpa gula. Tersedia penyajian panas atau dingin beres segar.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-brand-100 flex items-center justify-between">
                            <span class="font-extrabold text-brand-950 text-base">Rp 18.000</span>
                            <button onclick="addToCart('Classic Americano', 18000, 'Kopi')" class="px-4 py-2 rounded-xl bg-brand-900 text-white text-xs font-bold hover:bg-brand-800 transition-all shadow-sm active:scale-95 inline-flex items-center gap-1">
                                <span>+ Pesan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Item: Uji Matcha Latte -->
                    <div class="menu-card bg-brand-50/40 hover:bg-white border border-brand-200/80 rounded-3xl p-5 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-brand-950/5 hover:-translate-y-1 group" data-category="non-kopi" data-name="uji matcha latte hijau">
                        <div>
                            <div class="relative overflow-hidden rounded-2xl mb-4 aspect-[4/3] border border-brand-200/60 bg-brand-100">
                                <img 
                                    src="https://images.unsplash.com/photo-1536256263959-770b48d82b0a?auto=format&fit=crop&w=600&q=80" 
                                    alt="Pure Uji Matcha Latte" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                >
                                <span class="absolute top-3 right-3 bg-emerald-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-xl shadow-md uppercase tracking-wider">
                                    Favorit
                                </span>
                            </div>
                            <h3 class="font-bold text-brand-950 text-base group-hover:text-amber-700 transition-colors">Pure Uji Matcha Latte</h3>
                            <p class="text-xs text-brand-600 mt-1.5 leading-relaxed">
                                Bubuk matcha Uji impor dipadukan susu murni. Rasa lembut gurih khas teh hijau Jepang.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-brand-100 flex items-center justify-between">
                            <span class="font-extrabold text-brand-950 text-base">Rp 25.000</span>
                            <button onclick="addToCart('Pure Uji Matcha Latte', 25000, 'Non-Kopi')" class="px-4 py-2 rounded-xl bg-brand-900 text-white text-xs font-bold hover:bg-brand-800 transition-all shadow-sm active:scale-95 inline-flex items-center gap-1">
                                <span>+ Pesan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Item: Artisan Dark Chocolate -->
                    <div class="menu-card bg-brand-50/40 hover:bg-white border border-brand-200/80 rounded-3xl p-5 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-brand-950/5 hover:-translate-y-1 group" data-category="non-kopi" data-name="artisan dark chocolate cokelat">
                        <div>
                            <div class="relative overflow-hidden rounded-2xl mb-4 aspect-[4/3] border border-brand-200/60 bg-brand-100">
                                <img 
                                    src="https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?auto=format&fit=crop&w=600&q=80" 
                                    alt="Artisan Dark Chocolate" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                >
                            </div>
                            <h3 class="font-bold text-brand-950 text-base group-hover:text-amber-700 transition-colors">Artisan Dark Chocolate</h3>
                            <p class="text-xs text-brand-600 mt-1.5 leading-relaxed">
                                Cokelat pekat 65% khas Sulawesi dilelehkan dengan susu hangat. Rasa kaya tanpa terlalu manis.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-brand-100 flex items-center justify-between">
                            <span class="font-extrabold text-brand-950 text-base">Rp 24.000</span>
                            <button onclick="addToCart('Artisan Dark Chocolate', 24000, 'Non-Kopi')" class="px-4 py-2 rounded-xl bg-brand-900 text-white text-xs font-bold hover:bg-brand-800 transition-all shadow-sm active:scale-95 inline-flex items-center gap-1">
                                <span>+ Pesan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Item: Nasi Goreng Spesial -->
                    <div class="menu-card bg-brand-50/40 hover:bg-white border border-brand-200/80 rounded-3xl p-5 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-brand-950/5 hover:-translate-y-1 group" data-category="makanan" data-name="nasi goreng spesial perkoci makanan">
                        <div>
                            <div class="relative overflow-hidden rounded-2xl mb-4 aspect-[4/3] border border-brand-200/60 bg-brand-100">
                                <img 
                                    src="https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=600&q=80" 
                                    alt="Nasi Goreng Spesial Perkoci" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                >
                                <span class="absolute top-3 right-3 bg-amber-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-xl shadow-md uppercase tracking-wider">
                                    Best Seller
                                </span>
                            </div>
                            <h3 class="font-bold text-brand-950 text-base group-hover:text-amber-700 transition-colors">Nasi Goreng Spesial Perkoci</h3>
                            <p class="text-xs text-brand-600 mt-1.5 leading-relaxed">
                                Nasi rempah pilihan, suwiran ayam gurih, telur mata sapi, kerupuk udang & acar segar.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-brand-100 flex items-center justify-between">
                            <span class="font-extrabold text-brand-950 text-base">Rp 32.000</span>
                            <button onclick="addToCart('Nasi Goreng Spesial Perkoci', 32000, 'Makanan')" class="px-4 py-2 rounded-xl bg-brand-900 text-white text-xs font-bold hover:bg-brand-800 transition-all shadow-sm active:scale-95 inline-flex items-center gap-1">
                                <span>+ Pesan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Item: Spaghetti Aglio Olio -->
                    <div class="menu-card bg-brand-50/40 hover:bg-white border border-brand-200/80 rounded-3xl p-5 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-brand-950/5 hover:-translate-y-1 group" data-category="makanan" data-name="spaghetti aglio olio smoked beef pasta">
                        <div>
                            <div class="relative overflow-hidden rounded-2xl mb-4 aspect-[4/3] border border-brand-200/60 bg-brand-100">
                                <img 
                                    src="https://images.unsplash.com/photo-1621996346565-e3d5d6281288?auto=format&fit=crop&w=600&q=80" 
                                    alt="Spaghetti Aglio Olio Smoked Beef" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                >
                            </div>
                            <h3 class="font-bold text-brand-950 text-base group-hover:text-amber-700 transition-colors">Spaghetti Aglio Olio Smoked Beef</h3>
                            <p class="text-xs text-brand-600 mt-1.5 leading-relaxed">
                                Pasta al dente ditumis minyak zaitun, bawang putih harum, cabai kering & daging asap melimpah.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-brand-100 flex items-center justify-between">
                            <span class="font-extrabold text-brand-950 text-base">Rp 34.000</span>
                            <button onclick="addToCart('Spaghetti Aglio Olio Smoked Beef', 34000, 'Makanan')" class="px-4 py-2 rounded-xl bg-brand-900 text-white text-xs font-bold hover:bg-brand-800 transition-all shadow-sm active:scale-95 inline-flex items-center gap-1">
                                <span>+ Pesan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Item: Warm Butter Croissant -->
                    <div class="menu-card bg-brand-50/40 hover:bg-white border border-brand-200/80 rounded-3xl p-5 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-brand-950/5 hover:-translate-y-1 group" data-category="camilan" data-name="croissant butter pastry camilan">
                        <div>
                            <div class="relative overflow-hidden rounded-2xl mb-4 aspect-[4/3] border border-brand-200/60 bg-brand-100">
                                <img 
                                    src="https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=600&q=80" 
                                    alt="Warm Butter Croissant" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                >
                                <span class="absolute top-3 right-3 bg-brand-800 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-xl shadow-md uppercase tracking-wider">
                                    Fresh Oven
                                </span>
                            </div>
                            <h3 class="font-bold text-brand-950 text-base group-hover:text-amber-700 transition-colors">Warm Butter Croissant</h3>
                            <p class="text-xs text-brand-600 mt-1.5 leading-relaxed">
                                Pastry garing renyah di luar, beraroma mentega wangi di dalam. Dihangatkan saat dipesan.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-brand-100 flex items-center justify-between">
                            <span class="font-extrabold text-brand-950 text-base">Rp 20.000</span>
                            <button onclick="addToCart('Warm Butter Croissant', 20000, 'Camilan')" class="px-4 py-2 rounded-xl bg-brand-900 text-white text-xs font-bold hover:bg-brand-800 transition-all shadow-sm active:scale-95 inline-flex items-center gap-1">
                                <span>+ Pesan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Item: Pisang Goreng Madu Wijen -->
                    <div class="menu-card bg-brand-50/40 hover:bg-white border border-brand-200/80 rounded-3xl p-5 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-brand-950/5 hover:-translate-y-1 group" data-category="camilan" data-name="pisang goreng madu wijen snack">
                        <div>
                            <div class="relative overflow-hidden rounded-2xl mb-4 aspect-[4/3] border border-brand-200/60 bg-brand-100">
                                <img 
                                    src="https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&fit=crop&w=600&q=80" 
                                    alt="Pisang Goreng Madu Wijen" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                >
                            </div>
                            <h3 class="font-bold text-brand-950 text-base group-hover:text-amber-700 transition-colors">Pisang Goreng Madu Wijen</h3>
                            <p class="text-xs text-brand-600 mt-1.5 leading-relaxed">
                                Pisang raja manis digoreng karamel madu alami dengan taburan biji wijen garing.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-brand-100 flex items-center justify-between">
                            <span class="font-extrabold text-brand-950 text-base">Rp 18.000</span>
                            <button onclick="addToCart('Pisang Goreng Madu Wijen', 18000, 'Camilan')" class="px-4 py-2 rounded-xl bg-brand-900 text-white text-xs font-bold hover:bg-brand-800 transition-all shadow-sm active:scale-95 inline-flex items-center gap-1">
                                <span>+ Pesan</span>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Empty State -->
                <div id="no-menu" class="hidden text-center py-16 text-brand-600 bg-brand-50/50 rounded-3xl border border-brand-200/60 mt-6">
                    <span class="text-3xl block mb-2">🔍</span>
                    <p class="font-serif text-lg font-bold text-brand-950">Menu tidak ditemukan</p>
                    <p class="text-xs text-brand-600 mt-1">Coba gunakan kata kunci lain seperti "kopi", "nasi", atau "croissant".</p>
                </div>

            </div>
        </section>

        <!-- TENTANG & FASILITAS SECTION -->
        <section id="tentang" class="py-16 lg:py-24 bg-brand-100/40 border-b border-brand-200/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                    <div class="space-y-6">
                        <span class="text-xs font-bold uppercase tracking-widest text-brand-600 block">Nilai Kedai Kami</span>
                        <h2 class="font-serif text-3xl sm:text-4xl font-bold text-brand-950 leading-tight">
                            Tempat singgah yang teduh dan bersahaja.
                        </h2>
                        <p class="text-brand-700 leading-relaxed text-sm sm:text-base">
                            Perkoci hadir dari kerinduan akan tempat nongkrong yang tidak terburu-buru. Kami meracik setiap cangkir kopi dan piring sajian dengan bahan-bahan pilihan tanah air yang segar setiap hari.
                        </p>
                        <p class="text-brand-700 leading-relaxed text-sm sm:text-base">
                            Tanpa pemanis buatan berlebih atau proses yang dipaksakan. Hanya cita rasa jujur yang pas menemani waktu luang maupun kesibukan Anda.
                        </p>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-brand-200/80">
                            <div class="p-4 rounded-2xl bg-white border border-brand-200/70 shadow-sm">
                                <span class="font-serif font-bold text-base text-brand-950 block">Rasa Ramah</span>
                                <span class="text-xs text-brand-600 mt-0.5 block">Racikan konsisten harga terjangkau</span>
                            </div>
                            <div class="p-4 rounded-2xl bg-white border border-brand-200/70 shadow-sm">
                                <span class="font-serif font-bold text-base text-brand-950 block">Petani Lokal</span>
                                <span class="text-xs text-brand-600 mt-0.5 block">100% pasokan biji nusantara</span>
                            </div>
                        </div>
                    </div>

                    <!-- Facilities Grid -->
                    <div id="fasilitas" class="bg-white p-8 sm:p-10 rounded-3xl border border-brand-200/80 shadow-lg shadow-brand-950/5 space-y-6">
                        <h3 class="font-serif font-bold text-2xl text-brand-950">
                            Fasilitas Pengunjung
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-brand-50/70 border border-brand-100">
                                <span class="text-2xl block mb-2">⚡</span>
                                <h4 class="font-bold text-brand-950 text-sm">Stopkontak Banyak</h4>
                                <p class="text-xs text-brand-600 mt-1">Tersedia colokan di hampir setiap sudut meja.</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-brand-50/70 border border-brand-100">
                                <span class="text-2xl block mb-2">📶</span>
                                <h4 class="font-bold text-brand-950 text-sm">Wi-Fi 100 Mbps</h4>
                                <p class="text-xs text-brand-600 mt-1">Koneksi stabil untuk kerja, streaming & zoom.</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-brand-50/70 border border-brand-100">
                                <span class="text-2xl block mb-2">🌿</span>
                                <h4 class="font-bold text-brand-950 text-sm">Area Outdoor</h4>
                                <p class="text-xs text-brand-600 mt-1">Area outdoor teduh untuk merokok & bersantai.</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-brand-50/70 border border-brand-100">
                                <span class="text-2xl block mb-2">🕌</span>
                                <h4 class="font-bold text-brand-950 text-sm">Musholla Bersih</h4>
                                <p class="text-xs text-brand-600 mt-1">Sarana ibadah yang terawat dan nyaman.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- LOCATION & RESERVATION SECTION -->
        <section id="kontak" class="py-16 lg:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-br from-brand-950 via-brand-900 to-brand-850 text-brand-100 rounded-3xl p-8 sm:p-14 overflow-hidden relative shadow-2xl border border-brand-800">
                    <div class="grid lg:grid-cols-12 gap-8 items-center">
                        <div class="lg:col-span-7 space-y-5">
                            <span class="text-amber-400 text-xs font-extrabold uppercase tracking-widest block">Lokasi & Operasional</span>
                            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-white leading-tight">
                                Singgah & Nikmati Hari Ini
                            </h2>
                            <p class="text-brand-200 text-sm leading-relaxed max-w-xl">
                                Datang langsung atau pesan lewat sistem web mandiri ini untuk menghemat waktu Anda.
                            </p>
                            
                            <div class="space-y-3 pt-3 text-xs sm:text-sm font-medium">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-xl bg-brand-800 text-amber-400 flex items-center justify-center shrink-0">📍</span>
                                    <span>Jl. Perkoci No. 18, Area Sentra Kuliner & Kreatif</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-xl bg-brand-800 text-amber-400 flex items-center justify-center shrink-0">⏰</span>
                                    <span>Senin – Minggu: 08.00 - 22.00 WIB (Last Order 21.30)</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-xl bg-brand-800 text-amber-400 flex items-center justify-center shrink-0">💬</span>
                                    <span>WhatsApp Kedai: 0812-3456-7890</span>
                                </div>
                            </div>
                        </div>

                        <!-- WhatsApp Action Card -->
                        <div class="lg:col-span-5 bg-white/5 backdrop-blur-md p-6 rounded-3xl border border-white/10 text-center space-y-4">
                            <h3 class="font-serif font-bold text-xl text-white">Reservasi Meja / Event</h3>
                            <p class="text-xs text-brand-300 leading-relaxed">
                                Untuk reservasi kelompok di atas 6 orang atau diskusi komunitas, hubungi kasir kami langsung.
                            </p>
                            <a 
                                href="https://wa.me/6281234567890?text=Halo%20Perkoci%20Eatery,%20saya%20ingin%20tanya%20reservasi%20meja" 
                                target="_blank"
                                class="inline-flex items-center justify-center gap-2 w-full py-3.5 px-5 rounded-2xl bg-amber-500 text-brand-950 font-extrabold text-sm hover:bg-amber-400 transition-all shadow-lg active:scale-95"
                            >
                                <span>Hubungi via WhatsApp</span>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.584 1.761.889 2.802.89h.005c3.18 0 5.767-2.586 5.768-5.766 0-3.18-2.587-5.767-5.779-5.767zm3.349 8.163c-.14.394-.808.75-1.127.794-.319.043-.655.197-2.222-.449-1.567-.647-2.57-2.253-2.651-2.36-.081-.108-.636-.848-.636-1.615 0-.767.404-1.144.549-1.301.145-.157.319-.197.426-.197.106 0 .213.001.305.006.098.005.228-.037.355.27.135.328.461 1.124.502 1.206.041.082.068.178.014.286-.054.108-.082.176-.163.27-.082.095-.173.212-.246.286-.082.082-.168.172-.072.336.096.164.428.707.917 1.144.629.561 1.16.735 1.324.817.164.082.261.069.359-.043.098-.112.42-491.533-.66.113-.169.226-.142.378-.085.152.057.962.454 1.126.536.164.082.273.123.314.192.041.069.041.4-.099.794z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="bg-brand-950 text-brand-400 py-10 border-t border-brand-900 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 text-brand-300">
                <span class="font-serif font-bold text-white text-base">Perkoci Eatery</span>
                <span>•</span>
                <span>Sistem Pemesanan Mandiri</span>
            </div>
            <p>© {{ date('Y') }} Perkoci Eatery. Dibuat dengan perhatian & rasa hangat.</p>
        </div>
    </footer>

    <!-- SLIDE-OVER CART DRAWER -->
    <div id="cart-drawer-backdrop" onclick="toggleCart()" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden transition-opacity duration-300"></div>
    
    <aside id="cart-drawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-white z-50 shadow-2xl flex flex-col translate-x-full transition-transform duration-300 ease-in-out border-l border-brand-200">
        <!-- Header -->
        <div class="p-6 bg-brand-50 border-b border-brand-200/80 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-900 text-amber-400 flex items-center justify-center text-lg">
                    🛍️
                </div>
                <div>
                    <h2 class="font-serif font-bold text-lg text-brand-950">Keranjang Saya</h2>
                    <p class="text-xs text-brand-600">Periksa daftar item sebelum konfirmasi</p>
                </div>
            </div>
            <button onclick="toggleCart()" aria-label="Tutup Keranjang" class="p-2 rounded-xl text-brand-600 hover:bg-brand-200/60 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Cart Items List -->
        <div id="cart-items" class="flex-1 overflow-y-auto p-6 space-y-3.5">
            <!-- Dynamic JS Injected items -->
        </div>

        <!-- Order Options & Summary Footer -->
        <div class="p-6 bg-brand-50/80 border-t border-brand-200/80 space-y-4">
            <div>
                <label class="block text-xs font-bold text-brand-900 mb-1.5">Opsi Layanan & Meja:</label>
                <div class="grid grid-cols-2 gap-2">
                    <select id="order-type" onchange="toggleTableInput()" class="text-xs font-semibold bg-white border border-brand-300 rounded-xl p-2.5 focus:ring-2 focus:ring-amber-600 focus:outline-none">
                        <option value="dine-in">Dine-in (Makan di Tempat)</option>
                        <option value="takeaway">Takeaway (Bawa Pulang)</option>
                    </select>
                    <select id="table-number" class="text-xs font-semibold bg-white border border-brand-300 rounded-xl p-2.5 focus:ring-2 focus:ring-amber-600 focus:outline-none">
                        <option value="Meja 01">Meja 01 (Indoor)</option>
                        <option value="Meja 02">Meja 02 (Indoor)</option>
                        <option value="Meja 03">Meja 03 (Indoor)</option>
                        <option value="Meja 04">Meja 04 (Outdoor)</option>
                        <option value="Meja 05">Meja 05 (Outdoor)</option>
                        <option value="Bar 01">Bar Area</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-brand-900 mb-1.5">Catatan Khusus (Opsional):</label>
                <input 
                    type="text" 
                    id="order-notes" 
                    placeholder="Contoh: Es sedikit, gula aren dipisah..." 
                    class="w-full text-xs bg-white border border-brand-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-amber-600 focus:outline-none font-medium"
                >
            </div>

            <!-- Price Summary -->
            <div class="pt-3 border-t border-brand-200/80 space-y-1.5 text-xs text-brand-700">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span id="summary-subtotal" class="font-bold text-brand-950">Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span>Pajak & Layanan (10%)</span>
                    <span id="summary-tax" class="font-bold text-brand-950">Rp 0</span>
                </div>
                <div class="flex justify-between text-base font-extrabold text-brand-950 pt-2 border-t border-brand-200/80">
                    <span>Total Bayar</span>
                    <span id="summary-total" class="text-amber-700">Rp 0</span>
                </div>
            </div>

            <!-- Submit Button -->
            <button 
                id="checkout-btn"
                onclick="processOrder()" 
                disabled
                class="w-full py-3.5 rounded-2xl bg-brand-900 text-white font-extrabold text-sm hover:bg-brand-950 disabled:bg-brand-300 disabled:cursor-not-allowed transition-all shadow-md active:scale-95"
            >
                Kirim Pesanan ke Kasir
            </button>
        </div>
    </aside>

    <!-- RECEIPT MODAL -->
    <div id="receipt-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white max-w-sm w-full rounded-3xl border border-brand-200 p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in duration-200">
            <div class="text-center space-y-1">
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 mx-auto flex items-center justify-center text-2xl font-bold mb-2">
                    ✓
                </div>
                <h3 class="font-serif font-bold text-xl text-brand-950">Pesanan Diterima!</h3>
                <p class="text-xs text-brand-600">Tunjukkan struk digital ini ke kasir atau tunggu di meja Anda.</p>
            </div>

            <!-- Receipt Box -->
            <div class="bg-brand-50 p-4 rounded-2xl border border-dashed border-brand-300 text-xs space-y-2 font-mono">
                <div class="flex justify-between border-b border-brand-200 pb-2">
                    <span class="font-bold">PERKOCI EATERY</span>
                    <span id="receipt-date"></span>
                </div>
                <div class="flex justify-between text-brand-700 font-bold">
                    <span id="receipt-order-type"></span>
                    <span id="receipt-target" class="text-amber-700"></span>
                </div>
                <div id="receipt-items-list" class="space-y-1.5 py-2 border-b border-brand-200"></div>
                <div class="flex justify-between font-bold pt-1 text-sm text-brand-950">
                    <span>TOTAL:</span>
                    <span id="receipt-grand-total"></span>
                </div>
                <div id="receipt-notes-display" class="text-[11px] text-brand-600 italic pt-1"></div>
            </div>

            <button onclick="closeReceipt()" class="w-full py-3.5 rounded-2xl bg-brand-900 text-white text-xs font-bold hover:bg-brand-950 transition-all shadow-md">
                Tutup & Pesan Baru
            </button>
        </div>
    </div>

    <!-- CLIENT JAVASCRIPT LOGIC -->
    <script>
        let cart = [];

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
            if (type === 'takeaway') {
                tableSelect.disabled = true;
                tableSelect.classList.add('opacity-40');
            } else {
                tableSelect.disabled = false;
                tableSelect.classList.remove('opacity-40');
            }
        }

        function formatRupiah(num) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
        }

        function addToCart(name, price, category) {
            const item = cart.find(i => i.name === name);
            if (item) {
                item.qty += 1;
            } else {
                cart.push({ name, price, category, qty: 1 });
            }
            renderCart();

            // Badge pulse animation
            const badge = document.getElementById('cart-badge');
            badge.classList.add('scale-125');
            setTimeout(() => badge.classList.remove('scale-125'), 200);
        }

        function updateQty(name, delta) {
            const item = cart.find(i => i.name === name);
            if (!item) return;
            item.qty += delta;
            if (item.qty <= 0) {
                cart = cart.filter(i => i.name !== name);
            }
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cart-items');
            const badge = document.getElementById('cart-badge');
            const checkoutBtn = document.getElementById('checkout-btn');
            
            const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
            badge.textContent = totalQty;

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-16 text-brand-500 space-y-2">
                        <span class="text-4xl block mb-2">☕</span>
                        <p class="text-sm font-bold text-brand-950">Keranjang masih kosong</p>
                        <p class="text-xs text-brand-600">Pilih menu favorit Anda dari katalog hidangan.</p>
                    </div>
                `;
                document.getElementById('summary-subtotal').textContent = 'Rp 0';
                document.getElementById('summary-tax').textContent = 'Rp 0';
                document.getElementById('summary-total').textContent = 'Rp 0';
                checkoutBtn.disabled = true;
                return;
            }

            checkoutBtn.disabled = false;
            let html = '';
            let subtotal = 0;

            cart.forEach(item => {
                const total = item.price * item.qty;
                subtotal += total;
                html += `
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-brand-50/70 border border-brand-200/70">
                        <div class="flex-1 min-w-0 pr-3">
                            <h4 class="font-bold text-brand-950 text-xs truncate">${item.name}</h4>
                            <span class="text-[11px] text-brand-600 font-medium">${formatRupiah(item.price)}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border border-brand-300 rounded-xl bg-white text-xs overflow-hidden">
                                <button onclick="updateQty('${item.name}', -1)" class="px-2.5 py-1 hover:bg-brand-100 font-bold text-brand-900">-</button>
                                <span class="px-2 font-bold text-brand-950">${item.qty}</span>
                                <button onclick="updateQty('${item.name}', 1)" class="px-2.5 py-1 hover:bg-brand-100 font-bold text-brand-900">+</button>
                            </div>
                            <span class="text-xs font-extrabold text-brand-950 w-16 text-right">${formatRupiah(total)}</span>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;

            const tax = Math.round(subtotal * 0.1);
            const grandTotal = subtotal + tax;

            document.getElementById('summary-subtotal').textContent = formatRupiah(subtotal);
            document.getElementById('summary-tax').textContent = formatRupiah(tax);
            document.getElementById('summary-total').textContent = formatRupiah(grandTotal);
        }

        function processOrder() {
            if (cart.length === 0) return;

            const orderType = document.getElementById('order-type').value;
            const tableNumber = document.getElementById('table-number').value;
            const notes = document.getElementById('order-notes').value.trim();

            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const grandTotal = subtotal + Math.round(subtotal * 0.1);

            document.getElementById('receipt-date').textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('receipt-order-type').textContent = orderType === 'dine-in' ? 'Dine-in' : 'Takeaway';
            document.getElementById('receipt-target').textContent = orderType === 'dine-in' ? tableNumber : 'BAWA PULANG';

            let itemsList = '';
            cart.forEach(item => {
                itemsList += `
                    <div class="flex justify-between">
                        <span>${item.qty}x ${item.name}</span>
                        <span>${formatRupiah(item.price * item.qty)}</span>
                    </div>
                `;
            });
            document.getElementById('receipt-items-list').innerHTML = itemsList;
            document.getElementById('receipt-grand-total').textContent = formatRupiah(grandTotal);

            const notesDisplay = document.getElementById('receipt-notes-display');
            if (notes) {
                notesDisplay.textContent = 'Catatan: "' + notes + '"';
                notesDisplay.classList.remove('hidden');
            } else {
                notesDisplay.classList.add('hidden');
            }

            toggleCart();
            document.getElementById('receipt-modal').classList.remove('hidden');
        }

        function closeReceipt() {
            document.getElementById('receipt-modal').classList.add('hidden');
            cart = [];
            document.getElementById('order-notes').value = '';
            renderCart();
        }

        function setCategory(category) {
            const buttons = document.querySelectorAll('.category-btn');
            buttons.forEach(btn => {
                btn.classList.remove('bg-brand-900', 'text-white');
                btn.classList.add('bg-brand-100/70', 'text-brand-800');
            });
            event.currentTarget.classList.remove('bg-brand-100/70', 'text-brand-800');
            event.currentTarget.classList.add('bg-brand-900', 'text-white');

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
            const query = document.getElementById('menu-search').value.toLowerCase();
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

        renderCart();
    </script>
</body>
</html>
