<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\KategoriProduk;
use App\Models\Meja;
use App\Models\Produk;
use App\Models\ProdukOpsi;
use App\Models\Supplier;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FunctionalReportTest extends TestCase
{
    use RefreshDatabase;

    private function makeKaryawan(string $role, string $username, string $name = 'Test User'): Karyawan
    {
        return Karyawan::create([
            'nama_karyawan' => $name,
            'jabatan' => ucfirst($role),
            'role' => $role,
            'username' => $username,
            'password' => 'Password123!',
        ]);
    }

    private function makeMeja(int $nomor = 1): Meja
    {
        return Meja::create([
            'nomor_meja' => $nomor,
            'kapasitas' => 4,
            'status_aktif' => true,
        ]);
    }

    private function makeProduk(int $harga = 20000, int $stock = 10, bool $customizable = false): Produk
    {
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Test',
            'alamat' => 'Bandung',
            'no_telp' => '0800000000',
        ]);

        $kategori = KategoriProduk::create(['nama_kategori' => 'Manual Brew']);

        return Produk::create([
            'supplier_id' => $supplier->id,
            'kategori_produk_id' => $kategori->id,
            'nama_produk' => 'Americano Test',
            'harga_jual' => $harga,
            'harga_beli' => 10000,
            'tipe' => 'jual',
            'satuan' => 'cup',
            'stock' => $stock,
            'status_aktif' => true,
            'is_customizable' => $customizable,
        ]);
    }

    private function makeTransaksi(Karyawan $owner, int $total = 50000, bool $withMeja = true): Transaksi
    {
        return Transaksi::create([
            'meja_id' => $withMeja ? $this->makeMeja(random_int(1, 1000))->id : null,
            'karyawan_id' => $owner->id,
            'tipe_pesanan' => $withMeja ? 'dine-in' : 'take-away',
            'total_harga' => $total,
            'status_pesanan' => 'pending',
            'waktu_transaksi' => now(),
        ]);
    }

    public function test_tc_auth_01_login_valid(): void
    {
        $user = $this->makeKaryawan('kasir', 'farhan', 'Farhan');

        $response = $this->post('/login', [
            'username' => 'farhan',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user, 'karyawan');
    }

    public function test_tc_auth_02_login_invalid(): void
    {
        $this->makeKaryawan('kasir', 'farhan', 'Farhan');

        $response = $this->from('/login')->post('/login', [
            'username' => 'farhan',
            'password' => 'salah123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
        $this->assertGuest('karyawan');
    }

    public function test_login_form_uses_https_behind_reverse_proxy(): void
    {
        $response = $this->withServerVariables([
            'HTTPS' => 'off',
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ])->get('/login');

        $response->assertOk();
        $this->assertMatchesRegularExpression(
            '/<form[^>]+action="https:\/\/[^"]+\/login"/',
            $response->getContent()
        );
    }

    public function test_tc_auth_03_logout_invalidates_session(): void
    {
        $user = $this->makeKaryawan('kasir', 'farhan', 'Farhan');

        $this->actingAs($user, 'karyawan')
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest('karyawan');
    }

    public function test_tc_auth_04_kasir_cannot_access_admin_panel(): void
    {
        $kasir = $this->makeKaryawan('kasir', 'kasir1');

        $this->actingAs($kasir, 'karyawan')
            ->get('/karyawans')
            ->assertForbidden();
    }

    public function test_tc_idor_01_kasir_cannot_view_other_kasir_transaction(): void
    {
        $kasirA = $this->makeKaryawan('kasir', 'kasirA');
        $kasirB = $this->makeKaryawan('kasir', 'kasirB');
        $transaksi = $this->makeTransaksi($kasirB);

        $this->actingAs($kasirA, 'karyawan')
            ->get('/transaksis/' . $transaksi->id)
            ->assertForbidden();
    }

    public function test_tc_prod_01_admin_can_create_category_and_product(): void
    {
        $admin = $this->makeKaryawan('admin', 'admin1');
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Utama',
            'alamat' => 'Bandung',
            'no_telp' => '08123456789',
        ]);
        $kategori = KategoriProduk::create(['nama_kategori' => 'Manual Brew']);

        $this->actingAs($admin, 'karyawan')
            ->post('/kategori-produks', ['nama_kategori' => 'Seasonal Drinks'])
            ->assertRedirect(route('kategori-produks.index'));

        $this->assertDatabaseHas('kategori_produks', ['nama_kategori' => 'Seasonal Drinks']);

        $this->actingAs($admin, 'karyawan')
            ->post('/produks', [
                'supplier_id' => $supplier->id,
                'kategori_produk_id' => $kategori->id,
                'nama_produk' => 'Americano',
                'deskripsi' => 'Kopi hitam test',
                'harga_jual' => 15000,
                'harga_beli' => 8000,
                'tipe' => 'jual',
                'satuan' => 'cup',
                'stock' => 10,
                'status_aktif' => 1,
            ])
            ->assertRedirect(route('produks.index'));

        $this->assertDatabaseHas('produks', [
            'nama_produk' => 'Americano',
            'harga_jual' => 15000,
            'stock' => 10,
            'status_aktif' => 1,
        ]);
    }

    public function test_produk_index_shows_qr_menu_status_as_text(): void
    {
        $admin = $this->makeKaryawan('admin', 'admin-status');
        $aktif = $this->makeProduk();
        Produk::create([
            'supplier_id' => $aktif->supplier_id,
            'kategori_produk_id' => $aktif->kategori_produk_id,
            'nama_produk' => 'Americano Nonaktif',
            'deskripsi' => 'Produk nonaktif test',
            'harga_jual' => 15000,
            'harga_beli' => 8000,
            'tipe' => 'jual',
            'satuan' => 'cup',
            'stock' => 10,
            'status_aktif' => false,
        ]);

        $this->actingAs($admin, 'karyawan')
            ->get('/produks')
            ->assertOk()
            ->assertSee('Aktif')
            ->assertSee('Non-aktif')
            ->assertDontSee('toggle-switch');
    }

    public function test_admin_sales_chart_returns_hourly_revenue_and_volume_without_cancelled_orders(): void
    {
        $admin = $this->makeKaryawan('admin', 'admin-chart');
        $meja = $this->makeMeja();
        $hour = (int) now('Asia/Jakarta')->hour;
        $timestamp = now('Asia/Jakarta')->startOfHour()->addMinutes(10)->utc();

        Transaksi::create([
            'meja_id' => $meja->id,
            'karyawan_id' => $admin->id,
            'tipe_pesanan' => 'dine-in',
            'total_harga' => 12000,
            'status_pesanan' => 'completed',
            'waktu_transaksi' => $timestamp,
        ]);
        Transaksi::create([
            'meja_id' => $meja->id,
            'karyawan_id' => $admin->id,
            'tipe_pesanan' => 'dine-in',
            'total_harga' => 9000,
            'status_pesanan' => 'cancelled',
            'waktu_transaksi' => $timestamp,
        ]);

        $response = $this->actingAs($admin, 'karyawan')
            ->getJson(route('dashboard.sales-chart'))
            ->assertOk()
            ->assertJsonCount(24, 'sales')
            ->assertJsonCount(24, 'volume')
            ->assertJsonPath('peak_hour', $hour)
            ->assertJsonPath('peak_hour_label', sprintf('%02d:00 - %02d:00 WIB', $hour, ($hour + 1) % 24));

        $this->assertEquals(12000.0, $response->json("sales.{$hour}"));
        $this->assertSame(1, $response->json("volume.{$hour}"));

        $this->actingAs($admin, 'karyawan')
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Penjualan Real-time per Jam')
            ->assertSee('data-chart-series="volume"', false)
            ->assertSee('Belum ada transaksi hari ini. Grafik diperbarui otomatis.');
    }

    public function test_manager_sales_chart_tracks_selected_period_and_denies_kasir(): void
    {
        $manager = $this->makeKaryawan('manager', 'manager-chart');
        $kasir = $this->makeKaryawan('kasir', 'kasir-chart');
        $meja = $this->makeMeja();
        $date = now('Asia/Jakarta')->subDays(10)->toDateString();
        $hour = 15;
        $timestamp = \Carbon\Carbon::parse($date, 'Asia/Jakarta')->setHour($hour)->addMinutes(10)->utc();

        Transaksi::create([
            'meja_id' => $meja->id,
            'karyawan_id' => $manager->id,
            'tipe_pesanan' => 'dine-in',
            'total_harga' => 18000,
            'status_pesanan' => 'completed',
            'waktu_transaksi' => $timestamp,
        ]);
        Transaksi::create([
            'meja_id' => $meja->id,
            'karyawan_id' => $manager->id,
            'tipe_pesanan' => 'dine-in',
            'total_harga' => 7000,
            'status_pesanan' => 'cancelled',
            'waktu_transaksi' => $timestamp,
        ]);

        $response = $this->actingAs($manager, 'karyawan')
            ->getJson(route('dashboard.manager-sales-chart', ['periode' => 'harian', 'tanggal' => $date]))
            ->assertOk()
            ->assertJsonCount(24, 'sales')
            ->assertJsonCount(24, 'volume')
            ->assertJsonPath('peak_hour', $hour)
            ->assertJsonPath('peak_hour_label', '15:00 - 16:00 WIB');

        $this->assertEquals(18000.0, $response->json("sales.{$hour}"));
        $this->assertSame(1, $response->json("volume.{$hour}"));

        $this->actingAs($manager, 'karyawan')
            ->get('/dashboard?periode=harian&tanggal='.$date)
            ->assertOk()
            ->assertSee('data-manager-chart-series="volume"', false);

        $this->actingAs($kasir, 'karyawan')
            ->getJson(route('dashboard.manager-sales-chart', ['periode' => 'harian', 'tanggal' => $date]))
            ->assertForbidden();
    }

    public function test_tc_meja_01_admin_can_create_and_deactivate_table(): void
    {
        $admin = $this->makeKaryawan('admin', 'admin2');

        $this->actingAs($admin, 'karyawan')
            ->post('/mejas', [
                'nomor_meja' => 99,
                'kapasitas' => 4,
                'status_aktif' => 1,
            ])
            ->assertRedirect(route('mejas.index'));

        $meja = Meja::where('nomor_meja', 99)->firstOrFail();
        $this->assertTrue((bool) $meja->status_aktif);

        $this->actingAs($admin, 'karyawan')
            ->put('/mejas/' . $meja->id, [
                'nomor_meja' => 99,
                'kapasitas' => 4,
                'status_aktif' => 0,
            ])
            ->assertRedirect(route('mejas.index'));

        $this->assertFalse((bool) $meja->fresh()->status_aktif);
    }

    public function test_tc_pos_01_dine_in_with_customization(): void
    {
        $kasir = $this->makeKaryawan('kasir', 'kasirPos1');
        $meja = $this->makeMeja(1);
        $produk = $this->makeProduk(20000, 10, true);
        ProdukOpsi::create([
            'produk_id' => $produk->id,
            'kategori' => 'Susu',
            'tipe' => 'single',
            'is_required' => false,
            'nama_opsi' => 'Oat Milk',
            'harga_tambahan' => 5000,
        ]);

        $this->actingAs($kasir, 'karyawan')
            ->post('/transaksis', [
                'meja_id' => $meja->id,
                'tipe_pesanan' => 'dine-in',
                'metode_pembayaran' => 'cash',
                'items' => [[
                    'produk_id' => $produk->id,
                    'qty' => 2,
                    'customization' => 'Oat Milk (+5000)',
                ]],
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('transaksis', [
            'meja_id' => $meja->id,
            'karyawan_id' => $kasir->id,
            'tipe_pesanan' => 'dine-in',
            'total_harga' => 55000,
            'status_pesanan' => 'pending',
        ]);

        $this->assertDatabaseHas('detail_transaksis', [
            'produk_id' => $produk->id,
            'QTY' => 2,
            'subtotal' => 50000,
            'customization' => 'Oat Milk (+5000)',
        ]);

        $this->assertSame(8, $produk->fresh()->stock);
    }

    public function test_tc_pos_02_take_away(): void
    {
        $kasir = $this->makeKaryawan('kasir', 'kasirPos2');
        $produk = $this->makeProduk(15000, 5);

        $this->actingAs($kasir, 'karyawan')
            ->post('/transaksis', [
                'meja_id' => null,
                'tipe_pesanan' => 'take-away',
                'metode_pembayaran' => 'cash',
                'items' => [[
                    'produk_id' => $produk->id,
                    'qty' => 1,
                ]],
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('transaksis', [
            'karyawan_id' => $kasir->id,
            'meja_id' => null,
            'tipe_pesanan' => 'take-away',
            'total_harga' => 16500,
            'status_pesanan' => 'pending',
        ]);
    }

    public function test_tc_pay_01_cash_payment_with_change(): void
    {
        $kasir = $this->makeKaryawan('kasir', 'kasirPay1');
        $transaksi = $this->makeTransaksi($kasir, 50000);

        $this->actingAs($kasir, 'karyawan')
            ->post('/pembayarans', [
                'transaksi_id' => $transaksi->id,
                'jumlah_bayar' => 100000,
                'metode_pembayaran' => 'cash',
            ])
            ->assertRedirect(route('transaksis.show', $transaksi->id));

        $this->assertDatabaseHas('pembayarans', [
            'transaksi_id' => $transaksi->id,
            'jumlah_bayar' => 100000,
            'metode_pembayaran' => 'cash',
            'status_pembayaran' => 'paid',
            'kembalian' => 50000,
        ]);

        $this->assertSame('completed', $transaksi->fresh()->status_pesanan);
    }

    public function test_tc_pay_02_qris_can_generate_snap_token(): void
    {
        $kasir = $this->makeKaryawan('kasir', 'kasirQris');
        $transaksi = $this->makeTransaksi($kasir, 25000);

        $snap = \Mockery::mock('alias:Midtrans\Snap');
        $snap->shouldReceive('getSnapToken')->once()->andReturn('fake-snap-token');

        $this->actingAs($kasir, 'karyawan')
            ->get('/kasir/transaksi/' . $transaksi->id . '/qris')
            ->assertOk()
            ->assertSee('fake-snap-token');
    }

    public function test_tc_pay_03_payment_less_than_total_is_rejected(): void
    {
        $kasir = $this->makeKaryawan('kasir', 'kasirPay2');
        $transaksi = $this->makeTransaksi($kasir, 50000);

        $response = $this->actingAs($kasir, 'karyawan')
            ->from('/transaksis/' . $transaksi->id)
            ->post('/pembayarans', [
                'transaksi_id' => $transaksi->id,
                'jumlah_bayar' => 30000,
                'metode_pembayaran' => 'cash',
            ]);

        $response->assertRedirect('/transaksis/' . $transaksi->id);
        $response->assertSessionHasErrors('jumlah_bayar');

        $this->assertDatabaseMissing('pembayarans', ['transaksi_id' => $transaksi->id]);
        $this->assertSame('pending', $transaksi->fresh()->status_pesanan);
    }

    public function test_tc_ord_01_status_can_be_updated_to_completed(): void
    {
        $kasir = $this->makeKaryawan('kasir', 'kasirOrd');
        $transaksi = $this->makeTransaksi($kasir, 30000);

        $this->actingAs($kasir, 'karyawan')
            ->patch('/transaksis/' . $transaksi->id . '/status', [
                'status_pesanan' => 'completed',
            ])
            ->assertRedirect();

        $this->assertSame('completed', $transaksi->fresh()->status_pesanan);
    }

    public function test_tc_rep_01_manager_can_export_pdf_report(): void
    {
        $manager = $this->makeKaryawan('manager', 'managerRep', 'Manager Test');
        $kasir = $this->makeKaryawan('kasir', 'kasirRep');
        $transaksi = $this->makeTransaksi($kasir, 40000);
        $transaksi->update(['status_pesanan' => 'completed']);

        $response = $this->actingAs($manager, 'karyawan')
            ->get('/laporan/export');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_tc_err_01_custom_404_and_500_pages(): void
    {
        $this->get('/halaman-tidak-ada')
            ->assertStatus(404)
            ->assertSee('Halaman Tidak Ditemukan');

        Route::middleware('web')->get('/__test-500', function () {
            throw new \RuntimeException('simulated server error');
        });

        config(['app.debug' => false]);

        $this->get('/__test-500')
            ->assertStatus(500)
            ->assertSee('Mohon Maaf, Terjadi Kendala');
    }
}
