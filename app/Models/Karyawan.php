<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Karyawan extends Authenticatable
{
	use HasFactory;

	protected $table = 'karyawans';

	protected $fillable = [
		'nama_karyawan',
		'jabatan',
		'no_telepon',
		'alamat',
		'role',
		'username',
		'password',
	];

	protected $hidden = [
		'password',
	];

	protected $casts = [
		'password' => 'hashed',
	];

	public function transaksis()
	{
		return $this->hasMany(Transaksi::class, 'karyawan_id');
	}
}
