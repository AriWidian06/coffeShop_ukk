<!-- Excerpt dari: resources/views/layouts/app.blade.php -->
<!-- Excerpt gabungan dari: layouts/manager.blade.php dan layouts/kasir.blade.php -->
<!-- Fokus: sidebar fixed, desktop-only navigation. -->

<aside class="w-64 bg-white border-r border-slate-200 fixed h-full z-30 hidden lg:block">
    <div class="p-6 flex flex-col h-full">
        <div class="flex items-center gap-3 mb-8 shrink-0">
            <h1 class="font-bold text-slate-900 text-lg leading-tight">BAGI KOPI</h1>
        </div>

        <nav class="space-y-1 flex-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : 'inactive' }}">
                <span>Dashboard</span>
            </a>

            @if(auth('karyawan')->user()?->role === 'admin')
                <a href="{{ route('produks.index') }}" class="sidebar-link {{ request()->routeIs('produks.*') ? 'active' : 'inactive' }}">
                    <span>Produk</span>
                </a>

                <a href="{{ route('mejas.index') }}" class="sidebar-link {{ request()->routeIs('mejas.*') ? 'active' : 'inactive' }}">
                    <span>Meja & QR</span>
                </a>

                <a href="{{ route('karyawans.index') }}" class="sidebar-link {{ request()->routeIs('karyawans.*') ? 'active' : 'inactive' }}">
                    <span>Karyawan</span>
                </a>
            @endif

            @if(in_array(auth('karyawan')->user()?->role, ['manager', 'admin', 'kasir'], true))
                <a href="{{ route('transaksis.create') }}" class="sidebar-link {{ request()->routeIs('transaksis.create') ? 'active' : 'inactive' }}">
                    <span>POS (Kasir)</span>
                </a>
            @endif
        </nav>
    </div>
</aside>

<!-- Main content menyesuaikan lebar sidebar desktop. -->
<main class="flex-1 lg:ml-64 min-h-screen">
    @yield('content')
</main>
```