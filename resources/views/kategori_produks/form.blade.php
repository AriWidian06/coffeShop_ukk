<div>
    <label for="nama_kategori" class="form-label">
        Nama Kategori <span class="text-red-500">*</span>
    </label>
    <input 
        type="text" 
        id="nama_kategori"
        name="nama_kategori" 
        value="{{ old('nama_kategori', $kategoriProduk->nama_kategori ?? '') }}" 
        required
        placeholder="Contoh: Signature Espresso, Bahan Baku, Pastry"
        class="form-input @error('nama_kategori') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    >
    @error('nama_kategori') 
        <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ $message }}
        </p> 
    @enderror
    <p class="text-xs text-slate-500 mt-2">Gunakan nama yang jelas dan singkat agar mudah dikenali oleh kasir.</p>
</div>