<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasMovement extends Model
{
    protected $fillable = [
        'kas_account_id',
        'tanggal',
        'jenis',
        'reff_type',
        'reff_id',
        'debit',
        'kredit',
        'saldo_sebelum',
        'saldo_sesudah',
        'keterangan',
        'karyawan_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(KasAccount::class, 'kas_account_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function getJenisLabelAttribute(): string
    {
        return [
            'saldo_awal' => 'Saldo Awal',
            'penjualan' => 'Penjualan',
            'pengeluaran' => 'Pengeluaran',
            'setoran' => 'Setoran',
            'tarik_tunai' => 'Tarik Tunai',
            'penyesuaian' => 'Penyesuaian',
        ][$this->jenis] ?? ucfirst($this->jenis);
    }
}
