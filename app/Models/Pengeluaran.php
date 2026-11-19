<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table = 'pengeluarans';

    protected $fillable = [
        'karyawan_id',
        'kategori',
        'supplier_id',
        'deskripsi',
        'jumlah',
        'tanggal',
        'metode',
        'kas_account_id',
        'bukti',
        'catatan',
        'status',
        'approved_by',
        'approved_at',
        'catatan_approval',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'date',
        'approved_at' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function account()
    {
        return $this->belongsTo(KasAccount::class, 'kas_account_id');
    }

    public function approver()
    {
        return $this->belongsTo(Karyawan::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function getKategoriLabelAttribute(): string
    {
        return [
            'bahan_baku' => 'Bahan Baku',
            'operasional' => 'Operasional',
            'gaji' => 'Gaji Karyawan',
            'lainnya' => 'Lainnya',
        ][$this->kategori] ?? ucfirst($this->kategori);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ][$this->status] ?? ucfirst($this->status);
    }

    public function getBuktiUrlAttribute(): ?string
    {
        if (! $this->bukti) {
            return null;
        }

        if (filter_var($this->bukti, FILTER_VALIDATE_URL)) {
            return $this->bukti;
        }

        return asset('storage/'.$this->bukti);
    }
}
