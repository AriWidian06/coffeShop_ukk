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
    <div class="min-h-screen flex">
        
        <!-- SIDEBAR KASIR -->
        <aside class="w-64 bg-white border-r border-slate-200 fixed h-full z-30 hidden lg:block">
            <!-- Logo & Outlet Info -->
            <div class="h-16 flex items-center px-6 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-primary-600 text-white flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 1v3M10 1v3M14 1v3" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-slate-900 text-sm leading-tight">Bagi Kopi</h1>
                        <p class="text-[9px] text-slate-500 font-semibold uppercase tracking-wider">Espresso & Space</p>
                    </div>
                </div>
            </div>

            <!-- Outlet Info -->
            <div class="p-4 border-b border-slate-200">
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200">
                    <p class="text-[10px] text-slate-500 font-semibold uppercase mb-1">Outlet Terhubung</p>
                    <p class="text-sm font-bold text-slate-900">Senopati, JKT</p>
                </div>
            </div>

            <!-- Kasir Navigation -->
            <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-16rem)]">
                <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Menu Utama</p>
                
                <a href="{{ route('dashboard') }}#kasir" id="sidebar-nav-menu" class="sidebar-link {{ request()->is('dashboard*') || request()->routeIs('transaksis.create') ? 'active' : 'inactive' }}" onclick="if(window.switchTab){ event.preventDefault(); window.switchTab('kasir'); } else if(window.switchPosView){ event.preventDefault(); window.switchPosView('menu'); }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Kasir & Menu</span>
                </a>

                <a href="{{ route('dashboard') }}#meja" id="sidebar-nav-denah" class="sidebar-link inactive" onclick="if(window.switchTab){ event.preventDefault(); window.switchTab('meja'); } else if(window.switchPosView){ event.preventDefault(); window.switchPosView('denah'); }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Denah Meja</span>
                </a>

                <a href="{{ route('dashboard') }}#transaksi" id="sidebar-nav-pesanan" class="sidebar-link inactive flex items-center justify-between" onclick="if(window.switchTab){ event.preventDefault(); window.switchTab('transaksi'); }">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Pesanan Aktif</span>
                    </div>
                    <span id="sidebar-pesanan-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white hidden">0</span>
                </a>

                <a href="{{ route('transaksis.index') }}" class="sidebar-link {{ request()->routeIs('transaksis.index') ? 'active' : 'inactive' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Riwayat Transaksi</span>
                </a>

                <a href="#" class="sidebar-link inactive">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v2a2 2 0 002 2h6a2 2 0 002-2v-1" />
                    </svg>
                    <span>Kas Masuk/Keluar</span>
                </a>
            </nav>

            <!-- User Profile Bottom -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-200 bg-white">
                <div class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-50 cursor-pointer">
                    <div class="w-9 h-9 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-sm">
                        {{ substr(auth('karyawan')->user()->nama_karyawan, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-900 truncate">{{ auth('karyawan')->user()->nama_karyawan }}</p>
                        <p class="text-xs text-slate-500 capitalize">Kasir</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-slate-600 hover:bg-red-50 hover:text-red-600 rounded-xl transition-colors text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar / Log Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 lg:ml-64">
            @yield('content')
        </div>
    </div>
</body>

</html>