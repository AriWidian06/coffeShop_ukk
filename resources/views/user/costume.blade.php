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
            <div class="flex items-center gap-2 mb-6">
                <span id="mobile-custom-price" class="text-2xl font-bold text-primary-600"></span>
            </div>

            <!-- Dynamic Groups Container -->
            <div id="customization-groups-container" class="space-y-6">
                <!-- Groups will be rendered here by JS -->
            </div>

            <!-- Notes -->
            <div class="mt-8 mb-6">
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