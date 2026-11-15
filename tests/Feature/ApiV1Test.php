<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\KategoriProduk;
use App\Models\Meja;
use App\Models\Pembayaran;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
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

    private function tokenFor(Karyawan $karyawan): string
    {
        return $karyawan->createToken('phpunit')->plainTextToken;
    }

    private function withBearer(string $token): static
    {
        // Guard Sanctum menyimpan user pada instance aplikasi; reset agar
        // pergantian token antar-request dalam satu test benar-benar diuji.
        $this->app['auth']->forgetGuards();

        return $this->withHeader('Authorization', 'Bearer '.$token);
    }

    private function makeSupplier(): Supplier
    {
        return Supplier::create([
            'nama_supplier' => 'Supplier Test',
            'alamat' => 'Bandung',
            'no_telp' => '0800000000',
        ]);
    }

    private function makeKategori(string $nama = 'Manual Brew'): KategoriProduk
    {
        return KategoriProduk::create(['nama_kategori' => $nama]);
    }

    private function makeProduk(int $harga = 20000, int $stock = 20): Produk
    {
        return Produk::create([
            'supplier_id' => $this->makeSupplier()->id,
            'kategori_produk_id' => $this->makeKategori()->id,
            'nama_produk' => 'Americano Test',
            'harga_jual' => $harga,
            'harga_beli' => 10000,
            'tipe' => 'jual',
            'satuan' => 'cup',
            'stock' => $stock,
            'status_aktif' => true,
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

    public function test_login_returns_token_and_me_endpoint_works(): void
    {
        $this->makeKaryawan('kasir', 'kasir-api');

        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'kasir-api',
            'password' => 'Password123!',
            'device_name' => 'phpunit',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.username', 'kasir-api')
            ->assertJsonStructure(['data' => ['token_type', 'access_token', 'user']]);

        $token = $response->json('data.access_token');

        $this->withBearer($token)
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.role', 'kasir');
    }

    public function test_login_invalid_credentials_returns_422(): void
    {
        $this->makeKaryawan('kasir', 'kasir-api');

        $this->postJson('/api/v1/auth/login', [
            'username' => 'kasir-api',
            'password' => 'salah',
        ])->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors('username');
    }

    public function test_protected_endpoint_requires_token(): void
    {
        $this->getJson('/api/v1/produks')
            ->assertUnauthorized()
            ->assertJsonPath('success', false);
    }

    public function test_logout_revokes_current_token(): void
    {
        $karyawan = $this->makeKaryawan('kasir', 'kasir-logout');
        $token = $this->tokenFor($karyawan);

        $this->withBearer($token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        $this->app['auth']->forgetGuards();

        $this->withBearer($token)
            ->getJson('/api/v1/auth/me')
            ->assertUnauthorized();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_admin_can_manage_master_data_but_kasir_cannot(): void
    {
        $admin = $this->makeKaryawan('admin', 'admin-api');
        $kasir = $this->makeKaryawan('kasir', 'kasir-api');
        $adminToken = $this->tokenFor($admin);

        $kategori = $this->withBearer($adminToken)
            ->postJson('/api/v1/kategori-produks', ['nama_kategori' => 'Seasonal'])
            ->assertCreated()
            ->json('data');

        $supplier = $this->withBearer($adminToken)
            ->postJson('/api/v1/suppliers', [
                'nama_supplier' => 'Supplier Baru',
                'alamat' => 'Bandung',
                'no_telp' => '08123456789',
            ])
            ->assertCreated()
            ->json('data');

        $produk = $this->withBearer($adminToken)
            ->postJson('/api/v1/produks', [
                'supplier_id' => $supplier['id'],
                'kategori_produk_id' => $kategori['id'],
                'nama_produk' => 'Latte API',
                'harga_jual' => 25000,
                'harga_beli' => 12000,
                'tipe' => 'jual',
                'satuan' => 'cup',
                'stock' => 15,
                'status_aktif' => true,
            ])
            ->assertCreated()
            ->assertJsonPath('data.nama_produk', 'Latte API')
            ->json('data');

        $this->withBearer($adminToken)
            ->postJson('/api/v1/mejas', [
                'nomor_meja' => 99,
                'kapasitas' => 2,
                'status_aktif' => true,
            ])
            ->assertCreated();

        $this->withBearer($adminToken)
            ->postJson('/api/v1/promo-kodes', [
                'kode' => 'hemat10',
                'nama_promo' => 'Hemat 10%',
                'nilai_diskon' => 10,
                'is_aktif' => true,
            ])
            ->assertCreated()
            ->assertJsonPath('data.kode', 'HEMAT10');

        $this->withBearer($adminToken)
            ->postJson('/api/v1/karyawans', [
                'nama_karyawan' => 'Barista Baru',
                'role' => 'kasir',
                'username' => 'barista-baru',
                'password' => 'Password123!',
            ])
            ->assertCreated()
            ->assertJsonPath('data.role', 'kasir');

        $this->withBearer($this->tokenFor($kasir))
            ->postJson('/api/v1/kategori-produks', ['nama_kategori' => 'Ditolak'])
            ->assertForbidden()
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('produks', ['id' => $produk['id'], 'nama_produk' => 'Latte API']);
        $this->assertDatabaseHas('promo_kodes', ['kode' => 'HEMAT10']);
    }

    public function test_kasir_can_create_pos_transaction_and_pay_cash(): void
    {
        $kasir = $this->makeKaryawan('kasir', 'kasir-pos');
        $token = $this->tokenFor($kasir);
        $meja = $this->makeMeja();
        $produk = $this->makeProduk(20000, 10);

        $response = $this->withBearer($token)
            ->postJson('/api/v1/transaksis', [
                'meja_id' => $meja->id,
                'tipe_pesanan' => 'dine-in',
                'metode_pembayaran' => 'cash',
                'items' => [
                    ['produk_id' => $produk->id, 'qty' => 2],
                ],
            ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.transaksi.status_pesanan', 'pending')
            ->assertJsonPath('data.transaksi.total_harga', 44000);

        $transaksiId = $response->json('data.transaksi.id');

        $this->assertDatabaseHas('detail_transaksis', [
            'transaksi_id' => $transaksiId,
            'produk_id' => $produk->id,
            'QTY' => 2,
        ]);
        $this->assertSame(8, $produk->fresh()->stock);

        $payment = $this->withBearer($token)
            ->postJson("/api/v1/transaksis/{$transaksiId}/payment", [
                'jumlah_bayar' => 50000,
                'metode_pembayaran' => 'cash',
            ]);

        $payment->assertOk()
            ->assertJsonPath('data.transaksi.status_pesanan', 'completed')
            ->assertJsonPath('data.kembalian', 6000)
            ->assertJsonPath('data.pembayaran.metode_pembayaran', 'cash');

        $this->assertDatabaseHas('pembayarans', [
            'transaksi_id' => $transaksiId,
            'status_pembayaran' => 'paid',
            'kembalian' => 6000,
        ]);

        $this->withBearer($token)
            ->getJson("/api/v1/transaksis/{$transaksiId}/receipt")
            ->assertOk()
            ->assertJsonPath('data.ringkasan.total_harga', 44000);
    }

    public function test_payment_rejects_underpayment_and_double_payment(): void
    {
        $kasir = $this->makeKaryawan('kasir', 'kasir-pay');
        $token = $this->tokenFor($kasir);
        $produk = $this->makeProduk(20000, 10);

        $create = $this->withBearer($token)
            ->postJson('/api/v1/transaksis', [
                'tipe_pesanan' => 'take-away',
                'metode_pembayaran' => 'cash',
                'items' => [['produk_id' => $produk->id, 'qty' => 1]],
            ])
            ->assertCreated();

        $id = $create->json('data.transaksi.id');

        $this->withBearer($token)
            ->postJson("/api/v1/transaksis/{$id}/payment", [
                'jumlah_bayar' => 10000,
                'metode_pembayaran' => 'cash',
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->withBearer($token)
            ->postJson("/api/v1/transaksis/{$id}/payment", [
                'jumlah_bayar' => 50000,
                'metode_pembayaran' => 'cash',
            ])
            ->assertOk();

        $this->withBearer($token)
            ->postJson("/api/v1/transaksis/{$id}/payment", [
                'jumlah_bayar' => 50000,
                'metode_pembayaran' => 'cash',
            ])
            ->assertStatus(422);

        $this->assertSame(1, Pembayaran::where('transaksi_id', $id)->count());
    }

    public function test_kasir_cannot_view_other_kasir_transaction(): void
    {
        $kasirA = $this->makeKaryawan('kasir', 'kasir-a');
        $kasirB = $this->makeKaryawan('kasir', 'kasir-b');

        $transaksi = Transaksi::create([
            'karyawan_id' => $kasirB->id,
            'sumber_pesanan' => 'kasir',
            'tipe_pesanan' => 'take-away',
            'total_harga' => 20000,
            'status_pesanan' => 'pending',
            'waktu_transaksi' => now(),
        ]);

        $this->withBearer($this->tokenFor($kasirA))
            ->getJson("/api/v1/transaksis/{$transaksi->id}")
            ->assertForbidden();
    }

    public function test_active_orders_and_status_update_are_available_to_kasir(): void
    {
        $kasir = $this->makeKaryawan('kasir', 'kasir-order');
        $token = $this->tokenFor($kasir);
        $produk = $this->makeProduk(15000, 5);

        $create = $this->withBearer($token)
            ->postJson('/api/v1/transaksis', [
                'tipe_pesanan' => 'take-away',
                'metode_pembayaran' => 'cash',
                'items' => [['produk_id' => $produk->id, 'qty' => 1]],
            ])
            ->assertCreated();

        $id = $create->json('data.transaksi.id');

        $this->withBearer($token)
            ->getJson('/api/v1/pos/active-orders')
            ->assertOk()
            ->assertJsonPath('data.counts.total', 1)
            ->assertJsonPath('data.orders.0.id', $id);

        $this->withBearer($token)
            ->patchJson("/api/v1/transaksis/{$id}/status", ['status_pesanan' => 'prosesed'])
            ->assertOk()
            ->assertJsonPath('data.status_pesanan', 'prosesed');
    }

    public function test_manager_can_read_report_but_kasir_cannot(): void
    {
        $manager = $this->makeKaryawan('manager', 'manager-api');
        $kasir = $this->makeKaryawan('kasir', 'kasir-report');

        $this->withBearer($this->tokenFor($manager))
            ->getJson('/api/v1/laporan/summary?periode=harian')
            ->assertOk()
            ->assertJsonStructure(['data' => ['periode', 'ringkasan', 'pembayaran', 'produk_terlaris']]);

        $this->withBearer($this->tokenFor($kasir))
            ->getJson('/api/v1/laporan/summary')
            ->assertForbidden();
    }

    public function test_dashboard_summary_is_role_aware(): void
    {
        $admin = $this->makeKaryawan('admin', 'admin-dash');
        $kasir = $this->makeKaryawan('kasir', 'kasir-dash');
        $manager = $this->makeKaryawan('manager', 'manager-dash');

        $this->withBearer($this->tokenFor($admin))
            ->getJson('/api/v1/dashboard/summary')
            ->assertOk()
            ->assertJsonPath('data.role', 'admin')
            ->assertJsonStructure(['data' => ['data' => ['meja', 'transaksi_terbaru', 'grafik_penjualan']]]);

        $this->withBearer($this->tokenFor($manager))
            ->getJson('/api/v1/dashboard/summary')
            ->assertOk()
            ->assertJsonPath('data.role', 'manager')
            ->assertJsonStructure(['data' => ['data' => ['bulan_ini', 'produk_terlaris']]]);

        $this->withBearer($this->tokenFor($kasir))
            ->getJson('/api/v1/dashboard/summary')
            ->assertOk()
            ->assertJsonPath('data.role', 'kasir')
            ->assertJsonStructure(['data' => ['data' => ['antrean', 'meja', 'transaksi_terbaru']]]);
    }
}
