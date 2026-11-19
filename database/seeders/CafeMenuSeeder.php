<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\ProdukOpsi;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Katalog menu kafe: 30 makanan dan minuman.
 *
 * Seeder ini idempoten. Menu lama yang tidak ada di katalog baru akan:
 * - diarsipkan (status_aktif = false) jika sudah pernah dipakai transaksi,
 * - dihapus jika belum pernah dipakai transaksi.
 *
 * Dengan begitu riwayat pesanan tetap utuh dan katalog pelanggan hanya
 * menampilkan menu baru.
 */
class CafeMenuSeeder extends Seeder
{
    private const KATEGORI = ['Kopi', 'Non-Kopi', 'Makanan', 'Snack', 'Pastry'];

    public function run(): void
    {
        $kategori = $this->siapkanKategori();
        $supplier = $this->siapkanSupplier();

        $daftarMenu = $this->daftarMenu();
        $namaMenuBaru = array_column($daftarMenu, 'nama');

        // Tentukan apakah katalog baru sudah pernah terpasang sebelum run ini.
        // Ambang 20 mencegah satu produk lama yang kebetulan bernama sama
        // (mis. "Espresso") dianggap sebagai katalog baru yang lengkap.
        // Jika sudah terpasang, pembersihan hanya menyasar produk yang lebih
        // tua dari katalog baru; menu tambahan admin setelahnya tetap aman.
        $jumlahMenuBaru = Produk::whereIn('nama_produk', $namaMenuBaru)
            ->distinct()
            ->count('nama_produk');
        $katalogPernahAda = $jumlahMenuBaru >= 20;

        [$diarsipkan, $dihapus] = DB::transaction(function () use ($daftarMenu, $namaMenuBaru, $katalogPernahAda, $kategori, $supplier) {
            foreach ($daftarMenu as $menu) {
                $gambar = 'images/menu/'.Str::slug($menu['nama']).'.jpg';

                $produk = Produk::firstOrCreate(
                    ['nama_produk' => $menu['nama']],
                    [
                        'supplier_id' => $supplier->id,
                        'kategori_produk_id' => $kategori[$menu['kategori']]->id,
                        'deskripsi' => $menu['deskripsi'],
                        'gambar' => is_file(public_path($gambar)) ? $gambar : null,
                        'harga_jual' => $menu['harga_jual'],
                        'harga_beli' => $menu['harga_beli'],
                        'tipe' => 'jual',
                        'satuan' => $menu['satuan'],
                        'stock' => $menu['stock'],
                        'status_aktif' => true,
                        'is_customizable' => $menu['customizable'],
                    ]
                );

                // Jangan timpa gambar unggahan admin. Hanya isi jika produk
                // lama belum punya gambar dan file katalog tersedia.
                if (blank($produk->gambar) && is_file(public_path($gambar))) {
                    $produk->update(['gambar' => $gambar]);
                }

                if ($menu['opsi'] !== []) {
                    $this->buatOpsi($produk, $menu['opsi']);
                }
            }

            return $this->bersihkanMenuLama($namaMenuBaru, $katalogPernahAda);
        });

        $this->command?->info(
            'Katalog kafe siap: ' . count($daftarMenu) . ' menu. ' .
            "Menu lama diarsipkan: {$diarsipkan}, dihapus: {$dihapus}."
        );
    }

    /** @return array<string, KategoriProduk> */
    private function siapkanKategori(): array
    {
        $kategori = [];

        foreach (self::KATEGORI as $nama) {
            $kategori[$nama] = KategoriProduk::firstOrCreate(['nama_kategori' => $nama]);
        }

        return $kategori;
    }

    private function siapkanSupplier(): Supplier
    {
        return Supplier::firstOrCreate(
            ['nama_supplier' => 'Supplier Utama'],
            [
                'alamat' => 'Perkoci Eatery',
                'no_telp' => '-',
            ]
        );
    }

