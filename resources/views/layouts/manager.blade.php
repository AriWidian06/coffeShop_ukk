<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Manager Console - Bagi Kopi' }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-slate-50 font-sans">
    <div class="min-h-screen flex">
        
        <!-- SIDEBAR MANAGER -->
        <aside class="w-64 bg-white border-r border-slate-200 fixed h-full z-30 hidden lg:block">
            <!-- Logo & Branch Info -->
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
                        <p class="text-[9px] text-primary-600 font-semibold uppercase tracking-wider">Manager Console</p>
                    </div>
                </div>
            </div>

            <!-- Branch Selector -->
            <div class="p-4 border-b border-slate-200">
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200">
                    <p class="text-[10px] text-slate-500 font-semibold uppercase mb-1">Branch Unit</p>
                    <p class="text-sm font-bold text-slate-900">Senopati Flagship</p>
                </div>
            </div>

            <!-- Manager Navigation -->
            <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-16rem)]">
                <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Menu Utama</p>
                
                <!-- 1. Dashboard -->
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : 'inactive' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Laporan -->
                <a href="{{ route('transaksis.index') }}" class="sidebar-link {{ request()->routeIs('transaksis.*') ? 'active' : 'inactive' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Laporan</span>
                </a>

                <!-- 3. Ekspor Laporan (SUDAH DIHUBUNGKAN KE ROUTE) -->
                <a href="{{ route('laporan.export') }}" class="sidebar-link inactive hover:bg-slate-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Ekspor Laporan (CSV)</span>
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
                        <p class="text-xs text-slate-500 capitalize">Store Manager</p>
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