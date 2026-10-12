<div class="space-y-6">
    <!-- Nomor Meja -->
    <div>
        <label for="nomor_meja" class="form-label">
            Nomor Meja <span class="text-red-500">*</span>
        </label>
        <input 
            type="number" 
            id="nomor_meja"
            name="nomor_meja" 
            value="{{ old('nomor_meja', $meja->nomor_meja ?? '') }}" 
            required
            min="1"
            placeholder="Contoh: 1, 2, 3, dst"
            class="form-input @error('nomor_meja') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
        >
        @error('nomor_meja') 
            <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ $message }}
            </p> 
        @enderror
        <p class="text-xs text-slate-500 mt-2">Nomor meja harus unik dan tidak boleh sama dengan meja lain.</p>
    </div>

    <!-- Kapasitas -->
    <div>
        <label for="kapasitas" class="form-label">
            Kapasitas Meja <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input 
                type="number" 
                id="kapasitas"
                name="kapasitas" 
                value="{{ old('kapasitas', $meja->kapasitas ?? 2) }}" 
                required
                min="1"
                max="20"
                class="form-input @error('kapasitas') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
            >
            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-slate-500 font-semibold">Orang</span>
        </div>
        @error('kapasitas') 
            <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ $message }}
            </p> 
        @enderror
        <p class="text-xs text-slate-500 mt-2">Jumlah maksimal orang yang dapat duduk di meja ini.</p>
    </div>

    <!-- Status Aktif -->
    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
        <input 
            type="checkbox" 
            id="status_aktif"
            name="status_aktif" 
            value="1"
            @checked(old('status_aktif', $meja->status_aktif ?? true))
            class="w-5 h-5 text-primary-600 rounded border-slate-300 focus:ring-primary-500 cursor-pointer"
        >
        <div class="flex-1">
            <label for="status_aktif" class="text-sm font-bold text-slate-900 cursor-pointer">
                Meja Aktif
            </label>
            <p class="text-xs text-slate-500">Meja yang aktif akan ditampilkan di sistem self-order dan dapat dipesan pelanggan.</p>
        </div>
    </div>
</div>