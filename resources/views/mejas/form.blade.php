<label>Nomor Meja
    <input type="number" name="nomor_meja" value="{{ old('nomor_meja', $meja->nomor_meja ?? '') }}" required>
</label>

<label>Kapasitas Meja
    <input type="number" name="kapasitas" min="1" value="{{ old('kapasitas', $meja->kapasitas ?? 2) }}" required>
</label>

<div style="margin-top: 1rem; display: flex; align-items: center; gap: 0.5rem;">
    <input type="checkbox" name="status_aktif" value="1" id="status_aktif" @checked(old('status_aktif', $meja->status_aktif ?? true)) style="width: auto; margin: 0;">
    <label for="status_aktif" style="margin: 0; cursor: pointer;">Aktif</label>
</div>
