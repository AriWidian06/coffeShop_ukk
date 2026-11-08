<!-- Excerpt dari: resources/views/user/costume.blade.php -->
<!-- Fokus: modal kustomisasi mobile dan tombol aksi. -->

<div id="mobile-costume-modal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-end md:items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-t-3xl md:rounded-3xl max-h-[85vh] overflow-y-auto shadow-xl">
        <div class="p-4 border-b border-slate-200 sticky top-0 bg-white">
            <h2 class="font-bold text-slate-900 text-base">Kustomisasi Menu</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pilih opsi sesuai selera Anda.</p>
        </div>

        <div class="p-4 space-y-3">
            <button type="button" class="w-full min-h-11 rounded-xl border border-slate-200 text-left px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                Less Sugar (50%)
            </button>
            <button type="button" class="w-full min-h-11 rounded-xl border border-slate-200 text-left px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                Oat Milk +5.000
            </button>
            <button type="button" class="w-full min-h-11 rounded-xl border border-slate-200 text-left px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                Extra Shot +8.000
            </button>
        </div>

        <div class="p-4 border-t border-slate-200 sticky bottom-0 bg-white">
            <button type="button" class="w-full min-h-11 rounded-xl bg-primary-600 text-white font-bold text-sm hover:bg-primary-700 active:scale-95 transition">
                Tambahkan ke Keranjang
            </button>
        </div>
    </div>
</div>
```