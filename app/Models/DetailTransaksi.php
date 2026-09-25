<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
	use HasFactory;

	protected $table = 'detail_transaksis';

	protected $fillable = [
		'transaksi_id',
		'produk_id',
		'QTY',
		'subtotal',
	];

	protected $casts = [
		'QTY' => 'integer',
		'subtotal' => 'decimal:2',
	];

	public function transaksi()
	{
		return $this->belongsTo(Transaksi::class, 'transaksi_id');
	}

	public function produk()
	{
		return $this->belongsTo(Produk::class, 'produk_id');
	}
}
