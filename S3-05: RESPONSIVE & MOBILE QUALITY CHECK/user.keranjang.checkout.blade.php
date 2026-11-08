<!-- Excerpt dari: resources/views/user/keranjang.blade.php -->
<!-- Fokus: checkout mobile, fixed bottom payment dan navbar tambahan. -->

<div id="cart-page" class="fixed inset-0 z-50 bg-slate-50 hidden flex flex-col">
    <div class="flex-1 overflow-y-auto pb-4">
        <div class="max-w-3xl mx-auto px-4 py-6 space-y-4">
            <div id="cart-items"></div>

            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <h3 class="text-sm font-bold text-slate-900 mb-3">Opsi Layanan & Meja</h3>
                <select id="table-number" onchange="syncCheckoutState()" class="w-full text-sm font-semibold bg-white border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <option value="">Pilih meja...</option>
                </select>
            </div>

            <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-primary-600 bg-primary-50 cursor-pointer">
                <input type="radio" name="payment_method" value="QRIS" checked class="w-5 h-5 text-primary-600" onchange="updatePaymentMethodUI()">
            </label>
        </div>
    </div>

    <div class="shrink-0 bg-white border-t border-slate-200">
        <div class="max-w-3xl mx-auto p-4 pb-2">
            <button onclick="processOrder()" id="checkout-btn" disabled class="w-full bg-primary-600 text-white rounded-xl py-4 px-6 flex items-center justify-between font-bold shadow-lg shadow-primary-600/25 active:scale-95 transition disabled:bg-slate-300 disabled:cursor-not-allowed">
                <div>
                    <div class="text-sm">BAYAR SEKARANG</div>
                    <div class="text-xs opacity-90">QRIS Instan</div>
                </div>
                <div class="flex items-center gap-2">
                    <span id="bottom-total" class="text-lg">Rp 0</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>
        </div>

        <nav class="bg-white border-t border-slate-100 px-6 py-2 safe-area-pb">
            <div class="flex items-center justify-around max-w-md mx-auto">
                <button onclick="toggleCart(); switchTab('pesan')" class="flex flex-col items-center gap-1 py-1 px-3 text-slate-400 hover:text-primary-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-[10px] font-bold">Pesan</span>
                </button>
            </div>
        </nav>
    </div>
</div>
```