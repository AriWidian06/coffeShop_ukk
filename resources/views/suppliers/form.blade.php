<div class="space-y-6">
    <!-- Nama Supplier -->
    <div>
        <label for="nama_supplier" class="form-label">
            Nama Supplier <span class="text-red-500">*</span>
        </label>
        <input 
            type="text" 
            id="nama_supplier"
            name="nama_supplier" 
            value="{{ old('nama_supplier', $supplier->nama_supplier ?? '') }}" 
            required
            placeholder="Contoh: PT. Kopi Nusantara, CV. Sumber Rejeki"
            class="form-input @error('nama_supplier') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
        >
        @error('nama_supplier') 
            <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ $message }}
            </p> 
        @enderror
    </div>

    <!-- Nomor Telepon -->
    <div>
        <label for="no_telp" class="form-label">
            Nomor Telepon <span class="text-red-500">*</span>
        </label>
        <input 
            type="text" 
            id="no_telp"
            name="no_telp" 
            value="{{ old('no_telp', $supplier->no_telp ?? '') }}" 
            required
            placeholder="Contoh: 0812-3456-7890"
            class="form-input @error('no_telp') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
        >
        @error('no_telp') 
            <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ $message }}
            </p> 
        @enderror
    </div>

    <!-- Alamat -->
    <div>
        <label for="alamat" class="form-label">
            Alamat Lengkap <span class="text-red-500">*</span>
        </label>
        <textarea 
            id="alamat"
            name="alamat" 
            rows="4"
            required
            placeholder="Masukkan alamat lengkap supplier..."
            class="form-input resize-none @error('alamat') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
        >{{ old('alamat', $supplier->alamat ?? '') }}</textarea>
        @error('alamat') 
            <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ $message }}
            </p> 
        @enderror
    </div>
</div>