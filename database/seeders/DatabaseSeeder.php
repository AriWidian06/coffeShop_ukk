<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\KategoriProduk;
use App\Models\Meja;
use App\Models\Pembayaran;
use App\Models\Produk;
use App\Models\ProdukOpsi;
use App\Models\PromoKode;
use App\Models\Supplier;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private const KATEGORI = ['Kopi', 'Non-Kopi', 'Makanan', 'Snack', 'Pastry'];
    private const SESSION_PREFIX = 'BIGDATA-';
    private const TOTAL = 1200;

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
        $this->seedCafeMenu();

        // Data master besar & transaksi besar hanya dijalankan di luar environment
        // testing, agar suite test tetap cepat dan database uji tetap kosong.
        if (! app()->environment('testing')) {
            $this->seedBigMasterData();
            $this->seedBigData();
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

    private function seedCafeMenu(): void
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

    private function seedBigMasterData(): void
    {
        DB::transaction(function () {
            $this->seedKategori();
            $this->seedSupplier();
            $this->seedMeja();
            $this->seedKaryawan();
            $this->seedPromo();
            $this->seedProdukBahanBaku();
        });

        $this->command?->info('Master data besar selesai ditambahkan.');
    }

    private function seedKategori(): void
    {
        $nama = [
            'Minuman Kopi', 'Minuman Non-Kopi', 'Makanan Ringan', 'Makanan Berat',
            'Bahan Baku', 'Dessert', 'Pastry', 'Snack', 'Minuman Dingin',
            'Minuman Panas', 'Topping', 'Sirup', 'Susu & Creamer', 'Biji Kopi',
            'Teh & Herbal',
        ];
        foreach ($nama as $n) {
            KategoriProduk::firstOrCreate(['nama_kategori' => $n]);
        }
    }

    private function seedSupplier(): void
    {
        $kota = ['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Semarang', 'Yogyakarta', 'Malang', 'Denpasar', 'Makassar', 'Palembang'];
        $prefix = ['PT', 'CV', 'UD', 'Toko'];
        $bidang = ['Kopi Nusantara', 'Susu Segar', 'Bahan Kue Jaya', 'Plastik Pack', 'Gula Aren', 'Coklat', 'Teh', 'Sirup', 'Kemasan', 'Rempah'];
        $i = 0;
        while (Supplier::count() < 30) {
            $nama = $prefix[$i % count($prefix)].' '.$bidang[$i % count($bidang)].' '.($i + 1);
            Supplier::firstOrCreate(
                ['nama_supplier' => $nama],
                [
                    'alamat' => $kota[$i % count($kota)],
                    'no_telp' => '0'.rand(21, 89).'-'.rand(100000, 999999),
                ]
            );
            $i++;
            if ($i > 200) {
                break;
            }
        }
    }

    private function seedMeja(): void
    {
        $max = (int) Meja::max('nomor_meja');
        $start = $max + 1;
        for ($n = $start; $n < $start + 30; $n++) {
            Meja::firstOrCreate(
                ['nomor_meja' => $n],
                ['kapasitas' => [2, 2, 4, 4, 4, 6, 6, 8][array_rand([2, 2, 4, 4, 4, 6, 6, 8])], 'status_aktif' => true]
            );
        }
    }

    private function seedKaryawan(): void
    {
        $roles = ['kasir', 'kasir', 'kasir', 'kasir', 'manager', 'manager'];
        $jabatan = [
            'kasir' => 'Kasir',
            'manager' => 'Manager',
        ];
        $firstNames = ['Andi', 'Budi', 'Citra', 'Dewi', 'Eko', 'Fajar', 'Gita', 'Hadi', 'Intan', 'Joko', 'Kiki', 'Lina', 'Maya', 'Nanda', 'Oki', 'Putri', 'Qori', 'Rina', 'Sari', 'Tono', 'Umi', 'Vina', 'Wawan', 'Xena', 'Yoga', 'Zaki'];
        $lastNames = ['Santoso', 'Wijaya', 'Pratama', 'Sari', 'Putra', 'Lestari', 'Nugroho', 'Hidayat', 'Rahmawati', 'Kurniawan'];
        $i = 0;
        while (Karyawan::count() < 45) {
            $role = $roles[array_rand($roles)];
            $nama = $firstNames[array_rand($firstNames)].' '.$lastNames[array_rand($lastNames)];
            $username = 'user'.($i + 100);
            if (Karyawan::where('username', $username)->exists()) {
                $i++;
                continue;
            }
            Karyawan::create([
                'nama_karyawan' => $nama,
                'jabatan' => $jabatan[$role],
                'role' => $role,
                'username' => $username,
                'password' => Hash::make('password123'),
                'no_telepon' => '08'.rand(1000000000, 9999999999),
                'alamat' => 'Bandung',
            ]);
            $i++;
            if ($i > 200) {
                break;
            }
        }
    }

    private function seedPromo(): void
    {
        $promos = [
            ['kode' => 'HEMAT20', 'nama_promo' => 'Diskon 20%', 'nilai_diskon' => 20, 'min_belanja' => 50000, 'maks_diskon' => 25000],
            ['kode' => 'KOPI15', 'nama_promo' => 'Diskon Kopi 15%', 'nilai_diskon' => 15, 'min_belanja' => 30000, 'maks_diskon' => 15000],
            ['kode' => 'WEEKEND10', 'nama_promo' => 'Weekend 10%', 'nilai_diskon' => 10, 'min_belanja' => 20000, 'maks_diskon' => 10000],
            ['kode' => 'NEWCUST25', 'nama_promo' => 'Pelanggan Baru 25%', 'nilai_diskon' => 25, 'min_belanja' => 40000, 'maks_diskon' => 30000],
            ['kode' => 'BUYNOW5', 'nama_promo' => 'Langsung 5%', 'nilai_diskon' => 5, 'min_belanja' => 10000, 'maks_diskon' => 5000],
            ['kode' => 'BIGORDER30', 'nama_promo' => 'Order Besar 30%', 'nilai_diskon' => 30, 'min_belanja' => 100000, 'maks_diskon' => 50000],
            ['kode' => 'MORNING7', 'nama_promo' => 'Pagi Hemat 7%', 'nilai_diskon' => 7, 'min_belanja' => 15000, 'maks_diskon' => 7000],
            ['kode' => 'AFTERNOON12', 'nama_promo' => 'Sore Hemat 12%', 'nilai_diskon' => 12, 'min_belanja' => 25000, 'maks_diskon' => 12000],
        ];
        $start = Carbon::now()->subMonths(2)->startOfDay();
        $end = Carbon::now()->addMonths(6)->endOfDay();
        foreach ($promos as $p) {
            if (PromoKode::where('kode', $p['kode'])->exists()) {
                continue;
            }
            PromoKode::create([
                'kode' => $p['kode'],
                'nama_promo' => $p['nama_promo'],
                'nilai_diskon' => $p['nilai_diskon'],
                'min_belanja' => $p['min_belanja'],
                'maks_diskon' => $p['maks_diskon'],
                'tanggal_mulai' => $start,
                'tanggal_berakhir' => $end,
                'kuota' => rand(50, 200),
                'kuota_terpakai' => 0,
                'is_aktif' => true,
            ]);
        }
    }

    private function seedProdukBahanBaku(): void
    {
        $kategoriBahan = KategoriProduk::where('nama_kategori', 'Bahan Baku')->first();
        if (! $kategoriBahan) {
            return;
        }
        $supplier = Supplier::first();
        $bahan = [
            ['Biji Kopi Arabika 1kg', 120000, 90000],
            ['Biji Kopi Robusta 1kg', 90000, 65000],
            ['Susu UHT 1L', 18000, 14000],
            ['Sirup Vanilla', 45000, 32000],
            ['Sirup Caramel', 45000, 32000],
            ['Gula Aren 500g', 25000, 18000],
            ['Coklat Bubuk 500g', 55000, 40000],
            ['Whipping Cream 500ml', 35000, 26000],
            ['Cup Plastik 12oz (50pcs)', 40000, 28000],
            ['Sedotan (100pcs)', 15000, 10000],
        ];
        foreach ($bahan as [$nama, $jual, $beli]) {
            Produk::firstOrCreate(
                ['nama_produk' => $nama],
                [
                    'supplier_id' => $supplier?->id,
                    'kategori_produk_id' => $kategoriBahan->id,
                    'deskripsi' => 'Bahan baku kafe',
                    'harga_jual' => $jual,
                    'harga_beli' => $beli,
                    'tipe' => 'bahan baku',
                    'satuan' => 'pcs',
                    'stock' => rand(20, 80),
                    'status_aktif' => true,
                    'is_customizable' => false,
                ]
            );

        }
    }

    private function seedBigData(): void
    {
        if (Transaksi::where('session_id', 'like', self::SESSION_PREFIX.'%')->exists()) {
            $this->command?->warn('Data besar sudah ada. Seeder dilewati agar tidak membuat data ganda.');
            return;
        }

        $produks = Produk::where('tipe', 'jual')->where('status_aktif', true)->orderBy('id')->get();
        if ($produks->isEmpty()) {
            $this->call(DemoDataSeeder::class);
            $produks = Produk::where('tipe', 'jual')->where('status_aktif', true)->orderBy('id')->get();
        }

        $staffs = Karyawan::whereIn('role', ['admin', 'manager', 'kasir'])->get();
        if ($staffs->isEmpty()) {
            Karyawan::create([
                'nama_karyawan' => 'Administrator',
                'jabatan' => 'Admin',
                'role' => 'admin',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
            ]);
            $staffs = Karyawan::whereIn('role', ['admin', 'manager', 'kasir'])->get();
        }

        $mejas = Meja::where('status_aktif', true)->orderBy('nomor_meja')->get();
        if ($mejas->isEmpty()) {
            Meja::create(['nomor_meja' => 1, 'kapasitas' => 4, 'status_aktif' => true]);
            Meja::create(['nomor_meja' => 2, 'kapasitas' => 2, 'status_aktif' => true]);
            $mejas = Meja::where('status_aktif', true)->orderBy('nomor_meja')->get();
        }

        $promo = PromoKode::where('is_aktif', true)->first();

        $hourPool = [];
        foreach ([8 => 2, 9 => 6, 10 => 7, 11 => 5, 12 => 8, 13 => 7, 14 => 6, 15 => 5, 16 => 4, 17 => 5, 18 => 6, 19 => 7, 20 => 4, 21 => 2] as $h => $w) {
            for ($i = 0; $i < $w; $i++) {
                $hourPool[] = $h;
            }
        }

        $tz = 'Asia/Jakarta';
        $nowWib = Carbon::now($tz);
        $startDay = $nowWib->copy()->subDays(89)->startOfDay();

        mt_srand(20261119);

        $statuses = ['completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'pending', 'prosesed', 'ready', 'cancelled'];
        $metodes = ['cash', 'cash', 'cash', 'QRIS', 'QRIS'];

        $total = self::TOTAL;
        $this->command?->info("Membuat {$total} transaksi besar...");

        $batchSize = 100;
        $occupiedMejas = [];
        for ($batch = 0; $batch < (int) ceil($total / $batchSize); $batch++) {
            $count = min($batchSize, $total - $batch * $batchSize);

            DB::transaction(function () use ($batch, $batchSize, $count, $hourPool, $mejas, $metodes, $nowWib, $produks, $promo, $staffs, $startDay, $statuses, $tz, &$occupiedMejas) {
                for ($i = 0; $i < $count; $i++) {
                    $globalIndex = $batch * $batchSize + $i;

                    $isCancelled = (mt_rand(1, 100) <= 6);
                    $status = $isCancelled ? 'cancelled' : $statuses[mt_rand(0, count($statuses) - 2)];
                    $isCustomerOrder = (mt_rand(1, 100) <= 35);
                    $isTakeAway = (mt_rand(1, 100) <= 55);

                    $dayOffset = (int) (pow(mt_rand() / mt_getrandmax(), 0.6) * 89);
                    $dayOffset = min(89, max(0, $dayOffset));
                    $date = $startDay->copy()->addDays($dayOffset);

                    $hour = $hourPool[array_rand($hourPool)];
                    $minute = mt_rand(0, 59);
                    $waktuWib = $date->copy()->addHours($hour)->addMinutes($minute);
                    if ($waktuWib->isFuture()) {
                        $waktuWib = $nowWib->copy()->subMinutes(mt_rand(5, 600));
                    }
                    $waktu = $waktuWib->copy()->utc();

                    $meja = null;
                    $isActiveOrder = in_array($status, ['pending', 'ready', 'prosesed'], true);

                    if (! $isTakeAway) {
                        if ($isActiveOrder) {
                            // Hindari double booking: pilih meja yang belum terisi pesanan aktif.
                            $freeMejas = $mejas->filter(fn ($m) => ! in_array($m->id, $occupiedMejas, true))->values();
                            if ($freeMejas->isEmpty()) {
                                $isTakeAway = true;
                            } else {
                                $meja = $freeMejas->random();
                                $occupiedMejas[] = $meja->id;
                            }
                        } else {
                            $meja = $mejas->random();
                        }
                    }
                    $mejaId = $meja?->id;

                    $sessionId = self::SESSION_PREFIX.str_pad((string) ($globalIndex + 1), 6, '0', STR_PAD_LEFT);

                    $itemCount = mt_rand(1, 4);
                    $chosen = $produks->random($itemCount);
                    $details = [];
                    $subtotal = 0.0;
                    foreach ($chosen as $produk) {
                        $qty = mt_rand(1, 3);
                        $harga = (float) $produk->harga_jual;
                        $subtotal += $harga * $qty;
                        $details[] = ['produk' => $produk, 'qty' => $qty, 'subtotal' => round($harga * $qty, 2)];
                    }
                    $subtotal = round($subtotal, 2);

                    $diskon = 0.0;
                    $usePromo = $promo !== null
                        && ! $isCancelled
                        && mt_rand(1, 100) <= 12
                        && $subtotal >= 30000;

                    if ($usePromo) {
                        $hasilPromo = $promo->eligibleFor($subtotal, $mejaId, $sessionId);
                        if ($hasilPromo['ok']) {
                            $diskon = (float) $hasilPromo['diskon'];
                            $promo->increment('kuota_terpakai');
                        } else {
                            $usePromo = false;
                        }
                    }

                    $totalHarga = round(max(0, $subtotal - $diskon), 2);

                    $transaksi = Transaksi::create([
                        'meja_id' => $mejaId,
                        'karyawan_id' => $isCustomerOrder ? null : $staffs->random()->id,
                        'sumber_pesanan' => $isCustomerOrder ? 'pelanggan' : 'kasir',
                        'catatan' => (mt_rand(1, 100) <= 20) ? 'Less sugar' : null,
                        'tipe_pesanan' => $isTakeAway ? 'take-away' : 'dine-in',
                        'total_harga' => $totalHarga,
                        'status_pesanan' => $status,
                        'sudah_dibalik' => $isCancelled,
                        'waktu_transaksi' => $waktu,
                        'promo_kode_id' => $usePromo ? $promo->id : null,
                        'diskon' => $diskon,
                        'session_id' => $sessionId,
                    ]);

                    foreach ($details as $detail) {
                        $transaksi->detail_transaksis()->create([
                            'produk_id' => $detail['produk']->id,
                            'QTY' => $detail['qty'],
                            'subtotal' => $detail['subtotal'],
                            'customization' => (mt_rand(1, 100) <= 15) ? 'Less sugar' : null,
                        ]);

                        if (! $isCancelled) {
                            Produk::whereKey($detail['produk']->id)->decrement('stock', $detail['qty']);
                        }
                    }

                    $metode = $metodes[array_rand($metodes)];
                    $dibayar = in_array($status, ['completed', 'ready', 'prosesed'], true);
                    if ($metode === 'cash' && $dibayar) {
                        $jumlahBayar = $totalHarga > 0 ? ceil($totalHarga / 5000) * 5000 : 0;
                        $kembalian = max(0, $jumlahBayar - $totalHarga);
                    } else {
                        $jumlahBayar = $totalHarga;
                        $kembalian = 0;
                    }

                    Pembayaran::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah_bayar' => $jumlahBayar,
                        'metode_pembayaran' => $metode,
                        'status_pembayaran' => $dibayar ? 'paid' : 'pending',
                        'kembalian' => $kembalian,
                        'waktu_pembayaran' => $waktu->copy()->addMinutes(mt_rand(2, 20)),
                    ]);

                    if ($usePromo) {
                        PromoUsage::create([
                            'promo_kode_id' => $promo->id,
                            'meja_id' => $mejaId,
                            'transaksi_id' => $transaksi->id,
                            'session_id' => $sessionId,
                            'discount_amount' => $diskon,
                            'status' => $status === 'completed' ? 'released' : 'active',
                            'used_at' => $waktu,
                            'released_at' => $status === 'completed' ? $waktu->copy()->addMinutes(30) : null,
                        ]);
                    }
                }
            });

            $this->command?->info('  batch '.($batch + 1).' selesai ('.min(($batch + 1) * $batchSize, $total).'/'.$total.')');
        }

        mt_srand();
        $this->command?->info('Selesai: '.Transaksi::count().' total transaksi di database.');

    }

}
