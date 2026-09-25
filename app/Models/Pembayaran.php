<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
	public $timestamps = false;

	protected $fillable = [
		'transaksi_id',
		'jumlah_bayar',
		'metode_pembayaran',
		'status_pembayaran',
		'kembalian',
		'waktu_pembayaran',
	];

	protected $casts = [
		'jumlah_bayar' => 'decimal:2',
		'kembalian' => 'decimal:2',
		'waktu_pembayaran' => 'datetime',
	];

	public function transaksi()
	{
		return $this->belongsTo(Transaksi::class);
	}
}
