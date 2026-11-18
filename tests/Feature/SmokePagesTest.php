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

        $supplier = \App\Models\Supplier::firstOrFail();
        $kategori = \App\Models\KategoriProduk::firstOrFail();

        $produk = Produk::create([
            'supplier_id' => $supplier->id,
            'kategori_produk_id' => $kategori->id,
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
            '/kategori-produks/'.$kategori->id.'/edit',
            '/mejas',
            '/mejas/create',
            '/mejas/'.$meja->id.'/edit',
            '/suppliers',
            '/suppliers/create',
            '/suppliers/'.$supplier->id.'/edit',
            '/karyawans',
            '/karyawans/create',
            '/karyawans/'.$kasir->id.'/edit',
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

    public function test_web_route_names_do_not_collide_with_api_routes(): void
    {
        foreach (['suppliers', 'karyawans', 'promo-kodes'] as $resource) {
            $url = route($resource.'.index');

            $this->assertStringNotContainsString('/api/v1/', $url);
            $this->assertSame(url('/'.$resource), $url);
        }
    }

    public function test_records_with_history_cannot_be_deleted(): void
    {
        $this->seed();

        $admin = Karyawan::where('username', 'admin')->firstOrFail();
        $kasir = Karyawan::create([
            'nama_karyawan' => 'Kasir Riwayat',
            'jabatan' => 'Kasir',
            'role' => 'kasir',
            'username' => 'kasir_riwayat',
            'password' => 'password123',
        ]);
        $supplier = \App\Models\Supplier::firstOrFail();
        $kategori = \App\Models\KategoriProduk::firstOrFail();
        $produk = Produk::create([
            'supplier_id' => $supplier->id,
            'kategori_produk_id' => $kategori->id,
            'nama_produk' => 'Produk Riwayat',
            'harga_jual' => 20000,
            'harga_beli' => 10000,
            'tipe' => 'jual',
            'satuan' => 'pcs',
            'stock' => 5,
            'status_aktif' => true,
        ]);
        $meja = Meja::create(['nomor_meja' => 9, 'kapasitas' => 4, 'status_aktif' => true]);
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

        $this->actingAs($admin, 'karyawan')->delete(route('produks.destroy', $produk))->assertSessionHas('error');
        $this->actingAs($admin, 'karyawan')->delete(route('mejas.destroy', $meja))->assertSessionHas('error');
        $this->actingAs($admin, 'karyawan')->delete(route('kategori-produks.destroy', $kategori))->assertSessionHas('error');
        $this->actingAs($admin, 'karyawan')->delete(route('suppliers.destroy', $supplier))->assertSessionHas('error');
        $this->actingAs($admin, 'karyawan')->delete(route('karyawans.destroy', $kasir))->assertSessionHas('error');

        $this->assertDatabaseHas('produks', ['id' => $produk->id]);
        $this->assertDatabaseHas('mejas', ['id' => $meja->id]);
        $this->assertDatabaseHas('kategori_produks', ['id' => $kategori->id]);
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
        $this->assertDatabaseHas('karyawans', ['id' => $kasir->id]);
    }

    public function test_pos_cash_option_and_order_note_are_saved(): void
    {
        $this->seed();

        $admin = Karyawan::where('username', 'admin')->firstOrFail();
        $supplier = \App\Models\Supplier::firstOrFail();
        $kategori = \App\Models\KategoriProduk::firstOrFail();
        $produk = Produk::create([
            'supplier_id' => $supplier->id,
            'kategori_produk_id' => $kategori->id,
            'nama_produk' => 'Kopi Cash',
            'harga_jual' => 20000,
            'harga_beli' => 10000,
            'tipe' => 'jual',
            'satuan' => 'pcs',
            'stock' => 10,
            'status_aktif' => true,
        ]);
        $meja = Meja::create(['nomor_meja' => 7, 'kapasitas' => 4, 'status_aktif' => true]);

        $this->actingAs($admin, 'karyawan')
            ->get(route('transaksis.create'))
            ->assertOk()
            ->assertSee('value="cash"', false)
            ->assertDontSee('value="tunai"', false);

        $response = $this->actingAs($admin, 'karyawan')
            ->post(route('transaksis.store'), [
                'meja_id' => $meja->id,
                'tipe_pesanan' => 'dine-in',
                'metode_pembayaran' => 'cash',
                'catatan' => 'Tanpa gula, es sedikit',
                'items' => [
                    ['produk_id' => $produk->id, 'qty' => 1, 'customization' => null],
                ],
            ]);

        $transaksi = Transaksi::latest('id')->firstOrFail();
        $response->assertRedirect(route('transaksis.show', $transaksi->id));

        $this->assertDatabaseHas('transaksis', [
            'meja_id' => $meja->id,
            'sumber_pesanan' => 'kasir',
            'catatan' => 'Tanpa gula, es sedikit',
        ]);
    }

    public function test_dashboards_render_with_empty_database(): void
    {
        $this->seed();

        $admin = Karyawan::where('username', 'admin')->firstOrFail();
        $kasir = Karyawan::create([
            'nama_karyawan' => 'Kasir Kosong',
            'jabatan' => 'Kasir',
            'role' => 'kasir',
            'username' => 'kasir_kosong',
            'password' => 'password123',
        ]);
        $manager = Karyawan::create([
            'nama_karyawan' => 'Manager Kosong',
            'jabatan' => 'Manager',
            'role' => 'manager',
            'username' => 'manager_kosong',
            'password' => 'password123',
        ]);

        $this->actingAs($admin, 'karyawan')->get('/dashboard')->assertOk();
        $this->actingAs($kasir, 'karyawan')->get('/dashboard')->assertOk();
        $this->actingAs($manager, 'karyawan')->get('/dashboard')->assertOk();
    }

    public function test_login_remember_me_stores_recaller_token(): void
    {
        $kasir = Karyawan::create([
            'nama_karyawan' => 'Kasir Ingat',
            'jabatan' => 'Kasir',
            'role' => 'kasir',
            'username' => 'kasir_ingat',
            'password' => 'password123',
        ]);

        $this->post('/login', [
            'username' => 'kasir_ingat',
            'password' => 'password123',
            'remember' => '1',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($kasir, 'karyawan');
        $this->assertNotNull($kasir->fresh()->remember_token);
    }
}
