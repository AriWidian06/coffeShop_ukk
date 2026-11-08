<!-- Excerpt dari: resources/views/user/status.blade.php -->
<!-- Fokus: status pelanggan mobile, loading, empty state, dan tombol refresh. -->

<div id="tab-status" class="space-y-4 hidden">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-[11px] font-extrabold text-primary-600 tracking-wider">PELACAK LANGSUNG</p>
            <h2 id="status-page-title" class="text-xl font-extrabold text-slate-900">Status Pesanan</h2>
            <p id="status-page-subtitle" class="text-xs text-slate-500 mt-0.5">Memuat pesanan aktif...</p>
        </div>
        <button onclick="refreshMyOrder(true)" id="status-refresh-btn" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-primary-600 shadow-sm hover:bg-slate-50 active:scale-95 transition">
            <svg id="status-refresh-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
        </button>
    </div>

    <div id="status-loading" class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm flex flex-col items-center justify-center text-center">
        <svg class="w-8 h-8 text-primary-600 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <p class="text-sm font-bold text-slate-700 mt-3">Memeriksa pesanan Anda...</p>
        <p class="text-xs text-slate-500 mt-1">Menyambungkan ke dapur Bagi Kopi</p>
    </div>

    <div id="status-empty" class="hidden bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm flex flex-col items-center justify-center text-center">
        <h3 class="font-extrabold text-slate-900 text-base">Belum Ada Pesanan Aktif</h3>
        <p id="status-empty-message" class="text-xs text-slate-500 mt-1.5 leading-relaxed max-w-xs">Pesanan yang sedang diproses akan muncul di sini secara otomatis.</p>
    </div>
</div>
```