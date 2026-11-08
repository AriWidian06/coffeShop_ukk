<!-- Excerpt dari: resources/views/welcome.blade.php -->
<!-- Fokus: floating cart bar, mobile bottom navigation, safe area. -->

<!-- FLOATING CART BOTTOM BAR -->
<div id="floating-cart-bar" class="fixed bottom-16 left-4 right-4 max-w-md mx-auto z-40 hidden transition-all duration-300 transform translate-y-2 opacity-0">
    <div onclick="toggleCart()" class="bg-primary-600 hover:bg-primary-700 text-white rounded-2xl p-3.5 shadow-xl shadow-primary-600/30 flex items-center justify-between cursor-pointer transition-all active:scale-95">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center relative">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span id="floating-cart-badge" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-amber-500 text-white text-[10px] font-extrabold rounded-full flex items-center justify-center border-2 border-primary-600">0</span>
            </div>
            <div>
                <div id="floating-cart-title" class="text-[11px] font-bold text-primary-100 tracking-wide">0 MENU DIPILIH</div>
                <div id="floating-cart-total" class="text-base font-extrabold">Rp 0</div>
            </div>
        </div>
        <div class="flex items-center gap-1.5 bg-white/15 px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-white/25 transition">
            <span>Lihat Keranjang</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </div>
    </div>
</div>

<!-- Mobile Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 px-6 py-2 z-50 safe-area-pb max-w-md mx-auto">
    <div class="flex items-center justify-around">
        <button onclick="switchTab('pesan')" id="nav-pesan" class="flex flex-col items-center gap-1 py-1 px-3 text-primary-600 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="text-[10px] font-bold">Pesan</span>
        </button>
        <button onclick="toggleCart()" class="flex flex-col items-center gap-1 py-1 px-3 text-slate-400 relative transition hover:text-slate-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span class="text-[10px] font-bold">Keranjang</span>
        </button>
        <button onclick="switchTab('status')" id="nav-status" class="flex flex-col items-center gap-1 py-1 px-3 text-slate-400 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-[10px] font-bold">Status</span>
        </button>
    </div>
</nav>
```