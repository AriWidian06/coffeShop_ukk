<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriProduk extends Model
{
	protected $fillable = ['nama_kategori'];

	public function produks()
	{
		return $this->hasMany(Produk::class, 'kategori_produk_id');
	}

	/**
	 * Kategori yang masih memiliki minimal satu produk jual aktif.
	 * Dipakai untuk tab filter katalog pelanggan dan POS.
	 */
	public function scopeUntukKatalog($query)
	{
		return $query->whereHas('produks', function ($query) {
			$query->where('tipe', 'jual')->where('status_aktif', true);
		});
	}
}
