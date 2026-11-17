<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\KategoriProduk;
use App\Models\Supplier;
use App\Models\Produk;
use App\Models\Meja;
use App\Models\PromoKode;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->buatAdmin();
        $kategori = $this->buatKategori();
        $supplier = $this->buatSupplier();
        $this->call(CafeMenuSeeder::class);
        $this->buatBahanBaku($kategori, $supplier);
        $this->buatMeja();
        $this->buatPromo();
    }

    private function buatAdmin(): void
    {
        $admin = Karyawan::firstOrNew(['username' => 'admin']);

        if (! $admin->exists) {
            $admin->fill([
                'nama_karyawan' => 'Administrator',
                'jabatan' => 'Admin',
                'no_telepon' => '081234567890',
                'alamat' => 'Perkoci Eatery, Soreang, Bandung',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ])->save();
        }
    }

    /** @return array{kopi: KategoriProduk, pastry: KategoriProduk} */
    private function buatKategori(): array
    {
        return [
            'kopi' => KategoriProduk::firstOrCreate(['nama_kategori' => 'Kopi']),
            'pastry' => KategoriProduk::firstOrCreate(['nama_kategori' => 'Pastry']),
        ];
    }

    /** @return array{kopi: Supplier, pastry: Supplier} */
    private function buatSupplier(): array
    {
        return [
            'kopi' => Supplier::firstOrCreate(
                ['nama_supplier' => 'Supplier Kopi Nusantara'],
                ['alamat' => 'Bandung', 'no_telp' => '08111000111']
            ),
            'pastry' => Supplier::firstOrCreate(
                ['nama_supplier' => 'Supplier Pastry Bandung'],
                ['alamat' => 'Bandung', 'no_telp' => '08222000222']
            ),
        ];
    }

    private function buatBahanBaku(array $kategori, array $supplier): void
    {
        Produk::firstOrCreate(
            ['nama_produk' => 'Biji Kopi Arabika'],
            [
                'supplier_id' => $supplier['kopi']->id,
                'kategori_produk_id' => $kategori['kopi']->id,
                'deskripsi' => 'Biji kopi arabika premium.',
                'harga_jual' => 0,
                'harga_beli' => 250000,
                'tipe' => 'bahan baku',
                'satuan' => 'kg',
                'stock' => 100,
                'status_aktif' => true,
                'is_customizable' => false,
            ]
        );

        Produk::firstOrCreate(
            ['nama_produk' => 'Susu Segar'],
            [
                'supplier_id' => $supplier['pastry']->id,
                'kategori_produk_id' => $kategori['pastry']->id,
                'deskripsi' => 'Susu segar full cream.',
                'harga_jual' => 0,
                'harga_beli' => 20000,
                'tipe' => 'bahan baku',
                'satuan' => 'liter',
                'stock' => 200,
                'status_aktif' => true,
                'is_customizable' => false,
            ]
        );
    }

    private function buatMeja(): void
    {
        Meja::firstOrCreate(['nomor_meja' => 1], ['kapasitas' => 4, 'status_aktif' => true]);
        Meja::firstOrCreate(['nomor_meja' => 2], ['kapasitas' => 2, 'status_aktif' => true]);
    }

    private function buatPromo(): void
    {
        PromoKode::firstOrCreate(
            ['kode' => 'HEMAT10'],
            [
                'nama_promo' => 'Diskon 10%',
                'nilai_diskon' => 10,
                'min_belanja' => 30000,
                'maks_diskon' => 15000,
                'tanggal_mulai' => Carbon::now(),
                'tanggal_berakhir' => Carbon::now()->addDays(30),
                'kuota' => 50,
                'kuota_terpakai' => 0,
                'is_aktif' => true,
            ]
        );

        PromoKode::firstOrCreate(
            ['kode' => 'PROMO5K'],
            [
                'nama_promo' => 'Diskon 5%',
                'nilai_diskon' => 5,
                'min_belanja' => 20000,
                'maks_diskon' => 10000,
                'tanggal_mulai' => Carbon::now(),
                'tanggal_berakhir' => Carbon::now()->addDays(30),
                'kuota' => 20,
                'kuota_terpakai' => 0,
                'is_aktif' => true,
            ]
        );
    }
}
