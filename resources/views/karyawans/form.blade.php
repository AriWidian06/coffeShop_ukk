<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    
    <!-- Nama Karyawan -->
    <div class="md:col-span-2">
        <label for="nama_karyawan" class="form-label">Nama Lengkap Karyawan <span class="text-red-500">*</span></label>
        <input type="text" id="nama_karyawan" name="nama_karyawan" value="{{ old('nama_karyawan', $karyawan->nama_karyawan ?? '') }}" required placeholder="Contoh: Budi Santoso" class="form-input @error('nama_karyawan') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
        @error('nama_karyawan') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p> @enderror
    </div>

    <!-- Jabatan -->
    <div>
        <label for="jabatan" class="form-label">Jabatan / Posisi <span class="text-red-500">*</span></label>
        <input type="text" id="jabatan" name="jabatan" value="{{ old('jabatan', $karyawan->jabatan ?? '') }}" required placeholder="Contoh: Head Barista, Supervisor" class="form-input @error('jabatan') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
        @error('jabatan') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p> @enderror
    </div>

    <!-- Role / Hak Akses -->
    <div>
        <label for="role" class="form-label">Hak Akses Sistem (Role) <span class="text-red-500">*</span></label>
        <select id="role" name="role" required class="form-input @error('role') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
            <option value="">-- Pilih Hak Akses --</option>
            <option value="admin" @selected(old('role', $karyawan->role ?? '') === 'admin')>Admin (Akses Penuh)</option>
            <option value="manager" @selected(old('role', $karyawan->role ?? '') === 'manager')>Manager (Operasional & Laporan)</option>
            <option value="kasir" @selected(old('role', $karyawan->role ?? '') === 'kasir')>Kasir (Hanya POS)</option>
        </select>
        @error('role') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p> @enderror
    </div>

    <!-- Username -->
    <div>
        <label for="username" class="form-label">Username Login <span class="text-red-500">*</span></label>
        <input type="text" id="username" name="username" value="{{ old('username', $karyawan->username ?? '') }}" required placeholder="Contoh: budi.santoso" class="form-input @error('username') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
        @error('username') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p> @enderror
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="form-label">
            Password 
            @if(isset($karyawan))
                <span class="text-slate-400 font-normal text-xs">(Kosongkan jika tidak ingin mengubah)</span>
            @else
                <span class="text-red-500">*</span>
            @endif
        </label>
        <input type="password" id="password" name="password" {{ isset($karyawan) ? '' : 'required' }} placeholder="Minimal 8 karakter" class="form-input @error('password') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
        @error('password') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p> @enderror
    </div>

    <!-- No Telepon -->
    <div>
        <label for="no_telepon" class="form-label">Nomor Telepon</label>
        <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $karyawan->no_telepon ?? '') }}" placeholder="Contoh: 081234567890" class="form-input @error('no_telepon') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
        @error('no_telepon') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p> @enderror
    </div>

    <!-- Alamat -->
    <div class="md:col-span-2">
        <label for="alamat" class="form-label">Alamat Lengkap</label>
        <textarea id="alamat" name="alamat" rows="3" placeholder="Alamat domisili karyawan..." class="form-input resize-none @error('alamat') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">{{ old('alamat', $karyawan->alamat ?? '') }}</textarea>
        @error('alamat') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p> @enderror
    </div>
</div>