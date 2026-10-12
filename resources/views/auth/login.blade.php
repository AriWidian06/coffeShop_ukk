<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — Bagi Kopi</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased login-gradient-bg min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">

    <!-- Main Login Card -->
    <div class="login-card-shadow w-full max-w-5xl bg-white rounded-3xl overflow-hidden flex flex-col lg:flex-row login-animate">

        <!-- ============================================ -->
        <!-- PANEL KIRI: Branding & Informasi (Biru) -->
        <!-- ============================================ -->
        <div class="login-blue-panel lg:w-5/12 p-8 sm:p-10 lg:p-12 text-white flex flex-col justify-between relative z-10">

            <!-- Logo -->
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30 flex items-center justify-center overflow-hidden">
                    <!-- GANTI LOGO DI SINI: Upload file logo ke public/images/logo-bagi-kopi.jpg -->
                    <img src="{{ asset('images/logo-bagi-kopi.jpg') }}" alt="Bagi Kopi Logo" class="w-full h-full object-cover p-1" onerror="this.style.display='none'; this.parentElement.innerHTML='<svg class=\'w-6 h-6 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z\' /><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M6 1v3M10 1v3M14 1v3\' /></svg>'">
                </div>
                <div>
                    <h2 class="font-bold text-lg leading-tight">BAGI KOPI</h2>
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-100">Karsa Roastery & Supply</p>
                </div>
            </div>

            <!-- Konten Tengah -->
            <div class="my-10 lg:my-0">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-xs font-semibold mb-6">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    KONSOL ROASTERY V2.4
                </div>

                <h1 class="font-bold text-3xl lg:text-4xl leading-tight mb-4">
                    Presisi Artisan dalam<br>Setiap Takaran & Batch
                </h1>

                <p class="text-blue-100 text-sm leading-relaxed max-w-md">
                    Akses terpadu pengelolaan inventaris green beans, jadwal profiling sangrai cupping harian, hingga distribusi multi-outlet se-Nusantara.
                </p>
            </div>

            <!-- Card Bawah: Statistik -->
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="text-sm font-semibold">Sistem Manajemen Internal</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider bg-white/20 px-2.5 py-1 rounded-md">v2.4</span>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- PANEL KANAN: Form Login (Putih) -->
        <!-- ============================================ -->
        <div class="lg:w-7/12 p-8 sm:p-10 lg:p-12 bg-white">

            <!-- Badge Akses -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary-50 border border-primary-200 text-xs font-bold text-primary-700 mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-primary-600"></span>
                AKSES MASUK KARYAWAN
            </div>

            <!-- Heading -->
            <h2 class="font-bold text-3xl lg:text-4xl text-slate-900 mb-2">Selamat Datang Kembali</h2>
            <p class="text-slate-500 text-sm mb-8">
                Gunakan kredensial resmi manajemen internal Karsa Roastery untuk melanjutkan.
            </p>

            <!-- Error Alert -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-red-900 text-sm">Otentikasi Tidak Berhasil</p>
                        <p class="text-xs text-red-700 mt-0.5">
                            {{ $errors->first() }}
                        </p>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Form Login -->
            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <!-- Username -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="username" class="text-sm font-bold text-slate-700">
                            Email Pegawai / Akun Roaster
                        </label>
                        <span class="text-xs text-slate-400">Format: username Anda</span>
                    </div>
                    <div class="input-icon-wrapper">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="admin@karsaroastery.id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-11 pr-4 text-sm font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:bg-white transition-all"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="text-sm font-bold text-slate-700">
                            Kata Sandi Akun
                        </label>
                        <a href="#" class="text-xs font-semibold text-primary-600 hover:text-primary-700">
                            Lupa Kata Sandi?
                        </a>
                    </div>
                    <div class="input-icon-wrapper">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••••••"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-11 pr-12 text-sm font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:bg-white transition-all"
                        >
                        <button type="button" onclick="togglePassword()" class="password-toggle" aria-label="Tampilkan kata sandi">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                    <label for="remember" class="text-sm text-slate-600 cursor-pointer select-none">
                        Ingat sesi saya di terminal ini selama 14 hari
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-primary-600/25 active:scale-[0.98] flex items-center justify-center gap-2">
                    <span>Masuk ke Dashboard</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </button>
            </form>

            <!-- Quick Access Card -->
            <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium">Terminal Produksi & Roasting Room:</span>
                </div>
                <a href="/" class="text-xs font-bold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                    Mode Cepat POS
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>

            <!-- Footer Security -->
            <div class="mt-6 flex items-center justify-between text-xs text-slate-400">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Dilindungi enkripsi SSL 256-bit</span>
                </div>
                <span class="font-mono">Sistem Manajemen Internal Karsa Roastery v2.4</span>
            </div>
        </div>
    </div>

    <!-- Password Toggle Script -->
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            }
        }
    </script>
</body>
</html>