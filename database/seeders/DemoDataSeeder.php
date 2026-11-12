<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\KategoriProduk;
use App\Models\Supplier;
use App\Models\Produk;
use App\Models\ProdukOpsi;
use App\Models\Meja;
use App\Models\PromoKode;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Data demo lengkap: admin, kategori, supplier, menu + kustomisasi,
 * bahan baku, meja, dan kode promo.
 *
 * Idempoten (firstOrCreate/firstOrNew) sehingga aman dijalankan berulang
 * dan tidak menimpa data yang sudah ada.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->buatAdmin();
        $kategori = $this->buatKategori();
        $supplier = $this->buatSupplier();
        $this->buatMenu($kategori, $supplier);
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

    private function buatMenu(array $kategori, array $supplier): void
    {
        $kopiSusu = Produk::firstOrCreate(
            ['nama_produk' => 'Kopi Susu Gula Aren'],
            [
                'supplier_id' => $supplier['kopi']->id,
                'kategori_produk_id' => $kategori['kopi']->id,
                'deskripsi' => 'Kopi susu dengan gula aren asli.',
                'harga_jual' => 18000,
                'harga_beli' => 9000,
                'tipe' => 'jual',
                'satuan' => 'cup',
                'stock' => 50,
                'status_aktif' => true,
                'is_customizable' => true,
            ]
        );
        $this->buatOpsi($kopiSusu, [
            ['kategori' => 'Gula', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Less Sugar', 'harga_tambahan' => 0],
            ['kategori' => 'Gula', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Extra Sugar', 'harga_tambahan' => 2000],
            ['kategori' => 'Ukuran', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Regular', 'harga_tambahan' => 0],
            ['kategori' => 'Ukuran', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Large', 'harga_tambahan' => 5000],
        ]);

        $croissant = Produk::firstOrCreate(
            ['nama_produk' => 'Croissant Butter'],
            [
                'supplier_id' => $supplier['pastry']->id,
                'kategori_produk_id' => $kategori['pastry']->id,
                'deskripsi' => 'Croissant mentega renyah.',
                'harga_jual' => 20000,
                'harga_beli' => 10000,
                'tipe' => 'jual',
                'satuan' => 'pcs',
                'stock' => 30,
                'status_aktif' => true,
                'is_customizable' => true,
            ]
        );
        $this->buatOpsi($croissant, [
            ['kategori' => 'Suhu', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Panas', 'harga_tambahan' => 0],
            ['kategori' => 'Suhu', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Dingin', 'harga_tambahan' => 0],
            ['kategori' => 'Topping', 'tipe' => 'multiple', 'is_required' => 0, 'nama_opsi' => 'Extra Cheese', 'harga_tambahan' => 5000],
            ['kategori' => 'Topping', 'tipe' => 'multiple', 'is_required' => 0, 'nama_opsi' => 'Chocolate Drizzle', 'harga_tambahan' => 6000],
        ]);
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

    /** @param array<int, array{kategori: string, tipe: string, is_required: int, nama_opsi: string, harga_tambahan: int}> $daftar */
    private function buatOpsi(Produk $produk, array $daftar): void
    {
        foreach ($daftar as $opsi) {
            ProdukOpsi::firstOrCreate(
                [
                    'produk_id' => $produk->id,
                    'kategori' => $opsi['kategori'],
                    'nama_opsi' => $opsi['nama_opsi'],
                ],
                [
                    'tipe' => $opsi['tipe'],
                    'is_required' => $opsi['is_required'],
                    'harga_tambahan' => $opsi['harga_tambahan'],
                ]
            );
        }
    }
}
