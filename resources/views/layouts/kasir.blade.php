<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Terminal Kasir - Bagi Kopi' }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-slate-50 font-sans">
    <div class="min-h-screen lg:flex">

        <!-- MOBILE TOP BAR -->
        <header class="lg:hidden sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-slate-200">
            <div class="flex items-center justify-between gap-3 px-4 py-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <button type="button" id="kasir-sidebar-open" aria-label="Buka menu navigasi"
                            aria-controls="kasir-sidebar" aria-expanded="false"
                            class="w-10 h-10 -ml-1 rounded-xl text-slate-600 hover:bg-slate-100 active:bg-slate-200 flex items-center justify-center transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shadow-sm overflow-hidden border border-slate-200 shrink-0">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="Logo Bagi Kopi" class="w-full h-full object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-9 h-9 rounded-xl bg-primary-600 items-center justify-center" style="display: none;">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 1v3M10 1v3M14 1v3" />
                            </svg>
                        </div>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-900 leading-tight truncate">Bagi Kopi</p>
                        <p class="text-[9px] text-primary-600 font-semibold uppercase tracking-wider truncate">Terminal Kasir</p>
                    </div>
                </div>
                <div class="w-9 h-9 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-sm shrink-0">
                    {{ substr(auth('karyawan')->user()->nama_karyawan, 0, 1) }}
                </div>
            </div>
        </header>

        <!-- SIDEBAR OVERLAY (MOBILE) -->
        <div id="kasir-sidebar-overlay" class="app-sidebar-overlay fixed inset-0 bg-slate-900/50 z-40 lg:hidden" aria-hidden="true"></div>

        <!-- SIDEBAR KASIR -->
        <aside id="kasir-sidebar" class="app-sidebar kasir-sidebar w-72 max-w-[85vw] bg-white border-r border-slate-200 fixed inset-y-0 left-0 h-full z-50 flex flex-col lg:w-64 lg:max-w-none lg:z-30">
            <!-- Logo & Outlet Info -->
            <div class="relative overflow-hidden h-20 flex items-center justify-between gap-2 px-5 sm:px-6 border-b border-slate-200 shrink-0">
                <div class="absolute -top-12 -right-10 w-36 h-36 rounded-full bg-primary-100/70 blur-3xl pointer-events-none"></div>
                <div class="relative flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-sm overflow-hidden shrink-0">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="Logo Bagi Kopi" class="w-full h-full object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-10 h-10 rounded-2xl bg-primary-600 items-center justify-center" style="display: none;">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 1v3M10 1v3M14 1v3" />
                            </svg>
                        </div>
                    </div>
                    <div class="min-w-0">
                        <h1 class="font-extrabold text-slate-900 text-sm leading-tight truncate tracking-tight">Bagi Kopi</h1>
                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-[0.18em] truncate mt-0.5">Kasir Console</p>
                    </div>
                </div>
                <button type="button" id="kasir-sidebar-close" aria-label="Tutup menu navigasi"
                        class="lg:hidden relative w-9 h-9 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 flex items-center justify-center transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Outlet Info -->
            <div class="p-4 pb-2 shrink-0">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-[0.16em] flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,.6)]"></span>
                        Outlet Aktif
                    </p>
                    <p class="text-sm font-bold text-slate-900 mt-1.5">Soreang, Bandung</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Terminal #01 · Espresso &amp; Space</p>
                </div>
            </div>

            <!-- Kasir Navigation -->
            <nav class="px-4 pb-4 space-y-1 overflow-y-auto flex-1">
                <p class="px-3 pt-2 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-[0.18em]">Menu Utama</p>
                
                <a href="{{ route('dashboard') }}#kasir" id="sidebar-nav-menu" class="sidebar-link {{ request()->is('dashboard*') || request()->routeIs('transaksis.create') ? 'active' : 'inactive' }}" onclick="if(window.switchTab){ event.preventDefault(); window.switchTab('kasir'); } else if(window.switchPosView){ event.preventDefault(); window.switchPosView('menu'); }">
                    <span class="kasir-nav-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <span>Kasir &amp; Menu</span>
                </a>

                <a href="{{ route('dashboard') }}#meja" id="sidebar-nav-denah" class="sidebar-link inactive" onclick="if(window.switchTab){ event.preventDefault(); window.switchTab('meja'); } else if(window.switchPosView){ event.preventDefault(); window.switchPosView('denah'); }">
                    <span class="kasir-nav-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </span>
                    <span>Denah Meja</span>
                </a>

                <a href="{{ route('dashboard') }}#transaksi" id="sidebar-nav-pesanan" class="sidebar-link inactive flex items-center justify-between" onclick="if(window.switchTab){ event.preventDefault(); window.switchTab('transaksi'); }">
                    <span class="flex items-center gap-3">
                        <span class="kasir-nav-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </span>
                        <span>Pesanan Aktif</span>
                    </span>
                    <span id="sidebar-pesanan-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-400 text-amber-950 shadow-sm hidden">0</span>
                </a>

                <p class="px-3 pt-5 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-[0.18em]">Lainnya</p>

                <a href="{{ route('transaksis.index') }}" class="sidebar-link {{ request()->routeIs('transaksis.index') ? 'active' : 'inactive' }}">
                    <span class="kasir-nav-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <span>Riwayat Transaksi</span>
                </a>
            </nav>

            <!-- User Profile Bottom -->
            <div class="p-4 border-t border-slate-200 shrink-0">
                <div class="flex items-center gap-3 p-3 rounded-2xl border border-slate-200 bg-slate-50">
                    <div class="relative w-10 h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-extrabold text-sm shrink-0 ring-2 ring-white">
                        {{ substr(auth('karyawan')->user()->nama_karyawan, 0, 1) }}
                        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-400 border-2 border-white"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ auth('karyawan')->user()->nama_karyawan }}</p>
                        <p class="text-[10px] text-slate-500 capitalize mt-0.5">Kasir · Online</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-slate-600 hover:bg-red-50 hover:text-red-600 rounded-xl transition-colors text-xs font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar / Log Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 min-w-0 lg:ml-64">
            @yield('content')
        </div>
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('kasir-sidebar');
            const overlay = document.getElementById('kasir-sidebar-overlay');
            const openButton = document.getElementById('kasir-sidebar-open');
            const closeButton = document.getElementById('kasir-sidebar-close');

            if (!sidebar || !overlay || !openButton) return;

            const setOpen = (open) => {
                sidebar.classList.toggle('is-open', open);
                overlay.classList.toggle('is-open', open);
                document.body.classList.toggle('app-drawer-open', open);
                openButton.setAttribute('aria-expanded', open ? 'true' : 'false');
            };

            openButton.addEventListener('click', () => setOpen(true));
            closeButton?.addEventListener('click', () => setOpen(false));
            overlay.addEventListener('click', () => setOpen(false));

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') setOpen(false);
            });

            sidebar.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 1024) setOpen(false);
                });
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) setOpen(false);
            });
        })();
    </script>

    @include('partials.toast')

    @yield('scripts')
</body>

</html>
