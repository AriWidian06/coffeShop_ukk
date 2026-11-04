<!-- ========================================== -->
<!-- MOBILE CUSTOMIZATION MODAL -->
<!-- ========================================== -->
<div id="mobile-customization-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeMobileCustomization()"></div>
    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl max-h-[90vh] overflow-y-auto animate-slide-up">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between">
            <button onclick="closeMobileCustomization()" class="p-2 -ml-2 text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <h3 class="font-bold text-slate-900">Kustomisasi Pesanan</h3>
            <div class="w-10"></div>
        </div>

        <div class="px-4 py-4">
            <div class="rounded-2xl overflow-hidden mb-4 shadow-sm">
                <img id="mobile-custom-image" src="" alt="" class="w-full h-48 object-cover">
            </div>
            <div class="flex items-center gap-2 mb-2" id="mobile-custom-tags"></div>
            <h2 id="mobile-custom-name" class="text-xl font-bold text-slate-900 mb-1"></h2>
            <p id="mobile-custom-description" class="text-sm text-slate-500 mb-3"></p>
            <div class="flex items-center gap-2 mb-4">
                <span id="mobile-custom-price" class="text-2xl font-bold text-primary-600"></span>
            </div>

            <!-- Dynamic Options from Database -->
            <div id="dynamic-options-container" class="mb-5 hidden">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Pilihan Tambahan
                    </label>
                </div>
                <div class="space-y-2" id="dynamic-options-list"></div>
            </div>

            <!-- Temperature -->
            <div class="mb-5">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-bold text-slate-900 flex items-center gap-2">Penyajian Suhu</label>
                    <span class="text-[10px] font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full">Wajib</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="selectTemperature('iced')" id="temp-iced" class="temp-btn active py-3 px-4 rounded-xl border-2 border-primary-600 bg-primary-50 text-primary-700 font-bold text-sm">Dingin / Iced</button>
                    <button type="button" onclick="selectTemperature('hot')" id="temp-hot" class="temp-btn py-3 px-4 rounded-xl border-2 border-slate-200 text-slate-600 font-bold text-sm">Hangat / Hot</button>
                </div>
            </div>

            <!-- Milk -->
            <div class="mb-5">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-bold text-slate-900 flex items-center gap-2">Pilihan Susu</label>
                    <span class="text-[10px] font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full">Pilih 1</span>
                </div>
                <div class="space-y-2">
                    <button type="button" onclick="selectMilk('fresh', 0)" id="milk-fresh" class="milk-btn active w-full py-3 px-4 rounded-xl border-2 border-primary-600 bg-primary-50 text-left flex items-center justify-between">
                        <span class="font-bold text-slate-900 text-sm">Fresh Milk (Standar)</span>
                        <div class="milk-check flex items-center gap-2"><span class="text-xs font-bold text-slate-600">+Rp 0</span><svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
                    </button>
                    <button type="button" onclick="selectMilk('oat', 8000)" id="milk-oat" class="milk-btn w-full py-3 px-4 rounded-xl border-2 border-slate-200 text-left flex items-center justify-between">
                        <span class="font-bold text-slate-900 text-sm">Oat Milk</span>
                        <div class="milk-check"><span class="text-xs font-bold text-slate-600">+Rp 8.000</span></div>
                    </button>
                    <button type="button" onclick="selectMilk('almond', 8000)" id="milk-almond" class="milk-btn w-full py-3 px-4 rounded-xl border-2 border-slate-200 text-left flex items-center justify-between">
                        <span class="font-bold text-slate-900 text-sm">Almond Milk</span>
                        <div class="milk-check"><span class="text-xs font-bold text-slate-600">+Rp 8.000</span></div>
                    </button>
                </div>
            </div>

            <!-- Sugar -->
            <div class="mb-5">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-bold text-slate-900 flex items-center gap-2">Tingkat Manis Aren</label>
                    <span class="text-[10px] font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full">Pilih 1</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="selectSugar('normal')" id="sugar-normal" class="sugar-btn active py-3 px-2 rounded-xl border-2 border-primary-600 bg-primary-50 text-primary-700 font-bold text-xs text-center">Normal</button>
                    <button type="button" onclick="selectSugar('less')" id="sugar-less" class="sugar-btn py-3 px-2 rounded-xl border-2 border-slate-200 text-slate-600 font-bold text-xs text-center">Less Sweet</button>
                    <button type="button" onclick="selectSugar('no')" id="sugar-no" class="sugar-btn py-3 px-2 rounded-xl border-2 border-slate-200 text-slate-600 font-bold text-xs text-center">No Sugar</button>
                </div>
            </div>

            <!-- Extra Shot -->
            <div class="mb-5">
                <label class="flex items-center justify-between p-4 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-primary-300 transition">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="extra-shot" onchange="toggleExtraShot()" class="w-5 h-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">Ekstra Espresso Shot</div>
                            <div class="text-xs text-slate-500">Ekstra intensitas & body kopi</div>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-primary-600">+Rp 6.000</span>
                </label>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-2">Catatan untuk Barista</label>
                <textarea id="mobile-custom-notes" rows="3" placeholder="Cth: Less ice batu..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
            </div>
        </div>

        <!-- Fixed Bottom Bar -->
        <div class="sticky bottom-0 bg-white border-t border-slate-200 px-4 py-3 flex items-center gap-3">
            <div class="flex items-center gap-3 bg-slate-100 rounded-xl px-3 py-2">
                <button type="button" onclick="decrementQuantity()" class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-slate-700 font-bold shadow-sm">-</button>
                <span id="mobile-custom-qty" class="font-bold text-slate-900 w-6 text-center">1</span>
                <button type="button" onclick="incrementQuantity()" class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-slate-700 font-bold shadow-sm">+</button>
            </div>
            <button type="button" onclick="addToCartFromMobile()" class="flex-1 bg-primary-600 text-white font-bold py-3.5 px-6 rounded-xl flex items-center justify-between shadow-lg shadow-primary-600/25 active:scale-95 transition">
                <span>Pesan</span>
                <span id="mobile-custom-total">Rp 0</span>
            </button>
        </div>
    </div>
</div>