<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasClosing extends Model
{
    protected $fillable = [
        'kas_account_id',
        'tanggal',
        'saldo_awal',
        'total_masuk',
        'total_keluar',
        'saldo_buku',
        'saldo_fisik',
        'selisih',
        'status',
        'karyawan_id',
        'verified_by',
        'verified_at',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'saldo_awal' => 'decimal:2',
        'total_masuk' => 'decimal:2',
        'total_keluar' => 'decimal:2',
        'saldo_buku' => 'decimal:2',
        'saldo_fisik' => 'decimal:2',
        'selisih' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(KasAccount::class, 'kas_account_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function verifier()
    {
        return $this->belongsTo(Karyawan::class, 'verified_by');
    }
}
