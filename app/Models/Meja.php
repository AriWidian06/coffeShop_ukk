<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meja extends Model
{
	use HasFactory;

	protected $table = 'mejas';

	protected $fillable = [
		'nomor_meja',
		'kapasitas',
		'status_aktif',
	];

	protected $casts = [
		'nomor_meja' => 'integer',
		'kapasitas' => 'integer',
		'status_aktif' => 'boolean',
	];

	public function transaksis()
	{
		return $this->hasMany(Transaksi::class, 'meja_id');
	}
}
