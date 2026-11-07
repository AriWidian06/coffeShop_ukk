<!-- ========================================== -->
<!-- CART PAGE (FULL PAGE CHECKOUT - SCROLLABLE) -->
<!-- ========================================== -->
<div id="cart-page" class="fixed inset-0 z-50 bg-slate-50 hidden flex flex-col">
    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto pb-4">
        <div class="max-w-3xl mx-auto px-4 py-6 space-y-4">
            
            <!-- Cart Items -->
            <div id="cart-items">
                <!-- Items akan di-render oleh JavaScript -->
            </div>

            <!-- Opsi Layanan & Meja Section -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <h3 class="text-sm font-bold text-slate-900 mb-3">Opsi Layanan & Meja</h3>
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="setOrderType('dine-in')" id="btn-dine-in" class="order-type-btn active py-3 px-4 rounded-xl border-2 border-primary-600 bg-primary-50 text-primary-700 font-bold text-sm shadow-sm transition-all">
                            Dine-in
                        </button>
                        <button type="button" onclick="setOrderType('take-away')" id="btn-take-away" class="order-type-btn py-3 px-4 rounded-xl border-2 border-slate-200 text-slate-600 font-bold text-sm bg-white hover:border-primary-300 transition-all">
                            Takeaway
                        </button>
                    </div>

                    <div id="table-selection-container">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Meja:</label>
                        <select id="table-number" onchange="syncCheckoutState()" class="w-full text-sm font-semibold bg-white border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            @if(isset($mejas) && $mejas->isEmpty())
                                <option value="">Tidak ada meja tersedia</option>
                            @elseif(isset($mejas))
                                @php
                                    $mejaTerisi = $mejaTerisi ?? collect();
                                    $semuaTerisi = $mejaAktif === null && $mejas->count() > 0 && $mejas->every(fn ($m) => $mejaTerisi->contains($m->id));
                                @endphp
                                @if($semuaTerisi)
                                    <option value="">Semua meja sudah terisi</option>
                                @endif
                                @foreach($mejas as $meja)
                                    @php
                                        $dipakai = $mejaTerisi->contains($meja->id);
                                        $dipilihSendiri = isset($mejaAktif) && $mejaAktif->id === $meja->id;
                                        $terisi = $dipakai && !$dipilihSendiri;
                                    @endphp
                                    <option value="{{ $meja->id }}" {{ $dipilihSendiri ? 'selected' : '' }} {{ $terisi ? 'disabled' : '' }}>
                                        Meja {{ $meja->nomor_meja }} (Kapasitas: {{ $meja->kapasitas }}){{ $terisi ? ' - Terisi' : '' }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">Pilih meja...</option>
                            @endif
                        </select>
                        @if(isset($mejaAktif))
                            <p class="text-xs text-slate-500 mt-2">Anda sedang menggunakan Meja #{{ $mejaAktif->nomor_meja }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="order-notes" class="block text-xs font-bold text-slate-700 mb-2">Catatan Pesanan (Opsional)</label>
                        <textarea id="order-notes" rows="2" placeholder="Contoh: Es sedikit, gula aren dipisah..." class="w-full text-sm bg-white border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-primary-500 focus:outline-none resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Kode Promo Diskon</label>
                        <div class="flex gap-2">
                            <input id="promo-code" type="text" placeholder="Contoh: DISKON10" class="flex-1 uppercase text-sm bg-white border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <button type="button" onclick="applyPromo()" class="px-4 py-3 bg-primary-600 text-white rounded-xl text-sm font-bold hover:bg-primary-700 transition">Terapkan</button>
                            <button type="button" onclick="removePromo()" id="remove-promo-btn" class="px-3 py-3 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 hidden">X</button>
                        </div>
                        <p id="promo-status" class="text-xs text-slate-500 mt-2"></p>
                        <div id="promo-summary" class="hidden mt-2 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-700 font-semibold"></div>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <h3 class="text-sm font-bold text-slate-900 mb-3">METODE PEMBAYARAN</h3>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-primary-600 bg-primary-50 cursor-pointer">
                        <input type="radio" name="payment_method" value="QRIS" checked class="w-5 h-5 text-primary-600" onchange="updatePaymentMethodUI()">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <span class="font-bold text-slate-900 text-sm">QRIS Instan</span>
                                <span class="text-[10px] font-bold text-white bg-primary-600 px-1.5 py-0.5 rounded">Cepat</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">BCA, GoPay, OVO, Dana, ShopeePay</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-slate-300">
                        <input type="radio" name="payment_method" value="tunai" class="w-5 h-5 text-primary-600" onchange="updatePaymentMethodUI()">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 3.666V14m-6.118 4.014c.696 1.285 2.056 2.154 3.618 2.154s2.922-.869 3.618-2.154M19 10.25c0 .455-.066.893-.188 1.308M7.5 14c0 .69.105 1.354.3 1.972m9.4-1.972c.195-.618.3-1.282.3-1.972M12 2a8 8 0 100 16 8 8 0 000-16z" />
                                </svg>
                                <span class="font-bold text-slate-900 text-sm">Bayar di Kasir / Tunai</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Tunjukkan nomor meja saat ke bar</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <h3 class="text-sm font-bold text-slate-900 mb-3">RINCIAN PEMBAYARAN</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal Pesanan (<span id="subtotal-count">0</span> Item)</span>
                        <span id="summary-subtotal" class="font-semibold">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>PB1 Resto (10%)</span>
                        <span id="pb1-amount">Rp 0</span>
                    </div>
                    <div class="border-t border-slate-200 pt-2 mt-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-bold text-slate-900">Total Tagihan</span>
                                <p class="text-xs text-slate-500 mt-0.5">Termasuk PPN & Pelayanan</p>
                            </div>
                            <span id="summary-total" class="text-2xl font-bold text-primary-600">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fixed Bottom Payment Button & Navbar -->
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

        <!-- Navbar Navigasi Tambahan di Bawah Tombol Bayar -->
        <nav class="bg-white border-t border-slate-100 px-6 py-2 safe-area-pb">
            <div class="flex items-center justify-around max-w-md mx-auto">
                <button onclick="toggleCart(); switchTab('pesan')" class="flex flex-col items-center gap-1 py-1 px-3 text-slate-400 hover:text-primary-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-[10px] font-bold">Pesan</span>
                </button>
                <button onclick="toggleCart()" class="flex flex-col items-center gap-1 py-1 px-3 text-primary-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="text-[10px] font-bold">Keranjang</span>
                </button>
                <button onclick="toggleCart(); switchTab('status')" class="flex flex-col items-center gap-1 py-1 px-3 text-slate-400 hover:text-primary-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-[10px] font-bold">Status</span>
                </button>
            </div>
        </nav>
    </div>
</div>