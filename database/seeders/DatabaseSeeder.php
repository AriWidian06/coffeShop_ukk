<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Karyawan::updateOrCreate(['username' => 'admin'], [
            'nama_karyawan' => 'Administrator',
            'jabatan' => 'Administrator',
            'role' => 'admin',
            'password' => 'admin12345',
        ]);
    }
}
