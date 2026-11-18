<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jangan reset password admin yang sudah ada: `migrate --seed` dijalankan
        // setiap deploy, sehingga password bisa terpakai ulang oleh penyerang.
        $admin = Karyawan::firstOrNew(['username' => 'admin']);

        if (! $admin->exists) {
            $adminPassword = $this->adminPassword();

            $admin->fill([
                'nama_karyawan' => 'Administrator',
                'jabatan' => 'Administrator',
                'role' => 'admin',
                'password' => Hash::make($adminPassword),
            ])->save();

            $this->command?->info('Akun admin dibuat. Password awal: ' . $adminPassword);
        }

        // Katalog menu kafe (30 makanan/minuman) sekaligus membersihkan menu lama.
        $this->call(CafeMenuSeeder::class);
    }

    /**
     * Password awal admin. Ambil dari env ADMIN_PASSWORD agar bisa diubah per deploy;
     * jika kosong, buat password acak yang sekali saja ditampilkan di log.
     */
    private function adminPassword(): string
    {
        return (string) (env('ADMIN_PASSWORD') ?: 'admin' . Str::random(10));
    }
}
