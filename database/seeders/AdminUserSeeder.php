<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Karyawan::create([
            'nama_karyawan' => 'Administrator',
            'jabatan'       => 'Admin',
            'role'          => 'admin',
            'username'      => 'admin',
            'password'      => Hash::make('password123'),
            'no_telepon'    => '081234567890',
            'alamat'        => 'Kantor Pusat',
        ]);
    }
}
