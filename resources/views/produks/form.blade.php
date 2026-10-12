<!-- Form Fields -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Nama Produk -->
    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Produk *</label>
        <input type="text" name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk ?? '') }}" required
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
            placeholder="Contoh: Kopi Susu Aren Gula Melaka">
        @error('nama_produk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Deskripsi -->
    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Produk</label>
        <textarea name="deskripsi" rows="3"
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
            placeholder="Deskripsi singkat produk...">{{ old('deskripsi', $produk->deskripsi ?? '') }}</textarea>
        @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Foto Produk -->
    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-slate-700 mb-2">Foto Produk</label>
        <div class="flex items-start gap-4">
            @if(isset($produk) && $produk->gambar)
                <div class="relative group">
                    <img src="{{ asset('storage/' . $produk->gambar) }}" alt="Preview" class="w-24 h-24 object-cover rounded-xl border border-slate-200 shadow-sm">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl flex items-center justify-center">
                        <span class="text-white text-[10px] font-bold">Current</span>
                    </div>
                </div>
            @endif
            <div class="flex-1">
                <input type="file" name="gambar" accept="image/*"
                    class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG, JPEG. Maksimal 2MB.</p>
            </div>
        </div>
        @error('gambar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Kategori -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Kategori Produk *</label>
        <select name="kategori_produk_id" required
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
            <option value="">Pilih Kategori</option>
            @foreach ($kategoriProduks as $kategori)
                <option value="{{ $kategori->id }}" {{ old('kategori_produk_id', $produk->kategori_produk_id ?? '') == $kategori->id ? 'selected' : '' }}>
                    {{ $kategori->nama_kategori }}
                </option>
            @endforeach
        </select>
        @error('kategori_produk_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Supplier -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Supplier *</label>
        <select name="supplier_id" required
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
            <option value="">Pilih Supplier</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ old('supplier_id', $produk->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->nama_supplier }}
                </option>
            @endforeach
        </select>
        @error('supplier_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Harga Jual -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Harga Jual (Rp) *</label>
        <input type="number" name="harga_jual" value="{{ old('harga_jual', $produk->harga_jual ?? '') }}" required min="0"
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
            placeholder="32000">
        @error('harga_jual') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Harga Beli (HPP) -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Harga Beli / HPP (Rp) *</label>
        <input type="number" name="harga_beli" value="{{ old('harga_beli', $produk->harga_beli ?? '') }}" required min="0"
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
            placeholder="17000">
        @error('harga_beli') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Tipe Produk -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Tipe Produk *</label>
        <select name="tipe" required
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
            <option value="jual" {{ old('tipe', $produk->tipe ?? '') == 'jual' ? 'selected' : '' }}>Siap Jual (Menu)</option>
            <option value="bahan baku" {{ old('tipe', $produk->tipe ?? '') == 'bahan baku' ? 'selected' : '' }}>Bahan Baku</option>
        </select>
        @error('tipe') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Satuan -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Satuan *</label>
        <input type="text" name="satuan" value="{{ old('satuan', $produk->satuan ?? 'pcs') }}" required
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
            placeholder="pcs, cup, kg, liter">
        @error('satuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Stock -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Stok Awal *</label>
        <input type="number" name="stock" value="{{ old('stock', $produk->stock ?? 0) }}" required min="0"
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
            placeholder="100">
        @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Status Aktif -->
    <div class="flex items-center gap-3">
        <input type="checkbox" name="status_aktif" id="status_aktif" value="1"
            {{ old('status_aktif', $produk->status_aktif ?? true) ? 'checked' : '' }}
            class="w-5 h-5 text-primary-600 rounded border-slate-300 focus:ring-primary-500">
        <label for="status_aktif" class="text-sm font-bold text-slate-700 cursor-pointer">
            Produk Aktif (Tampil di Menu)
        </label>
    </div>

    <!-- Kostumisasi -->
    <div class="flex items-center gap-3">
        <input type="checkbox" name="is_customizable" id="is_customizable" value="1"
            {{ old('is_customizable', $produk->is_customizable ?? false) ? 'checked' : '' }}
            class="w-5 h-5 text-primary-600 rounded border-slate-300 focus:ring-primary-500">
        <label for="is_customizable" class="text-sm font-bold text-slate-700 cursor-pointer">
            Bisa Dikostumisasi (Opsi Tambahan)
        </label>
    </div>

    <!-- Opsi Kostumisasi (Dynamic Section) -->
    <div id="customization-section" class="{{ old('is_customizable', $produk->is_customizable ?? false) ? '' : 'hidden' }} md:col-span-2 p-4 bg-slate-100 rounded-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-bold text-slate-800">Opsi Kostumisasi</h3>
            <button type="button" id="add-option" class="px-3 py-1.5 bg-primary-600 text-white rounded-lg text-xs font-semibold hover:bg-primary-700 transition-all flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Opsi
            </button>
        </div>

        <div id="options-list" class="space-y-3">
            @if(isset($produk) && $produk->opsis->count() > 0)
                @foreach($produk->opsis as $index => $opsi)
                    <div class="option-row flex items-center gap-3 p-3 bg-white rounded-xl border border-slate-200 shadow-sm">
                        <div class="flex-1">
                            <input type="text" name="opsis[{{ $index }}][nama_opsi]" value="{{ $opsi->nama_opsi }}" required
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
                                placeholder="Contoh: Less Sugar">
                        </div>
                        <div class="w-32">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rp</span>
                                <input type="number" name="opsis[{{ $index }}][harga_tambahan]" value="{{ $opsi->harga_tambahan }}" required min="0"
                                    class="w-full pl-7 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
                                    placeholder="0">
                            </div>
                        </div>
                        <button type="button" class="remove-option p-2 text-slate-400 hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 5H12l-4 4R5 13l4-4z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const customizableCheckbox = document.getElementById('is_customizable');
        const customizationSection = document.getElementById('customization-section');
        const addOptionBtn = document.getElementById('add-option');
        const optionsList = document.getElementById('options-list');

        // Toggle section visibility
        customizableCheckbox.addEventListener('change', function() {
            if (this.checked) {
                customizationSection.classList.remove('hidden');
            } else {
                customizationSection.classList.add('hidden');
            }
        });

        // Add new option row
        addOptionBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'option-row flex items-center gap-3 p-3 bg-white rounded-xl border border-slate-200 shadow-sm';
            row.innerHTML = `
                <div class="flex-1">
                    <input type="text" name="opsis[${Date.now()}][nama_opsi]" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
                        placeholder="Contoh: Less Sugar">
                </div>
                <div class="w-32">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rp</span>
                        <input type="number" name="opsis[${Date.now()}][harga_tambahan]" required min="0"
                        class="w-full pl-7 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
                        placeholder="0">
                    </div>
                </div>
                <button type="button" class="remove-option p-2 text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 5H12l-4 4R5 13l4-4z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6" />
                    </svg>
                </button>
            `;
            // Fix: Use a consistent unique ID for the row
            const uniqueId = Date.now();
            row.innerHTML = row.innerHTML.replace(/\${Date.now()}/g, uniqueId);
            optionsList.appendChild(row);
        });

        // Remove option row (Event Delegation)
        optionsList.addEventListener('click', function(e) {
            if (e.target.closest('.remove-option')) {
                e.target.closest('.option-row').remove();
            }
        });
    });
</script>