    /**
     * @param  array<int, string>  $namaMenuBaru
     * @return array{0: int, 1: int} Jumlah menu yang diarsipkan dan dihapus.
     */
    private function bersihkanMenuLama(array $namaMenuBaru, bool $katalogPernahAda): array
    {
        $diarsipkan = 0;
        $dihapus = 0;

        $query = Produk::query()
            ->where('tipe', 'jual')
            ->where('status_aktif', true)
            ->whereNotIn('nama_produk', $namaMenuBaru);

        if ($katalogPernahAda) {
            // Jangan sentuh menu yang dibuat setelah katalog baru pertama kali
            // terpasang (mis. menu tambahan dari admin).
            $batasWaktu = Produk::whereIn('nama_produk', $namaMenuBaru)->min('created_at');

            if ($batasWaktu !== null) {
                $query->where('created_at', '<', $batasWaktu);
            }
        }

        $menuLama = $query->get();

        foreach ($menuLama as $produk) {
            if ($produk->detailTransaksis()->exists()) {
                // Jangan hapus produk yang sudah dipakai transaksi: detail
                // transaksi memakai cascade delete dan riwayat akan hilang.
                $produk->update(['status_aktif' => false]);
                $diarsipkan++;
            } else {
                $produk->delete();
                $dihapus++;
            }
        }

        return [$diarsipkan, $dihapus];
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

    /**
     * @return array<int, array{
     *     nama: string,
     *     kategori: string,
     *     deskripsi: string,
     *     harga_jual: int,
     *     harga_beli: int,
     *     satuan: string,
     *     stock: int,
     *     customizable: bool,
     *     opsi: array<int, array{kategori: string, tipe: string, is_required: int, nama_opsi: string, harga_tambahan: int}>
     * }>
     */
    private function daftarMenu(): array
    {
        return [
            // ============================== KOPI ==============================
            [
                'nama' => 'Espresso',
                'kategori' => 'Kopi',
                'deskripsi' => 'Espresso pekat dari biji arabika pilihan.',
                'harga_jual' => 13000,
                'harga_beli' => 6000,
                'satuan' => 'cup',
                'stock' => 60,
                'customizable' => true,
                'opsi' => $this->opsiEspresso(),
            ],
            [
                'nama' => 'Americano',
                'kategori' => 'Kopi',
                'deskripsi' => 'Espresso dengan air panas atau es, segar dan ringan.',
                'harga_jual' => 15000,
                'harga_beli' => 7000,
                'satuan' => 'cup',
                'stock' => 60,
                'customizable' => true,
                'opsi' => $this->opsiMinuman(),
            ],
            [
                'nama' => 'Es Kopi Susu Gula Aren',
                'kategori' => 'Kopi',
                'deskripsi' => 'Kopi susu dingin dengan gula aren asli.',
                'harga_jual' => 18000,
                'harga_beli' => 9000,
                'satuan' => 'cup',
                'stock' => 60,
                'customizable' => true,
                'opsi' => $this->opsiMinuman(false),
            ],
            [
                'nama' => 'Cafe Latte',
                'kategori' => 'Kopi',
                'deskripsi' => 'Espresso dengan susu steamed yang lembut.',
                'harga_jual' => 22000,
                'harga_beli' => 10000,
                'satuan' => 'cup',
                'stock' => 50,
                'customizable' => true,
                'opsi' => $this->opsiMinuman(),
            ],
            [
                'nama' => 'Cappuccino',
                'kategori' => 'Kopi',
                'deskripsi' => 'Espresso, susu steamed, dan foam tebal.',
                'harga_jual' => 22000,
                'harga_beli' => 10000,
                'satuan' => 'cup',
                'stock' => 50,
                'customizable' => true,
                'opsi' => $this->opsiMinuman(),
            ],
            [
                'nama' => 'Caramel Macchiato',
                'kategori' => 'Kopi',
                'deskripsi' => 'Latte dengan saus karamel manis.',
                'harga_jual' => 25000,
                'harga_beli' => 11000,
                'satuan' => 'cup',
                'stock' => 40,
                'customizable' => true,
                'opsi' => $this->opsiMinuman(),
            ],
            [
                'nama' => 'Vanilla Latte',
                'kategori' => 'Kopi',
                'deskripsi' => 'Latte dengan sirup vanila.',
                'harga_jual' => 24000,
                'harga_beli' => 11000,
                'satuan' => 'cup',
                'stock' => 40,
                'customizable' => true,
                'opsi' => $this->opsiMinuman(),
            ],
            [
                'nama' => 'Mocha Latte',
                'kategori' => 'Kopi',
                'deskripsi' => 'Perpaduan espresso, cokelat, dan susu.',
                'harga_jual' => 25000,
                'harga_beli' => 12000,
                'satuan' => 'cup',
                'stock' => 40,
                'customizable' => true,
                'opsi' => $this->opsiMinuman(),
            ],

            // ============================ NON-KOPI ============================
            [
                'nama' => 'Matcha Latte',
                'kategori' => 'Non-Kopi',
                'deskripsi' => 'Matcha premium dengan susu segar.',
                'harga_jual' => 23000,
                'harga_beli' => 10000,
                'satuan' => 'cup',
                'stock' => 50,
                'customizable' => true,
                'opsi' => $this->opsiMinuman(),
            ],
            [
                'nama' => 'Chocolate Hazelnut',
                'kategori' => 'Non-Kopi',
                'deskripsi' => 'Cokelat hazelnut creamy.',
                'harga_jual' => 22000,
                'harga_beli' => 9000,
                'satuan' => 'cup',
                'stock' => 50,
                'customizable' => true,
                'opsi' => $this->opsiMinuman(),
            ],
            [
                'nama' => 'Strawberry Milk',
                'kategori' => 'Non-Kopi',
                'deskripsi' => 'Susu segar dengan strawberry.',
                'harga_jual' => 20000,
                'harga_beli' => 8000,
                'satuan' => 'cup',
                'stock' => 50,
                'customizable' => true,
                'opsi' => $this->opsiMinuman(false),
            ],
            [
                'nama' => 'Lemon Tea',
                'kategori' => 'Non-Kopi',
                'deskripsi' => 'Teh dengan perasan lemon segar.',
                'harga_jual' => 16000,
                'harga_beli' => 6000,
                'satuan' => 'cup',
                'stock' => 50,
                'customizable' => true,
                'opsi' => $this->opsiMinuman(),
            ],
            [
                'nama' => 'Lychee Tea',
                'kategori' => 'Non-Kopi',
                'deskripsi' => 'Teh leci dingin yang menyegarkan.',
                'harga_jual' => 18000,
                'harga_beli' => 7000,
                'satuan' => 'cup',
                'stock' => 50,
                'customizable' => true,
                'opsi' => $this->opsiMinuman(),
            ],
            [
                'nama' => 'Thai Tea',
                'kategori' => 'Non-Kopi',
                'deskripsi' => 'Teh Thailand creamy khas.',
                'harga_jual' => 18000,
                'harga_beli' => 7000,
                'satuan' => 'cup',
                'stock' => 45,
                'customizable' => true,
                'opsi' => $this->opsiMinuman(),
            ],
            [
                'nama' => 'Butterfly Pea Lemonade',
                'kategori' => 'Non-Kopi',
                'deskripsi' => 'Bunga telang dengan lemon, berubah warna saat diaduk.',
                'harga_jual' => 20000,
                'harga_beli' => 8000,
                'satuan' => 'cup',
                'stock' => 45,
                'customizable' => true,
                'opsi' => $this->opsiMinuman(false),
            ],

            // ============================= MAKANAN ============================
            [
                'nama' => 'Nasi Goreng BagiKopi',
                'kategori' => 'Makanan',
                'deskripsi' => 'Nasi goreng khas BagiKopi dengan telur dan ayam.',
                'harga_jual' => 28000,
                'harga_beli' => 14000,
                'satuan' => 'porsi',
                'stock' => 30,
                'customizable' => true,
                'opsi' => array_merge($this->opsiPedas(), $this->opsiTopping([
                    'Telur Mata Sapi' => 5000,
                    'Ayam Suwir' => 8000,
                ])),
            ],
            [
                'nama' => 'Chicken Rice Bowl',
                'kategori' => 'Makanan',
                'deskripsi' => 'Nasi, ayam panggang, dan saus spesial.',
                'harga_jual' => 32000,
                'harga_beli' => 16000,
                'satuan' => 'porsi',
                'stock' => 30,
                'customizable' => true,
                'opsi' => array_merge($this->opsiPedas(), $this->opsiTopping([
                    'Extra Chicken' => 8000,
                    'Extra Rice' => 4000,
                ])),
            ],
            [
                'nama' => 'Beef Rice Bowl',
                'kategori' => 'Makanan',
                'deskripsi' => 'Nasi dengan beef slice dan saus teriyaki.',
                'harga_jual' => 38000,
                'harga_beli' => 20000,
                'satuan' => 'porsi',
                'stock' => 25,
                'customizable' => true,
                'opsi' => array_merge($this->opsiPedas(), $this->opsiTopping([
                    'Extra Beef' => 10000,
                    'Extra Rice' => 4000,
                ])),
            ],
            [
                'nama' => 'Spaghetti Aglio Olio',
                'kategori' => 'Makanan',
                'deskripsi' => 'Spaghetti dengan bawang putih, cabai, dan olive oil.',
                'harga_jual' => 30000,
                'harga_beli' => 14000,
                'satuan' => 'porsi',
                'stock' => 25,
                'customizable' => true,
                'opsi' => array_merge($this->opsiPedas(), $this->opsiTopping([
                    'Smoked Beef' => 8000,
                    'Keju Parut' => 5000,
                ])),
            ],
            [
                'nama' => 'Chicken Carbonara',
                'kategori' => 'Makanan',
                'deskripsi' => 'Spaghetti creamy dengan ayam dan keju.',
                'harga_jual' => 35000,
                'harga_beli' => 17000,
                'satuan' => 'porsi',
                'stock' => 25,
                'customizable' => true,
                'opsi' => $this->opsiTopping([
                    'Extra Chicken' => 8000,
                    'Keju Parut' => 5000,
                ]),
            ],
            [
                'nama' => 'Club Sandwich',
                'kategori' => 'Makanan',
                'deskripsi' => 'Sandwich berlapis ayam, telur, dan sayuran.',
                'harga_jual' => 30000,
                'harga_beli' => 14000,
                'satuan' => 'porsi',
                'stock' => 25,
                'customizable' => true,
                'opsi' => $this->opsiTopping([
                    'Extra Telur' => 5000,
                    'Extra Keju' => 5000,
                ]),
            ],
            [
                'nama' => 'Mie Goreng Jawa',
                'kategori' => 'Makanan',
                'deskripsi' => 'Mie goreng dengan bumbu Jawa dan telur.',
                'harga_jual' => 26000,
                'harga_beli' => 12000,
                'satuan' => 'porsi',
                'stock' => 30,
                'customizable' => true,
                'opsi' => array_merge($this->opsiPedas(), $this->opsiTopping([
                    'Telur Ceplok' => 5000,
                    'Ayam Suwir' => 8000,
                ])),
            ],

            // ============================== SNACK =============================
            [
                'nama' => 'French Fries',
                'kategori' => 'Snack',
                'deskripsi' => 'Kentang goreng renyah dengan saus pilihan.',
                'harga_jual' => 18000,
                'harga_beli' => 7000,
                'satuan' => 'porsi',
                'stock' => 30,
                'customizable' => true,
                'opsi' => $this->opsiSaus([
                    'Mayonaise' => 0,
                    'Sambal' => 0,
                    'BBQ' => 0,
                ]),
            ],
            [
                'nama' => 'Chicken Wings',
                'kategori' => 'Snack',
                'deskripsi' => 'Sayap ayam goreng dengan saus pilihan.',
                'harga_jual' => 28000,
                'harga_beli' => 13000,
                'satuan' => 'porsi',
                'stock' => 25,
                'customizable' => true,
                'opsi' => $this->opsiSaus([
                    'BBQ' => 0,
                    'Buffalo' => 0,
                    'Madu' => 0,
                ]),
            ],
            [
                'nama' => 'Dimsum Ayam',
                'kategori' => 'Snack',
                'deskripsi' => 'Dimsum ayam kukus dengan saus.',
                'harga_jual' => 22000,
                'harga_beli' => 10000,
                'satuan' => 'porsi',
                'stock' => 30,
                'customizable' => true,
                'opsi' => $this->opsiSaus([
                    'Saus Dimsum' => 0,
                    'Sambal' => 0,
                ]),
            ],
            [
                'nama' => 'Onion Rings',
                'kategori' => 'Snack',
                'deskripsi' => 'Bawang bombay goreng renyah.',
                'harga_jual' => 18000,
                'harga_beli' => 7000,
                'satuan' => 'porsi',
                'stock' => 30,
                'customizable' => true,
                'opsi' => $this->opsiSaus([
                    'Mayonaise' => 0,
                    'BBQ' => 0,
                ]),
            ],

            // ============================= PASTRY =============================
            [
                'nama' => 'Butter Croissant',
                'kategori' => 'Pastry',
                'deskripsi' => 'Croissant mentega renyah.',
                'harga_jual' => 20000,
                'harga_beli' => 9000,
                'satuan' => 'pcs',
                'stock' => 24,
                'customizable' => true,
                'opsi' => [
                    ['kategori' => 'Pemanas', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Hangat', 'harga_tambahan' => 0],
                    ['kategori' => 'Pemanas', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Dingin', 'harga_tambahan' => 0],
                    ['kategori' => 'Topping', 'tipe' => 'multiple', 'is_required' => 0, 'nama_opsi' => 'Extra Butter', 'harga_tambahan' => 3000],
                    ['kategori' => 'Topping', 'tipe' => 'multiple', 'is_required' => 0, 'nama_opsi' => 'Keju Parut', 'harga_tambahan' => 4000],
                ],
            ],
            [
                'nama' => 'Pain au Chocolat',
                'kategori' => 'Pastry',
                'deskripsi' => 'Pastry berlapis cokelat.',
                'harga_jual' => 22000,
                'harga_beli' => 10000,
                'satuan' => 'pcs',
                'stock' => 24,
                'customizable' => true,
                'opsi' => [
                    ['kategori' => 'Pemanas', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Hangat', 'harga_tambahan' => 0],
                    ['kategori' => 'Pemanas', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Dingin', 'harga_tambahan' => 0],
                    ['kategori' => 'Topping', 'tipe' => 'multiple', 'is_required' => 0, 'nama_opsi' => 'Extra Cokelat', 'harga_tambahan' => 4000],
                ],
            ],
            [
                'nama' => 'Cinnamon Roll',
                'kategori' => 'Pastry',
                'deskripsi' => 'Roti gulung kayu manis dengan cream cheese.',
                'harga_jual' => 20000,
                'harga_beli' => 9000,
                'satuan' => 'pcs',
                'stock' => 24,
                'customizable' => true,
                'opsi' => [
                    ['kategori' => 'Pemanas', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Hangat', 'harga_tambahan' => 0],
                    ['kategori' => 'Pemanas', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Dingin', 'harga_tambahan' => 0],
                    ['kategori' => 'Topping', 'tipe' => 'multiple', 'is_required' => 0, 'nama_opsi' => 'Extra Cream Cheese', 'harga_tambahan' => 5000],
                ],
            ],
            [
                'nama' => 'Banana Cake',
                'kategori' => 'Pastry',
                'deskripsi' => 'Bolu pisang lembut.',
                'harga_jual' => 18000,
                'harga_beli' => 8000,
                'satuan' => 'pcs',
                'stock' => 24,
                'customizable' => true,
                'opsi' => [
                    ['kategori' => 'Pemanas', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Hangat', 'harga_tambahan' => 0],
                    ['kategori' => 'Pemanas', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Dingin', 'harga_tambahan' => 0],
                    ['kategori' => 'Topping', 'tipe' => 'multiple', 'is_required' => 0, 'nama_opsi' => 'Extra Pisang', 'harga_tambahan' => 4000],
                    ['kategori' => 'Topping', 'tipe' => 'multiple', 'is_required' => 0, 'nama_opsi' => 'Es Krim', 'harga_tambahan' => 6000],
                ],
            ],
        ];
    }

    /** @return array<int, array{kategori: string, tipe: string, is_required: int, nama_opsi: string, harga_tambahan: int}> */
    private function opsiEspresso(): array
    {
        return [
            ['kategori' => 'Ekstra Shot', 'tipe' => 'multiple', 'is_required' => 0, 'nama_opsi' => 'Extra Shot', 'harga_tambahan' => 5000],
        ];
    }

    /** @return array<int, array{kategori: string, tipe: string, is_required: int, nama_opsi: string, harga_tambahan: int}> */
    private function opsiMinuman(bool $suhu = true): array
    {
        $opsi = [
            ['kategori' => 'Gula', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Normal', 'harga_tambahan' => 0],
            ['kategori' => 'Gula', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Less Sugar', 'harga_tambahan' => 0],
            ['kategori' => 'Gula', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'No Sugar', 'harga_tambahan' => 0],
            ['kategori' => 'Ukuran', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Regular', 'harga_tambahan' => 0],
            ['kategori' => 'Ukuran', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Large', 'harga_tambahan' => 5000],
        ];

        if ($suhu) {
            $opsi[] = ['kategori' => 'Suhu', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Panas', 'harga_tambahan' => 0];
            $opsi[] = ['kategori' => 'Suhu', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Dingin', 'harga_tambahan' => 0];
        }

        return $opsi;
    }

    /** @return array<int, array{kategori: string, tipe: string, is_required: int, nama_opsi: string, harga_tambahan: int}> */
    private function opsiPedas(): array
    {
        return [
            ['kategori' => 'Level Pedas', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Tidak Pedas', 'harga_tambahan' => 0],
            ['kategori' => 'Level Pedas', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Sedang', 'harga_tambahan' => 0],
            ['kategori' => 'Level Pedas', 'tipe' => 'single', 'is_required' => 1, 'nama_opsi' => 'Pedas', 'harga_tambahan' => 0],
        ];
    }

    /**
     * @param  array<string, int>  $topping
     * @return array<int, array{kategori: string, tipe: string, is_required: int, nama_opsi: string, harga_tambahan: int}>
     */
    private function opsiTopping(array $topping): array
    {
        $opsi = [];

        foreach ($topping as $nama => $harga) {
            $opsi[] = [
                'kategori' => 'Topping',
                'tipe' => 'multiple',
                'is_required' => 0,
                'nama_opsi' => $nama,
                'harga_tambahan' => $harga,
            ];
        }

        return $opsi;
    }

    /**
     * @param  array<string, int>  $saus
     * @return array<int, array{kategori: string, tipe: string, is_required: int, nama_opsi: string, harga_tambahan: int}>
     */
    private function opsiSaus(array $saus): array
    {
        $opsi = [];

        foreach ($saus as $nama => $harga) {
            $opsi[] = [
                'kategori' => 'Saus',
                'tipe' => 'multiple',
                'is_required' => 0,
                'nama_opsi' => $nama,
                'harga_tambahan' => $harga,
            ];
        }

        return $opsi;
    }
}
