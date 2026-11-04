<!-- ================= TAB STATUS PESANAN ================= -->
<div id="tab-status" class="space-y-4 hidden">
    <!-- Header Label -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-extrabold text-primary-600 tracking-wider">PELACAK LANGSUNG</p>
            <h2 class="text-xl font-extrabold text-slate-900">Status Pesanan (Meja #07)</h2>
        </div>
        <button class="w-10 h-10 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-primary-600 shadow-sm hover:bg-slate-50 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </button>
    </div>

    <!-- Estimated Time Card (Gradient Blue Box) -->
    <div class="bg-gradient-to-r from-blue-600 to-primary-600 text-white rounded-3xl p-5 shadow-lg shadow-blue-600/20 relative overflow-hidden flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/30 relative">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" />
            </svg>
            <span class="absolute top-1 right-1 w-3.5 h-3.5 bg-emerald-400 rounded-full border-2 border-blue-600 animate-pulse"></span>
        </div>
        <div>
            <span class="text-[11px] font-bold text-blue-100 tracking-wide uppercase">SEDANG DISEDUH</span>
            <h3 class="text-lg font-extrabold leading-tight">Perkiraan Siap: ±7 - 10 Menit</h3>
            <p class="text-xs text-blue-100/90 mt-0.5 leading-relaxed">Pesanan Anda diracik sesuai giliran secara presisi.</p>
        </div>
    </div>

    <!-- Progres Pesanan Card -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Progres Pesanan</h3>
            <span class="px-3 py-1 bg-primary-50 text-primary-700 text-xs font-extrabold rounded-full border border-primary-100">Langkah 2 dari 4</span>
        </div>

        <!-- Step Bar Graphic -->
        <div class="relative flex items-center justify-between px-2 pt-2 pb-1">
            <div class="absolute left-6 right-6 top-6 h-0.5 bg-slate-200 -z-0"></div>

            <!-- Step 1 -->
            <div class="flex flex-col items-center relative z-10">
                <div class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-md shadow-primary-600/30 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-900 mt-2">Diterima</span>
                <span class="text-[10px] text-slate-400 mt-0.5">10:14</span>
            </div>

            <!-- Step 2 -->
            <div class="flex flex-col items-center relative z-10">
                <div class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-md shadow-primary-600/30 font-bold ring-4 ring-primary-100">
                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-extrabold text-primary-600 mt-2">Sedang Dibuat</span>
                <span class="text-[10px] font-bold text-primary-600 mt-0.5 bg-primary-50 px-1.5 py-0.5 rounded">Proses</span>
            </div>

            <!-- Step 3 -->
            <div class="flex flex-col items-center relative z-10">
                <div class="w-10 h-10 rounded-full bg-slate-100 border-2 border-slate-200 text-slate-400 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <span class="text-xs font-medium text-slate-400 mt-2">Siap Diantar</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Menunggu</span>
            </div>

            <!-- Step 4 -->
            <div class="flex flex-col items-center relative z-10">
                <div class="w-10 h-10 rounded-full bg-slate-100 border-2 border-slate-200 text-slate-400 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span class="text-xs font-medium text-slate-400 mt-2">Selesai</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Selesai</span>
            </div>
        </div>
    </div>

    <!-- Barista Note Card -->
    <div class="bg-white rounded-3xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5">
        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80" alt="Barista Rama" class="w-12 h-12 rounded-2xl object-cover shrink-0 shadow-sm">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span class="text-[10px] font-extrabold text-primary-600 tracking-wider">SLOW BAR SPECIALIST</span>
            </div>
            <h4 class="font-bold text-slate-900 text-sm leading-tight mt-0.5">Sedang diracik oleh Barista Rama</h4>
            <p class="text-[11px] text-slate-500 italic mt-0.5">Menyeduh V60 pada rasio 1:15 suhu 92°C</p>
        </div>
    </div>

    <!-- Rincian Menu Pesanan Card -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 text-sm">Rincian Menu</h3>
            </div>
            <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg">3 Item</span>
        </div>

        <!-- Payment Status Tag -->
        <div class="bg-primary-50/60 border border-primary-100 rounded-2xl p-3 flex items-center justify-between">
            <div class="flex items-center gap-2 text-primary-700">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-xs font-bold">Lunas via QRIS</span>
            </div>
            <span class="text-sm font-extrabold text-slate-900">Rp 115.000</span>
        </div>

        <!-- Ordered Items List -->
        <div class="space-y-3 pt-1">
            <div class="flex items-center gap-3">
                <img src="https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=150&q=80" alt="Karsa Aren Latte" class="w-14 h-14 rounded-xl object-cover shrink-0 border border-slate-100">
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-slate-900 text-xs leading-tight">1x Karsa Aren Latte</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Less Sugar (50%), Oat Milk</p>
                </div>
                <span class="text-xs font-bold text-slate-900">Rp 38.000</span>
            </div>

            <div class="flex items-center gap-3 border-t border-slate-100 pt-3">
                <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=150&q=80" alt="Flores Bajawa V60" class="w-14 h-14 rounded-xl object-cover shrink-0 border border-slate-100">
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-slate-900 text-xs leading-tight">1x Flores Bajawa V60</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Manual Brew, Hot, Floral Notes</p>
                </div>
                <span class="text-xs font-bold text-slate-900">Rp 42.000</span>
            </div>

            <div class="flex items-center gap-3 border-t border-slate-100 pt-3">
                <img src="https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=150&q=80" alt="Almond Croissant" class="w-14 h-14 rounded-xl object-cover shrink-0 border border-slate-100">
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-slate-900 text-xs leading-tight">1x Almond Croissant</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Hangat (Warmed Up)</p>
                </div>
                <span class="text-xs font-bold text-slate-900">Rp 35.000</span>
            </div>
        </div>
    </div>

    <!-- Call Waiter Button -->
    <button onclick="alert('Waiter telah dipanggil ke Meja #07. Mohon tunggu sebentar.')" class="w-full bg-white hover:bg-slate-50 border border-slate-200 text-primary-600 font-bold py-3.5 px-4 rounded-2xl flex items-center justify-center gap-2 shadow-sm transition active:scale-95">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <span>Panggil Waiter ke Meja</span>
    </button>

    <!-- Bottom Dual Action Buttons -->
    <div class="grid grid-cols-2 gap-3 pt-1">
        <button onclick="switchTab('pesan')" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 px-4 rounded-2xl flex items-center justify-center gap-2 shadow-md shadow-primary-600/20 transition active:scale-95 text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Menu</span>
        </button>
        <button onclick="toggleCart()" class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold py-3.5 px-4 rounded-2xl flex items-center justify-center gap-2 shadow-sm transition active:scale-95 text-xs">
            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Struk Digital</span>
        </button>
    </div>
</div>