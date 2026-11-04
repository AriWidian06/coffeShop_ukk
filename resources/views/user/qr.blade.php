<!-- ========================================== -->
<!-- QRIS PAYMENT & DIGITAL RECEIPT FULL PAGE -->
<!-- ========================================== -->
<div id="receipt-modal" class="fixed inset-0 z-50 bg-slate-50 hidden flex flex-col overflow-y-auto">
    <!-- Header (Sticky) -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-md mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button onclick="closeReceipt()" class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </button>
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-primary-600 text-white flex items-center justify-center shadow-md shadow-primary-600/20 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 10h-1.26A8 8 0 109 20h9a2 2 0 002-2v-6a2 2 0 00-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-sm text-slate-900 leading-tight">Qris Payment</h1>
                        <p class="text-[10px] text-slate-500 font-bold tracking-wider">BAGI KOPI</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-primary-50 rounded-xl border border-primary-200">
                    <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    <span id="receipt-target" class="text-xs font-bold text-primary-700">Meja #07</span>
                </div>
                <button class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-md w-full mx-auto px-4 py-4 space-y-4 pb-28">
        
        <!-- Status Bar & Timer -->
        <div class="flex items-center justify-between bg-white px-4 py-3 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-extrabold text-slate-800 tracking-wide">PESANAN AKTIF</span>
            </div>
            <div class="flex items-center gap-2">
                <span id="receipt-number" class="text-xs font-extrabold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-lg">#KRS-8821</span>
            </div>
        </div>

        <div class="bg-primary-50/70 border border-primary-100 rounded-2xl px-4 py-3 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5 text-slate-700">
                <svg class="w-5 h-5 text-primary-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-bold text-slate-700">Selesaikan pembayaran dalam</span>
            </div>
            <span class="text-xs font-extrabold text-primary-700 bg-white px-3 py-1.5 rounded-xl border border-primary-200 shadow-inner">14:43</span>
        </div>

        <!-- QRIS Main Card Container -->
        <div id="receipt-qris-section" class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-5">
            <!-- Badges Top Card -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 bg-blue-600 text-white text-[10px] font-extrabold rounded-md shadow-sm">QRIS</span>
                    <span class="px-2.5 py-0.5 bg-red-600 text-white text-[10px] font-extrabold rounded-md shadow-sm">GPN</span>
                </div>
                <div class="flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Otomatis Terverifikasi
                </div>
            </div>

            <!-- Merchant Info -->
            <div class="space-y-0.5 text-center sm:text-left">
                <h3 class="font-extrabold text-lg text-slate-900">Bagi Kopi</h3>
                <p class="text-[11px] text-slate-500 font-medium">NMID: ID10293847291 • Standar Interoperabel</p>
            </div>

            <!-- QR Image Box -->
            <div class="p-4 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 text-center space-y-2">
                <div class="p-3 bg-white rounded-2xl shadow-sm inline-block border border-slate-100">
                    <img id="receipt-qris-img" src="" alt="QRIS Code" class="w-48 h-48 mx-auto rounded-xl object-contain">
                </div>
                <p id="receipt-qris-status" class="text-xs font-bold text-amber-700 bg-amber-50 py-1.5 px-3 rounded-xl border border-amber-200 inline-block">Nominal otomatis sesuai total belanja.</p>
            </div>

            <!-- Total Pembayaran Section -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Pembayaran</p>
                    <h4 id="receipt-grand-total" class="text-xl font-extrabold text-primary-600 mt-0.5">Rp 0</h4>
                </div>
                <button onclick="navigator.clipboard.writeText(document.getElementById('receipt-grand-total').textContent); alert('Nominal berhasil disalin!');" class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl shadow-sm flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    Salin Nominal
                </button>
            </div>

            <!-- Action Buttons Grid -->
            <div class="grid grid-cols-2 gap-2.5">
                <a id="download-qr-btn" href="#" download="QRIS-BagiKopi.png" onclick="const img=document.getElementById('receipt-qris-img'); if(img.src){this.href=img.src;} else {alert('QR belum dimuat'); event.preventDefault();}" class="py-3 px-4 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs rounded-xl flex items-center justify-center gap-2 shadow-sm transition">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh QR Code
                </a>
                <button onclick="alert('Silakan buka aplikasi e-wallet (BCA, GoPay, OVO, Dana, dll.) lalu scan QR di atas.')" class="py-3 px-4 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs rounded-xl flex items-center justify-center gap-2 shadow-sm transition">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Buka e-Wallet
                </button>
            </div>
        </div>

        <!-- Accordion Panduan Pembayaran QRIS -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between cursor-pointer" onclick="this.nextElementSibling.classList.toggle('hidden')">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h4 class="font-bold text-slate-900 text-sm">Panduan Pembayaran QRIS</h4>
                </div>
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="space-y-3 pt-2 border-t border-slate-100">
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-primary-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">1</span>
                    <div>
                        <p class="text-xs font-bold text-slate-900">Buka Aplikasi Pembayaran</p>
                        <p class="text-[11px] text-slate-500">Gunakan m-Banking (BCA, Mandiri, BRI, dll.) atau e-Wallet favoritmu (GoPay, OVO, Dana, ShopeePay).</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-primary-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">2</span>
                    <div>
                        <p class="text-xs font-bold text-slate-900">Pindai / Upload QR</p>
                        <p class="text-[11px] text-slate-500">Scan QR di atas & pastikan nama merchant tertera <span class="font-bold text-slate-700">Bagi Kopi</span>.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-primary-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">3</span>
                    <div>
                        <p class="text-xs font-bold text-slate-900">Konfirmasi & Masukkan PIN</p>
                        <p class="text-[11px] text-slate-500">Cek kesesuaian total pembayaran dan tuntasakn transaksi dengan PIN keamanan Anda.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden elements to satisfy existing JS hooks (supaya fungsi JavaScript bawaan tetap berjalan normal) -->
        <div class="hidden">
            <span id="receipt-date"></span>
            <span id="receipt-order-type"></span>
            <div id="receipt-items-list"></div>
            <div id="receipt-notes-display"></div>
        </div>
    </main>

    <!-- Fixed Bottom Button & Security Footer -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 z-40 max-w-md mx-auto space-y-2">
        <button onclick="closeReceipt()" class="w-full py-4 rounded-2xl bg-primary-600 text-white font-bold text-sm hover:bg-primary-700 transition-all shadow-lg shadow-primary-600/30 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>Saya Sudah Membayar</span>
        </button>
        <p class="text-[10px] text-center text-slate-400 font-medium">Transaksi dilindungi enkripsi SSL 256-bit standar Bank Indonesia</p>
    </div>
</div>