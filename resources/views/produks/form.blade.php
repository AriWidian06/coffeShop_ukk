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
</div>