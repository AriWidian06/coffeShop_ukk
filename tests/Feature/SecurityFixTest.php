<?php

namespace Tests\Feature;

use App\Models\DetailTransaksi;
use App\Models\Karyawan;
use App\Models\KategoriProduk;
use App\Models\Meja;
use App\Models\Pembayaran;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\Transaksi;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression test untuk perbaikan keamanan & integritas data.
 * Setiap test di sini gagal pada versi sebelum perbaikan.
 */
class SecurityFixTest extends TestCase
{
    use RefreshDatabase;

    private function karyawan(string $role, string $username): Karyawan
    {
        return Karyawan::create([
            'nama_karyawan' => $username,
            'jabatan' => $role,
            'role' => $role,
            'username' => $username,
            'password' => 'Password123!',
        ]);
    }

    private function meja(int $nomor = 1): Meja
    {
        return Meja::create(['nomor_meja' => $nomor, 'kapasitas' => 4, 'status_aktif' => true]);
    }

    private function produk(int $harga = 20000, int $stock = 50): Produk
    {
        $supplier = Supplier::create(['nama_supplier' => 'S', 'alamat' => 'A', 'no_telp' => '1']);
        $kategori = KategoriProduk::create(['nama_kategori' => 'K']);

        return Produk::create([
            'supplier_id' => $supplier->id,
            'kategori_produk_id' => $kategori->id,
            'nama_produk' => 'Kopi '.uniqid(),
            'harga_jual' => $harga,
            'harga_beli' => 10000,
            'tipe' => 'jual',
            'satuan' => 'cup',
            'stock' => $stock,
            'status_aktif' => true,
        ]);
    }

    /** Hapus transaksi harus mengembalikan stok produk. */
    public function test_destroy_transaksi_mengembalikan_stok(): void
    {
        $kasir = $this->karyawan('kasir', 'kasir1');
        $meja = $this->meja();
        $produk = $this->produk(20000, 50);

        $trx = Transaksi::create([
            'meja_id' => $meja->id,
            'karyawan_id' => $kasir->id,
            'tipe_pesanan' => 'dine-in',
            'total_harga' => 20000,
            'status_pesanan' => 'pending',
            'waktu_transaksi' => now(),
        ]);

        DetailTransaksi::create([
            'transaksi_id' => $trx->id,
            'produk_id' => $produk->id,
            'QTY' => 5,
            'subtotal' => 100000,
        ]);

        $produk->decrement('stock', 5);
        $this->assertSame(45, (int) $produk->fresh()->stock);

        $this->actingAs($kasir, 'karyawan')
            ->delete('/transaksis/'.$trx->id)
            ->assertRedirect();

        $this->assertSame(50, (int) $produk->fresh()->stock, 'Stok harus dikembalikan setelah transaksi dihapus.');
    }

    /** Meja yang sudah ada pesanan aktif tidak boleh dibooking lagi lewat POS kasir. */
    public function test_pos_kasir_tidak_bisa_double_booking_meja(): void
    {
        $kasir = $this->karyawan('kasir', 'kasir1');
        $meja = $this->meja();
        $produk = $this->produk();

        Transaksi::create([
            'meja_id' => $meja->id,
            'karyawan_id' => $kasir->id,
            'tipe_pesanan' => 'dine-in',
            'total_harga' => 20000,
            'status_pesanan' => 'pending',
            'waktu_transaksi' => now(),
        ]);

        $this->actingAs($kasir, 'karyawan')->post('/transaksis', [
            'meja_id' => $meja->id,
            'tipe_pesanan' => 'dine-in',
            'metode_pembayaran' => 'cash',
            'items' => [['produk_id' => $produk->id, 'qty' => 1]],
        ])->assertSessionHasErrors('meja_id');

        $this->assertSame(1, Transaksi::where('meja_id', $meja->id)->count());
    }

    /** Notifikasi Midtrans dengan signature palsu harus ditolak. */
    public function test_notifikasi_midtrans_palsu_ditolak(): void
    {
        config(['services.midtrans.server_key' => 'super-secret-key']);

        $kasir = $this->karyawan('kasir', 'kasir1');
        $meja = $this->meja();
        $trx = Transaksi::create([
            'meja_id' => $meja->id,
            'karyawan_id' => $kasir->id,
            'tipe_pesanan' => 'dine-in',
            'total_harga' => 50000,
            'status_pesanan' => 'pending',
            'waktu_transaksi' => now(),
        ]);

        $this->postJson('/midtrans/notification', [
            'order_id' => 'TRX-'.$trx->id.'-123',
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'transaction_status' => 'capture',
            'fraud_status' => 'accept',
            'signature_key' => 'signature-palsu',
        ])->assertStatus(400);

        $this->assertSame('pending', $trx->fresh()->status_pesanan);
    }

