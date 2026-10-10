<label>Nama
    <input name="nama_karyawan" value="{{ old('nama_karyawan', $karyawan->nama_karyawan ?? '') }}" required>
</label>

<label>Jabatan
    <select name="role" required>
        <option value="">-- Pilih Jabatan --</option>
        @foreach (['admin' => 'Admin', 'kasir' => 'Kasir', 'manager' => 'Manager'] as $value => $label)
            <option value="{{ $value }}" @selected(old('role', $karyawan->role ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</label>

<label>Telepon
    <input name="no_telepon" value="{{ old('no_telepon', $karyawan->no_telepon ?? '') }}">
</label>

<label>Alamat
    <textarea name="alamat">{{ old('alamat', $karyawan->alamat ?? '') }}</textarea>
</label>

<label>Username
    <input name="username" value="{{ old('username', $karyawan->username ?? '') }}" required>
</label>

<label>Password
    <input type="password" name="password" {{ isset($karyawan) ? '' : 'required' }}>
</label>
