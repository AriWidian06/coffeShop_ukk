<!-- ================= TAB STATUS PESANAN ================= -->
<div id="tab-status" class="space-y-4 hidden">
    <!-- Header Label -->
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-[11px] font-extrabold text-primary-600 tracking-wider">PELACAK LANGSUNG</p>
            <h2 id="status-page-title" class="text-xl font-extrabold text-slate-900">Status Pesanan</h2>
            <p id="status-page-subtitle" class="text-xs text-slate-500 mt-0.5">Memuat pesanan aktif...</p>
        </div>
        <button onclick="refreshMyOrder(true)" id="status-refresh-btn" title="Segarkan status" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-primary-600 shadow-sm hover:bg-slate-50 active:scale-95 transition">
            <svg id="status-refresh-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
        </button>
    </div>

    <!-- Loading State -->
    <div id="status-loading" class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm flex flex-col items-center justify-center text-center">
        <svg class="w-8 h-8 text-primary-600 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-sm font-bold text-slate-700 mt-3">Memeriksa pesanan Anda...</p>
        <p class="text-xs text-slate-500 mt-1">Menyambungkan ke dapur Bagi Kopi</p>
    </div>

    <!-- Empty State -->
    <div id="status-empty" class="hidden bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>
        <h3 class="font-extrabold text-slate-900 text-base">Belum Ada Pesanan Aktif</h3>
        <p id="status-empty-message" class="text-xs text-slate-500 mt-1.5 leading-relaxed max-w-xs">Pesanan yang sedang diproses akan muncul di sini secara otomatis.</p>
        <button onclick="switchTab('pesan')" class="mt-5 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold rounded-xl shadow-md shadow-primary-600/20 transition active:scale-95">
            Lihat Menu
        </button>
    </div>

    <!-- Order Status Content -->
    <div id="status-content" class="hidden space-y-4">
        <!-- Current Status Card -->
        <div class="bg-gradient-to-r from-blue-600 to-primary-600 text-white rounded-3xl p-5 shadow-lg shadow-blue-600/20 relative overflow-hidden flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/30 relative">
                <span id="status-current-icon" class="text-white">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span id="status-live-dot" class="absolute top-1 right-1 w-3.5 h-3.5 bg-emerald-400 rounded-full border-2 border-blue-600 animate-pulse"></span>
            </div>
            <div class="min-w-0 flex-1">
                <span id="status-current-kicker" class="text-[11px] font-bold text-blue-100 tracking-wide uppercase">STATUS SAAT INI</span>
                <h3 id="status-current-label" class="text-lg font-extrabold leading-tight">Memuat...</h3>
                <p id="status-current-desc" class="text-xs text-blue-100/90 mt-0.5 leading-relaxed">Mohon tunggu sebentar.</p>
            </div>
            <span id="status-order-type" class="self-start px-2.5 py-1 bg-white/20 border border-white/30 rounded-lg text-[10px] font-extrabold uppercase tracking-wide">Dine-in</span>
        </div>

        <!-- Progress Card -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm">Progres Pesanan</h3>
                <span id="status-step-badge" class="px-3 py-1 bg-primary-50 text-primary-700 text-xs font-extrabold rounded-full border border-primary-100">Langkah 1 dari 4</span>
            </div>

            <div class="relative flex items-center justify-between px-2 pt-2 pb-1">
                <div class="absolute left-6 right-6 top-6 h-0.5 bg-slate-200 -z-0">
                    <div id="status-progress-fill" class="h-full bg-primary-600 transition-all duration-500" style="width: 0%"></div>
                </div>

                <div class="flex flex-col items-center relative z-10">
                    <div id="status-dot-1" class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-md shadow-primary-600/30 font-bold">1</div>
                    <span id="status-step-label-1" class="text-xs font-bold text-slate-900 mt-2">Diterima</span>
                </div>

                <div class="flex flex-col items-center relative z-10">
                    <div id="status-dot-2" class="w-10 h-10 rounded-full bg-slate-100 border-2 border-slate-200 text-slate-400 flex items-center justify-center font-bold">2</div>
                    <span id="status-step-label-2" class="text-xs font-medium text-slate-400 mt-2">Dibuat</span>
                </div>

                <div class="flex flex-col items-center relative z-10">
                    <div id="status-dot-3" class="w-10 h-10 rounded-full bg-slate-100 border-2 border-slate-200 text-slate-400 flex items-center justify-center font-bold">3</div>
                    <span id="status-step-label-3" class="text-xs font-medium text-slate-400 mt-2">Siap</span>
                </div>

                <div class="flex flex-col items-center relative z-10">
                    <div id="status-dot-4" class="w-10 h-10 rounded-full bg-slate-100 border-2 border-slate-200 text-slate-400 flex items-center justify-center font-bold">4</div>
                    <span id="status-step-label-4" class="text-xs font-medium text-slate-400 mt-2">Selesai</span>
                </div>
            </div>
        </div>

        <!-- Order Detail Card -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Rincian Pesanan</h3>
                </div>
                <span id="status-item-count" class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg">0 Item</span>
            </div>

            <div class="bg-primary-50/60 border border-primary-100 rounded-2xl p-3 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-primary-700 min-w-0">
                    <svg class="w-5 h-5 text-primary-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span id="status-payment-tag" class="text-xs font-bold truncate">Menunggu Pembayaran</span>
                </div>
                <span id="status-total" class="text-sm font-extrabold text-slate-900 shrink-0">Rp 0</span>
            </div>

            <div class="text-[11px] font-semibold text-slate-500 flex flex-wrap items-center gap-x-2 gap-y-1">
                <span id="status-order-number">#-</span>
                <span class="text-slate-300">•</span>
                <span id="status-order-time">-</span>
                <span class="text-slate-300">•</span>
                <span id="status-order-table">-</span>
            </div>

            <div id="status-items" class="space-y-3 pt-1"></div>

            <div id="status-notes" class="hidden bg-amber-50 border border-amber-100 rounded-2xl p-3">
                <p class="text-[10px] font-extrabold text-amber-700 uppercase tracking-wider mb-0.5">Catatan Pesanan</p>
                <p id="status-notes-text" class="text-xs text-amber-800 leading-relaxed"></p>
            </div>
        </div>

        <!-- Bottom Actions -->
        <div class="grid grid-cols-2 gap-3 pt-1">
            <button onclick="switchTab('pesan')" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 px-4 rounded-2xl flex items-center justify-center gap-2 shadow-md shadow-primary-600/20 transition active:scale-95 text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Tambah Menu</span>
            </button>
            <button onclick="refreshMyOrder(true)" class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold py-3.5 px-4 rounded-2xl flex items-center justify-center gap-2 shadow-sm transition active:scale-95 text-xs">
                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                <span>Segarkan</span>
            </button>
        </div>

        <p id="status-last-update" class="text-center text-[10px] font-semibold text-slate-400">Status diperbarui otomatis setiap 5 detik</p>
    </div>
</div>
