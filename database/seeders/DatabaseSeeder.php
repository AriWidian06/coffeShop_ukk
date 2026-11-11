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
            $admin->fill([
                'nama_karyawan' => 'Administrator',
                'jabatan' => 'Administrator',
                'role' => 'admin',
                'password' => Hash::make($this->adminPassword()),
            ])->save();

            $this->command?->info('Akun admin dibuat. Password awal: ' . $this->adminPassword());
        }

        // Data dasar agar admin bisa langsung menambah produk setelah deploy.
        \App\Models\Supplier::firstOrCreate(
            ['nama_supplier' => 'Supplier Utama'],
            [
                'alamat' => 'Perkoci Eatery',
                'no_telp' => '-',
            ]
        );

        foreach (['Kopi Susu', 'Pastry', 'Topping'] as $namaKategori) {
            \App\Models\KategoriProduk::firstOrCreate([
                'nama_kategori' => $namaKategori,
            ]);
        }
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