    /** Tanpa server key, notifikasi apa pun harus ditolak (tidak bisa di-forge). */
    public function test_notifikasi_midtrans_ditolak_saat_server_key_kosong(): void
    {
        config(['services.midtrans.server_key' => null]);

        $kasir = $this->karyawan('kasir', 'kasir1');
        $meja = $this->meja();
        $trx = Transaksi::create([
            'meja_id' => $meja->id,
            'karyawan_id' => $kasir->id,
            'tipe_pesanan' => 'dine-in',
            'total_harga' => 50000,
            'status_pesanan' => 'pending',
            'waktu_transaksi' => now(),
        ]);

        $orderId = 'TRX-'.$trx->id.'-123';
        $this->postJson('/midtrans/notification', [
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'transaction_status' => 'capture',
            'fraud_status' => 'accept',
            'signature_key' => hash('sha512', $orderId.'200'.'50000.00'.''),
        ])->assertStatus(400);

        $this->assertSame('pending', $trx->fresh()->status_pesanan);
    }

    /** Transaksi yang sudah dibayar tidak boleh dibayar lagi (dulu HTTP 500). */
    public function test_pembayaran_ganda_ditolak_dengan_pesan_ramah(): void
    {
        $kasir = $this->karyawan('kasir', 'kasir1');
        $meja = $this->meja();
        $trx = Transaksi::create([
            'meja_id' => $meja->id,
            'karyawan_id' => $kasir->id,
            'tipe_pesanan' => 'dine-in',
            'total_harga' => 50000,
            'status_pesanan' => 'pending',
            'waktu_transaksi' => now(),
        ]);

        $this->actingAs($kasir, 'karyawan')->post('/pembayarans', [
            'transaksi_id' => $trx->id,
            'jumlah_bayar' => 50000,
            'metode_pembayaran' => 'cash',
        ])->assertSessionHasNoErrors();

        $this->actingAs($kasir, 'karyawan')->post('/pembayarans', [
            'transaksi_id' => $trx->id,
            'jumlah_bayar' => 50000,
            'metode_pembayaran' => 'cash',
        ])->assertSessionHasErrors('transaksi_id');

        $this->assertSame(1, Pembayaran::where('transaksi_id', $trx->id)->count());
    }

    /** Route API harus dibatasi rate-nya. */
    public function test_route_api_memiliki_rate_limit(): void
    {
        $routes = collect(app('router')->getRoutes())->mapWithKeys(fn ($r) => [$r->uri() => $r->gatherMiddleware()]);

        foreach (['api/transaksis', 'api/pesanan-saya', 'api/promo/validate'] as $uri) {
            $this->assertTrue(
                collect($routes[$uri] ?? [])->contains(fn ($m) => str_contains((string) $m, 'throttle')),
                "Route {$uri} harus memakai throttle."
            );
        }
    }

    /** Login harus dibatasi agar tidak bisa brute force. */
    public function test_login_memiliki_rate_limit(): void
    {
        // Ada dua route dengan uri 'login' (GET form & POST submit); yang di-throttle adalah POST.
        $route = collect(app('router')->getRoutes())
            ->first(fn ($r) => $r->uri() === 'login' && in_array('POST', $r->methods(), true));

        $this->assertNotNull($route, 'Route POST /login harus ada.');
        $this->assertTrue(
            collect($route->gatherMiddleware())->contains(fn ($m) => str_contains((string) $m, 'throttle')),
            'Route login harus memakai throttle.'
        );
    }

    /** Seeder tidak boleh menimpa password admin yang sudah ada. */
    public function test_seeder_tidak_menimpa_password_admin_yang_ada(): void
    {
        $admin = Karyawan::create([
            'nama_karyawan' => 'Admin',
            'jabatan' => 'Admin',
            'role' => 'admin',
            'username' => 'admin',
            'password' => 'PasswordLamaYangKuat123',
        ]);

        $hashLama = $admin->password;

        $this->seed(DatabaseSeeder::class);

        $this->assertSame($hashLama, Karyawan::where('username', 'admin')->first()->password);
    }
}
