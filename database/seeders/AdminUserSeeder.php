<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Sama seperti DatabaseSeeder: jangan menimpa password admin yang sudah ada.
        $admin = Karyawan::firstOrNew(['username' => 'admin']);

        if ($admin->exists) {
            return;
        }

        $password = (string) (env('ADMIN_PASSWORD') ?: 'admin' . \Illuminate\Support\Str::random(10));

        $admin->fill([
            'nama_karyawan' => 'Administrator',
            'jabatan' => 'Admin',
            'role' => 'admin',
            'username' => 'admin',
            'password' => Hash::make($password),
            'no_telepon' => '081234567890',
            'alamat' => 'Kantor Pusat',
        ])->save();

        $this->command?->info('Akun admin dibuat. Password awal: ' . $password);
    }
}
