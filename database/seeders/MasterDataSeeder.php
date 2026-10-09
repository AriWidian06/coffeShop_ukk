<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\KategoriProduk;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Kategori Produk
        $kategori = [
            ['nama_kategori' => 'Minuman Kopi'],
            ['nama_kategori' => 'Minuman Non-Kopi'],
            ['nama_kategori' => 'Makanan Ringan'],
            ['nama_kategori' => 'Makanan Berat'],
            ['nama_kategori' => 'Bahan Baku'],
        ];

        foreach ($kategori as $item) {
            KategoriProduk::create($item);
        }

        // Seed Supplier
        $suppliers = [
            ['nama_supplier' => 'PT Kopi Nusantara', 'alamat' => 'Jakarta', 'no_telp' => '021-123456'],
            ['nama_supplier' => 'CV Susu Segar', 'alamat' => 'Bandung', 'no_telp' => '022-123456'],
            ['nama_supplier' => 'Toko Bahan Kue Jaya', 'alamat' => 'Surabaya', 'no_telp' => '021-789012'],
            ['nama_supplier' => 'Supplier Plastik Pack', 'alamat' => 'Medan', 'no_telp' => '061-345678'],
        ];

        foreach ($suppliers as $s) {
            Supplier::create($s);
        }
    }
}
