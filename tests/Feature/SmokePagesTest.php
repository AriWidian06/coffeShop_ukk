<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\Meja;
use App\Models\Produk;
use App\Models\PromoKode;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_main_pages_render_for_each_role(): void
    {
        $this->seed();

        $admin = Karyawan::where('username', 'admin')->firstOrFail();
        $kasir = Karyawan::create([
            'nama_karyawan' => 'Kasir Smoke',
            'jabatan' => 'Kasir',
            'role' => 'kasir',
            'username' => 'kasir_smoke',
            'password' => 'password123',
        ]);
        $manager = Karyawan::create([
            'nama_karyawan' => 'Manager Smoke',
            'jabatan' => 'Manager',
            'role' => 'manager',
            'username' => 'manager_smoke',
            'password' => 'password123',
        ]);

        $produk = Produk::create([
            'supplier_id' => \App\Models\Supplier::firstOrFail()->id,
            'kategori_produk_id' => \App\Models\KategoriProduk::firstOrFail()->id,
            'nama_produk' => 'Produk Smoke',
            'harga_jual' => 20000,
            'harga_beli' => 10000,
            'tipe' => 'jual',
            'satuan' => 'pcs',
            'stock' => 2,
            'status_aktif' => true,
        ]);
        $meja = Meja::create(['nomor_meja' => 1, 'kapasitas' => 4, 'status_aktif' => true]);
        $promo = PromoKode::create([
            'kode' => 'SMOKE10',
            'nama_promo' => 'Promo Smoke',
            'nilai_diskon' => 10,
            'min_belanja' => 0,
            'maks_diskon' => 10000,
            'tanggal_mulai' => now()->subDay(),
            'tanggal_berakhir' => now()->addDay(),
            'kuota' => 10,
            'kuota_terpakai' => 0,
            'is_aktif' => true,
        ]);

        $transaksi = Transaksi::create([
            'meja_id' => $meja->id,
            'karyawan_id' => $kasir->id,
            'sumber_pesanan' => 'kasir',
            'tipe_pesanan' => 'dine-in',
            'total_harga' => 22000,
            'status_pesanan' => 'completed',
            'waktu_transaksi' => now(),
        ]);
        $transaksi->detail_transaksis()->create([
            'produk_id' => $produk->id,
            'QTY' => 1,
            'subtotal' => 22000,
        ]);
        $transaksi->pembayaran()->create([
            'jumlah_bayar' => 22000,
            'metode_pembayaran' => 'cash',
            'status_pembayaran' => 'paid',
            'kembalian' => 0,
            'waktu_pembayaran' => now(),
        ]);

        $adminPages = [
            '/dashboard',
            '/produks',
            '/produks/create',
            '/produks/'.$produk->id.'/edit',
            '/kategori-produks',
            '/kategori-produks/create',
            '/mejas',
            '/mejas/create',
            '/mejas/'.$meja->id.'/edit',
            '/suppliers',
            '/suppliers/create',
            '/karyawans',
            '/karyawans/create',
            '/promo-kodes',
            '/promo-kodes/create',
            '/promo-kodes/'.$promo->id.'/edit',
            '/transaksis',
            '/transaksis/'.$transaksi->id,
            '/transaksis/'.$transaksi->id.'/receipt',
        ];

        foreach ($adminPages as $uri) {
            $this->actingAs($admin, 'karyawan')->get($uri)->assertOk();
        }

        $this->actingAs($kasir, 'karyawan')->get('/dashboard')->assertOk();
        $this->actingAs($kasir, 'karyawan')->get('/transaksis/create')->assertOk();
        $this->actingAs($kasir, 'karyawan')->get('/transaksis')->assertOk();
        $this->actingAs($kasir, 'karyawan')->get('/transaksis/'.$transaksi->id)->assertOk();

        $this->actingAs($manager, 'karyawan')->get('/dashboard')->assertOk();
        $this->actingAs($manager, 'karyawan')->get('/transaksis')->assertOk();
        $this->actingAs($manager, 'karyawan')->get('/transaksis/'.$transaksi->id)->assertOk();
    }

    public function test_cancelled_filter_is_accepted(): void
    {
        $this->seed();
        $admin = Karyawan::where('username', 'admin')->firstOrFail();

        $this->actingAs($admin, 'karyawan')
            ->get('/transaksis?status=cancelled')
            ->assertOk();
    }
}
