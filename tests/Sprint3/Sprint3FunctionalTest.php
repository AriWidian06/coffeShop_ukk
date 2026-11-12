<?php

namespace Tests\Sprint3;

use App\Models\DetailTransaksi;
use App\Models\Karyawan;
use App\Models\KategoriProduk;
use App\Models\Meja;
use App\Models\Pembayaran;
use App\Models\Produk;
use App\Models\PromoKode;
use App\Models\PromoUsage;
use App\Models\Supplier;
use App\Models\Transaksi;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Suite pengujian Sprint 3.
 *
 * Jalankan hanya dengan konfigurasi terpisah:
 *   vendor/bin/phpunit -c phpunit.sprint3.xml
 *
 * Suite ini memakai database uji db_perkoci_test dan sengaja tidak dimasukkan
 * ke phpunit.xml default agar tidak menyentuh database utama db_perkoci.
 */
class Sprint3FunctionalTest extends TestCase
{
    use RefreshDatabase;

    protected function beforeRefreshingDatabase()
    {
        if (DB::connection()->getDatabaseName() !== 'db_perkoci_test') {
            $this->markTestSkipped(
                'Sprint 3 test hanya boleh dijalankan pada database db_perkoci_test. ' .
                'Gunakan: vendor/bin/phpunit -c phpunit.sprint3.xml'
            );
        }
    }

    private Karyawan $admin;
    private Karyawan $kasir;
    private Karyawan $manager;
    private Meja $meja1;
    private Meja $meja2;
    private Meja $meja3;
    private KategoriProduk $kategori;
    private Supplier $supplier;
    private Produk $produk;
    private Produk $produkHabis;
    private PromoKode $promo;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 11, 8, 10, 0, 0, 'UTC'));

        $this->admin = Karyawan::create([
            'nama_karyawan' => 'Admin Uji',
            'jabatan' => 'Administrator',
            'role' => 'admin',
            'username' => 'admin_uji',
            'password' => 'password123',
        ]);

        $this->kasir = Karyawan::create([
            'nama_karyawan' => 'Kasir Uji',
            'jabatan' => 'Kasir',
            'role' => 'kasir',
            'username' => 'kasir_uji',
            'password' => 'password123',
        ]);

        $this->manager = Karyawan::create([
            'nama_karyawan' => 'Manager Uji',
            'jabatan' => 'Store Manager',
            'role' => 'manager',
            'username' => 'manager_uji',
            'password' => 'password123',
        ]);

        $this->meja1 = Meja::create(['nomor_meja' => 1, 'kapasitas' => 2, 'status_aktif' => true]);
        $this->meja2 = Meja::create(['nomor_meja' => 2, 'kapasitas' => 4, 'status_aktif' => true]);
        $this->meja3 = Meja::create(['nomor_meja' => 3, 'kapasitas' => 4, 'status_aktif' => true]);

        $this->kategori = KategoriProduk::create(['nama_kategori' => 'Kopi Uji']);
        $this->supplier = Supplier::create([
            'nama_supplier' => 'Supplier Uji',
            'alamat' => 'Bandung',
            'no_telp' => '08123456789',
        ]);

        $this->produk = Produk::create([
            'supplier_id' => $this->supplier->id,
            'kategori_produk_id' => $this->kategori->id,
            'nama_produk' => 'Kopi Susu Uji',
            'deskripsi' => 'Produk untuk pengujian',
            'harga_jual' => 20000,
            'harga_beli' => 10000,
            'tipe' => 'jual',
            'satuan' => 'pcs',
            'stock' => 20,
            'status_aktif' => true,
            'is_customizable' => true,
        ]);

        $this->produk->opsis()->create([
            'kategori' => 'Level Gula',
            'tipe' => 'single',
            'is_required' => false,
            'nama_opsi' => 'Gula Aren',
            'harga_tambahan' => 5000,
        ]);

        $this->produkHabis = Produk::create([
            'supplier_id' => $this->supplier->id,
            'kategori_produk_id' => $this->kategori->id,
            'nama_produk' => 'Menu Habis Uji',
            'harga_jual' => 15000,
            'harga_beli' => 7000,
            'tipe' => 'jual',
            'satuan' => 'pcs',
            'stock' => 0,
            'status_aktif' => true,
            'is_customizable' => false,
        ]);

        $this->promo = PromoKode::create([
            'kode' => 'HEMAT10',
            'nama_promo' => 'Diskon Uji 10%',
            'nilai_diskon' => 10,
            'min_belanja' => 0,
            'maks_diskon' => 50000,
            'tanggal_mulai' => Carbon::now()->subDay(),
            'tanggal_berakhir' => Carbon::now()->addDay(),
            'kuota' => 100,
            'kuota_terpakai' => 0,
            'is_aktif' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function apiOrder(array $overrides = []): array
    {
        return array_merge([
            'meja_id' => $this->meja1->id,
            'tipe_pesanan' => 'dine-in',
            'metode_pembayaran' => 'tunai',
            'session_id' => 'SESI-' . uniqid(),
            'items' => [
                ['produk_id' => $this->produk->id, 'qty' => 1, 'customization' => null],
            ],
        ], $overrides);
    }

    private function midtransPayload(Transaksi $transaksi, string $status = 'settlement'): array
    {
        $orderId = 'TRX-' . $transaksi->id . '-' . time();
        $gross = number_format((float) $transaksi->total_harga, 2, '.', '');
        $signature = hash('sha512', $orderId . '200' . $gross . config('services.midtrans.server_key'));

        return [
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => $gross,
            'transaction_status' => $status,
            'fraud_status' => 'accept',
            'signature_key' => $signature,
        ];
    }

    public function test_ft01_login_per_role(): void
    {
        $roles = [
            ['admin_uji', $this->admin, 'Dashboard'],
            ['kasir_uji', $this->kasir, 'Terminal POS'],
            ['manager_uji', $this->manager, 'Laporan Penjualan'],
        ];

        foreach ($roles as [$username, $user, $expectedText]) {
            $this->post('/login', [
                'username' => $username,
                'password' => 'password123',
            ])->assertRedirect('/dashboard');

            $this->assertAuthenticatedAs($user, 'karyawan');
            $this->get('/dashboard')->assertOk()->assertSee($expectedText);
            $this->post('/logout');
        }
    }

    public function test_ft02_add_product_with_photo_and_options(): void
    {
        Storage::fake('public');
        $tmp = tempnam(sys_get_temp_dir(), 'produk');
        file_put_contents($tmp, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Z1ZkAAAAASUVORK5CYII='));

        $response = $this->actingAs($this->admin, 'karyawan')->post(route('produks.store'), [
            'supplier_id' => $this->supplier->id,
            'kategori_produk_id' => $this->kategori->id,
            'nama_produk' => 'Produk Uji Baru',
            'deskripsi' => 'Deskripsi produk uji',
            'gambar' => new UploadedFile($tmp, 'produk-uji.png', 'image/png', null, true),
            'harga_jual' => 25000,
            'harga_beli' => 12000,
            'tipe' => 'jual',
            'satuan' => 'pcs',
            'stock' => 10,
            'status_aktif' => 1,
            'is_customizable' => 1,
            'opsis' => [
                [
                    'kategori' => 'Level Gula',
                    'tipe' => 'single',
                    'is_required' => 1,
                    'nama_opsi' => 'Less Sugar',
                    'harga_tambahan' => 0,
                ],
            ],
        ]);

        $response->assertRedirect(route('produks.index'));
        $produk = Produk::where('nama_produk', 'Produk Uji Baru')->firstOrFail();
        $this->assertNotNull($produk->gambar);
        Storage::disk('public')->assertExists($produk->gambar);
        $this->assertTrue($produk->has_gambar_data);
        $this->assertDatabaseHas('produk_gambars', ['produk_id' => $produk->id]);
        $this->assertDatabaseHas('produk_opsis', ['produk_id' => $produk->id, 'nama_opsi' => 'Less Sugar']);
        $this->get('/')->assertOk()->assertSee('Produk Uji Baru')->assertSee('Less Sugar');

        // Simulasikan filesystem container produksi yang dibersihkan saat
        // restart: gambar harus tetap tersaji dari database.
        Storage::disk('public')->delete($produk->gambar);
        $produk->refresh();

        $this->get(route('produks.gambar', $produk))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');
        $this->get('/')->assertOk()->assertSee('/produk/'.$produk->id.'/gambar', false);

        @unlink($tmp);
    }

    public function test_ft03_master_data_crud(): void
    {
        $this->actingAs($this->admin, 'karyawan')
            ->post(route('kategori-produks.store'), ['nama_kategori' => 'Kategori Uji Baru'])
            ->assertRedirect(route('kategori-produks.index'));

        $kategori = KategoriProduk::where('nama_kategori', 'Kategori Uji Baru')->firstOrFail();
        $this->actingAs($this->admin, 'karyawan')
            ->put(route('kategori-produks.update', $kategori), ['nama_kategori' => 'Kategori Uji Diubah'])
            ->assertRedirect(route('kategori-produks.index'));
        $this->assertDatabaseHas('kategori_produks', ['id' => $kategori->id, 'nama_kategori' => 'Kategori Uji Diubah']);
        $this->actingAs($this->admin, 'karyawan')->delete(route('kategori-produks.destroy', $kategori));
        $this->assertDatabaseMissing('kategori_produks', ['id' => $kategori->id]);

        $this->actingAs($this->admin, 'karyawan')
            ->post(route('suppliers.store'), ['nama_supplier' => 'Supplier Baru Uji', 'alamat' => 'Jakarta', 'no_telp' => '021123456'])
            ->assertRedirect(route('suppliers.index'));
        $supplier = Supplier::where('nama_supplier', 'Supplier Baru Uji')->firstOrFail();
        $this->actingAs($this->admin, 'karyawan')
            ->put(route('suppliers.update', $supplier), ['nama_supplier' => 'Supplier Diubah Uji', 'alamat' => 'Bandung', 'no_telp' => '022123456'])
            ->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'nama_supplier' => 'Supplier Diubah Uji']);
        $this->actingAs($this->admin, 'karyawan')->delete(route('suppliers.destroy', $supplier));
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_ft04_promo_crud_and_timezone(): void
    {
        $this->actingAs($this->admin, 'karyawan')->post(route('promo-kodes.store'), [
            'kode' => 'promo-uji',
            'nama_promo' => 'Promo Uji',
            'nilai_diskon' => 15,
            'min_belanja' => 10000,
            'maks_diskon' => 20000,
            'tanggal_mulai' => '2026-11-08T08:00',
            'tanggal_berakhir' => '2026-11-30T23:59',
            'kuota' => 10,
            'is_aktif' => 1,
        ])->assertRedirect(route('promo-kodes.index'));

        $promo = PromoKode::where('kode', 'PROMO-UJI')->firstOrFail();
        $this->assertSame('2026-11-08 01:00:00', $promo->tanggal_mulai->format('Y-m-d H:i:s'));

        $this->actingAs($this->admin, 'karyawan')->put(route('promo-kodes.update', $promo), [
            'kode' => 'PROMO-UJI',
            'nama_promo' => 'Promo Uji Diubah',
            'nilai_diskon' => 20,
            'min_belanja' => 10000,
            'maks_diskon' => 20000,
            'tanggal_mulai' => '2026-11-08T08:00',
            'tanggal_berakhir' => '2026-11-30T23:59',
            'kuota' => 10,
            'is_aktif' => 1,
        ])->assertRedirect(route('promo-kodes.index'));
        $this->assertDatabaseHas('promo_kodes', ['id' => $promo->id, 'nama_promo' => 'Promo Uji Diubah']);

        $this->actingAs($this->admin, 'karyawan')->delete(route('promo-kodes.destroy', $promo));
        $this->assertDatabaseMissing('promo_kodes', ['id' => $promo->id]);
    }

    public function test_ft05_order_via_table_qr(): void
    {
        $response = $this->postJson('/api/transaksis', $this->apiOrder([
            'session_id' => 'SESI-QR-1',
            'items' => [
                ['produk_id' => $this->produk->id, 'qty' => 1, 'customization' => 'Gula Aren'],
            ],
        ]));

        $response->assertCreated()->assertJsonPath('data.total_harga', 27500);
        $transaksi = Transaksi::findOrFail($response->json('data.id'));
        $this->assertSame($this->meja1->id, $transaksi->meja_id);
        $this->assertSame('pending', $transaksi->status_pesanan);
        $this->assertDatabaseHas('detail_transaksis', [
            'transaksi_id' => $transaksi->id,
            'customization' => 'Gula Aren',
            'subtotal' => 25000,
        ]);
    }

    public function test_ft06_checkout_dine_in_and_take_away(): void
    {
        $dineIn = $this->postJson('/api/transaksis', $this->apiOrder([
            'meja_id' => $this->meja2->id,
            'tipe_pesanan' => 'dine-in',
            'session_id' => 'SESI-DINE',
        ]));
        $dineIn->assertCreated();
        $this->assertDatabaseHas('transaksis', [
            'id' => $dineIn->json('data.id'),
            'meja_id' => $this->meja2->id,
            'tipe_pesanan' => 'dine-in',
            'status_pesanan' => 'pending',
        ]);

        $takeAway = $this->postJson('/api/transaksis', $this->apiOrder([
            'meja_id' => null,
            'tipe_pesanan' => 'take-away',
            'session_id' => 'SESI-TA',
        ]));
        $takeAway->assertCreated();
        $this->assertDatabaseHas('transaksis', [
            'id' => $takeAway->json('data.id'),
            'meja_id' => null,
            'tipe_pesanan' => 'take-away',
            'status_pesanan' => 'pending',
        ]);
    }

    public function test_ft07_qris_payment_creates_qr_and_updates_status(): void
    {
        // Jangan memanggil API Midtrans sungguhan dari test: hasilnya bergantung
        // pada kredensial sandbox dan koneksi jaringan. Gunakan respons QRIS palsu.
        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('createQrisCharge')
                ->once()
                ->andReturn([
                    'order_id' => 'TRX-TEST-QRIS',
                    'qr_url' => 'https://example.test/qris.png',
                    'qr_string' => 'dummy-qr-string',
                    'transaction_status' => 'pending',
                ]);
        });

        $response = $this->postJson('/api/transaksis', $this->apiOrder([
            'metode_pembayaran' => 'QRIS',
            'session_id' => 'SESI-QRIS',
        ]));

        $response->assertCreated();
        $response->assertJsonStructure(['data' => ['qris' => ['order_id', 'qr_url']]]);
    }

    public function test_ft07b_qris_callback_updates_payment_status(): void
    {
        $response = $this->postJson('/api/transaksis', $this->apiOrder([
            'metode_pembayaran' => 'QRIS',
            'session_id' => 'SESI-QRIS-CALLBACK',
        ]))->assertCreated();

        $transaksi = Transaksi::findOrFail($response->json('data.id'));
        $this->postJson('/midtrans/notification', $this->midtransPayload($transaksi))
            ->assertOk()
            ->assertJson(['status' => 'OK']);

        $this->assertSame('completed', $transaksi->fresh()->status_pesanan);
        $this->assertDatabaseHas('pembayarans', [
            'transaksi_id' => $transaksi->id,
            'metode_pembayaran' => 'QRIS',
            'status_pembayaran' => 'paid',
        ]);
    }

    public function test_ft08_valid_promo_is_applied(): void
    {
        $this->postJson('/api/promo/validate', [
            'kode' => 'hemat10',
            'subtotal' => 100000,
            'session_id' => 'SESI-PROMO-VALID',
        ])->assertOk()->assertJsonPath('data.diskon', 10000);

        $response = $this->postJson('/api/transaksis', $this->apiOrder([
            'meja_id' => $this->meja3->id,
            'session_id' => 'SESI-PROMO-VALID',
            'kode_promo' => 'HEMAT10',
            'items' => [['produk_id' => $this->produk->id, 'qty' => 5, 'customization' => null]],
        ]));

        $response->assertCreated()->assertJsonPath('data.diskon', 10000);
        $transaksi = Transaksi::findOrFail($response->json('data.id'));
        $this->assertSame('10000.00', $transaksi->diskon);
        $this->assertSame('100000.00', $transaksi->total_harga);
    }

    public function test_ft09_order_status_flow(): void
    {
        $response = $this->postJson('/api/transaksis', $this->apiOrder([
            'meja_id' => $this->meja2->id,
            'session_id' => 'SESI-STATUS',
        ]))->assertCreated();

        $transaksi = Transaksi::findOrFail($response->json('data.id'));

        foreach (['prosesed', 'ready', 'completed'] as $status) {
            $this->actingAs($this->kasir, 'karyawan')
                ->patchJson(route('transaksis.status', $transaksi), ['status_pesanan' => $status])
                ->assertOk()
                ->assertJsonPath('status_pesanan', $status);

            $this->getJson('/api/pesanan-saya?session_id=SESI-STATUS')
                ->assertOk()
                ->assertJsonPath('data.status_pesanan', $status);
        }
    }

    public function test_ft10_queue_is_fifo(): void
    {
        $ids = [];

        foreach ([['10:00', $this->meja2->id, 'dine-in'], ['10:05', $this->meja3->id, 'dine-in'], ['10:10', null, 'take-away']] as [$time, $mejaId, $tipePesanan]) {
            Carbon::setTestNow(Carbon::parse('2026-11-08 ' . $time . ':00', 'UTC'));
            $response = $this->postJson('/api/transaksis', $this->apiOrder([
                'meja_id' => $mejaId,
                'tipe_pesanan' => $tipePesanan,
                'session_id' => 'SESI-FIFO-' . $time,
            ]))->assertCreated();
            $ids[] = $response->json('data.id');
        }

        $response = $this->actingAs($this->kasir, 'karyawan')->getJson(route('transaksis.active'));
        $response->assertOk();
        $this->assertSame($ids[0], $response->json('orders.0.id'), 'Antrean harus menampilkan pesanan paling awal (FIFO).');
    }

    public function test_ft11_stock_reduces_and_low_stock_warning(): void
    {
        $response = $this->postJson('/api/transaksis', $this->apiOrder([
            'meja_id' => $this->meja1->id,
            'items' => [['produk_id' => $this->produk->id, 'qty' => 3, 'customization' => null]],
        ]))->assertCreated();

        $transaksi = Transaksi::findOrFail($response->json('data.id'));
        $this->assertSame(17, $this->produk->fresh()->stock);

        $this->postJson('/midtrans/notification', $this->midtransPayload($transaksi))->assertOk();
        $this->assertSame(17, $this->produk->fresh()->stock, 'Stok tidak boleh berkurang dua kali.');
        $this->assertSame('completed', $transaksi->fresh()->status_pesanan);
        $this->actingAs($this->admin, 'karyawan')->get(route('produks.index'))
            ->assertOk()
            ->assertSee('Peringatan Stok Tipis');
    }

    public function test_ft12_manual_cashier_order_with_cash(): void
    {
        $response = $this->actingAs($this->kasir, 'karyawan')->post(route('transaksis.store'), [
            'meja_id' => $this->meja2->id,
            'tipe_pesanan' => 'dine-in',
            'metode_pembayaran' => 'cash',
            'items' => [
                ['produk_id' => $this->produk->id, 'qty' => 1, 'customization' => null],
            ],
        ]);

        $response->assertRedirect(route('dashboard'));
        $transaksi = Transaksi::latest('id')->firstOrFail();
        $this->assertSame($this->kasir->id, $transaksi->karyawan_id);
        $this->assertSame('pending', $transaksi->status_pesanan);
        $this->assertSame(22000.00, (float) $transaksi->total_harga);
        $this->actingAs($this->kasir, 'karyawan')->getJson(route('transaksis.active'))
            ->assertOk()
            ->assertJsonFragment(['id' => $transaksi->id]);
    }

    public function test_ft13_manager_dashboard_periods(): void
    {
        Transaksi::create([
            'total_harga' => 100000,
            'status_pesanan' => 'completed',
            'waktu_transaksi' => '2026-11-08 05:00:00',
        ]);
        Transaksi::create([
            'total_harga' => 50000,
            'status_pesanan' => 'completed',
            'waktu_transaksi' => '2026-11-03 05:00:00',
        ]);

        $this->actingAs($this->manager, 'karyawan')
            ->get('/dashboard?periode=harian&tanggal=2026-11-08')
            ->assertOk()
            ->assertSee(number_format(100000, 0, ',', '.'))
            ->assertDontSee(number_format(150000, 0, ',', '.'));

        $this->actingAs($this->manager, 'karyawan')
            ->get('/dashboard?periode=mingguan&tanggal=2026-11-08')
            ->assertOk()
            ->assertSee(number_format(150000, 0, ',', '.'));

        $this->actingAs($this->manager, 'karyawan')
            ->get('/dashboard?periode=bulanan&tanggal=2026-11-08')
            ->assertOk()
            ->assertSee(number_format(150000, 0, ',', '.'));
    }

    public function test_ft14_export_report_pdf(): void
    {
        Transaksi::create([
            'total_harga' => 100000,
            'status_pesanan' => 'completed',
            'waktu_transaksi' => '2026-11-08 05:00:00',
        ]);

        $response = $this->actingAs($this->manager, 'karyawan')
            ->get('/laporan/export?periode=harian&tanggal=2026-11-08');

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_ft15_status_visible_from_other_device(): void
    {
        $response = $this->postJson('/api/transaksis', $this->apiOrder([
            'meja_id' => $this->meja1->id,
            'session_id' => 'SESI-LAIN',
        ]))->assertCreated();

        $id = $response->json('data.id');

        $this->getJson('/api/pesanan-saya?meja_id=' . $this->meja1->id)
            ->assertOk()
            ->assertJsonPath('data.id', $id);

        $this->getJson('/api/pesanan-saya?session_id=SESI-LAIN')
            ->assertOk()
            ->assertJsonPath('data.id', $id);
    }

    public function test_nt01_dashboard_requires_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_nt02_role_access_is_forbidden(): void
    {
        $this->actingAs($this->kasir, 'karyawan')
            ->get('/produks')
            ->assertForbidden();
    }

    public function test_nt03_idor_random_session_is_rejected(): void
    {
        $this->postJson('/api/transaksis', $this->apiOrder(['session_id' => 'SESI-PUNYA-SENDIRI']))->assertCreated();

        $this->getJson('/api/pesanan-saya?session_id=SESI-ORANG-LAIN')->assertNotFound();
    }

    public function test_nt04_empty_cart_and_missing_table_are_rejected(): void
    {
        $this->postJson('/api/transaksis', $this->apiOrder(['items' => []]))->assertUnprocessable();

        $this->postJson('/api/transaksis', $this->apiOrder([
            'meja_id' => null,
            'tipe_pesanan' => 'dine-in',
        ]))->assertUnprocessable()->assertJsonValidationErrors('meja_id');
    }

    public function test_nt05_out_of_stock_product_is_rejected(): void
    {
        $this->postJson('/api/transaksis', $this->apiOrder([
            'items' => [['produk_id' => $this->produkHabis->id, 'qty' => 1, 'customization' => null]],
        ]))->assertUnprocessable()->assertJsonValidationErrors('items');
    }

    public function test_nt06_duplicate_active_table_is_rejected(): void
    {
        $this->postJson('/api/transaksis', $this->apiOrder([
            'meja_id' => $this->meja1->id,
            'session_id' => 'SESI-MEJA-1',
        ]))->assertCreated();

        $this->postJson('/api/transaksis', $this->apiOrder([
            'meja_id' => $this->meja1->id,
            'session_id' => 'SESI-MEJA-2',
        ]))->assertUnprocessable()->assertJsonValidationErrors('meja_id');
    }

    public function test_nt07_invalid_promos_are_rejected(): void
    {
        PromoKode::create([
            'kode' => 'EXPIRED',
            'nilai_diskon' => 10,
            'min_belanja' => 0,
            'tanggal_mulai' => Carbon::now()->subDays(10),
            'tanggal_berakhir' => Carbon::now()->subDay(),
            'kuota' => 10,
            'kuota_terpakai' => 0,
            'is_aktif' => true,
        ]);
        PromoKode::create([
            'kode' => 'KUOTAHABIS',
            'nilai_diskon' => 10,
            'min_belanja' => 0,
            'kuota' => 1,
            'kuota_terpakai' => 1,
            'is_aktif' => true,
        ]);
        PromoKode::create([
            'kode' => 'MINIMAL50',
            'nilai_diskon' => 10,
            'min_belanja' => 50000,
            'kuota' => 10,
            'kuota_terpakai' => 0,
            'is_aktif' => true,
        ]);

        foreach (['EXPIRED', 'KUOTAHABIS', 'MINIMAL50'] as $kode) {
            $this->postJson('/api/promo/validate', [
                'kode' => $kode,
                'subtotal' => 25000,
                'session_id' => 'SESI-INVALID-' . $kode,
            ])->assertUnprocessable()->assertJsonValidationErrors('kode');
        }
    }

    public function test_nt08_second_promo_in_same_session_is_rejected(): void
    {
        PromoUsage::create([
            'promo_kode_id' => $this->promo->id,
            'meja_id' => null,
            'transaksi_id' => null,
            'session_id' => 'SESI-PROMO-DUA',
            'discount_amount' => 1000,
            'status' => 'active',
            'used_at' => now(),
        ]);

        $this->postJson('/api/promo/validate', [
            'kode' => 'HEMAT10',
            'subtotal' => 25000,
            'session_id' => 'SESI-PROMO-DUA',
        ])->assertUnprocessable()->assertJsonValidationErrors('kode');
    }

    public function test_nt09_dangerous_and_oversized_uploads_are_rejected(): void
    {
        $phpFile = tempnam(sys_get_temp_dir(), 'evil');
        file_put_contents($phpFile, '<?php echo "evil"; ?>');

        $this->actingAs($this->admin, 'karyawan')->post(route('produks.store'), [
            'supplier_id' => $this->supplier->id,
            'kategori_produk_id' => $this->kategori->id,
            'nama_produk' => 'Upload Berbahaya',
            'gambar' => new UploadedFile($phpFile, 'evil.jpg', 'image/jpeg', null, true),
            'harga_jual' => 25000,
            'harga_beli' => 12000,
            'tipe' => 'jual',
            'satuan' => 'pcs',
            'stock' => 10,
            'status_aktif' => 1,
        ])->assertSessionHasErrors('gambar');

        $largeFile = UploadedFile::fake()->create('besar.jpg', 3000, 'image/jpeg');
        $this->actingAs($this->admin, 'karyawan')->post(route('produks.store'), [
            'supplier_id' => $this->supplier->id,
            'kategori_produk_id' => $this->kategori->id,
            'nama_produk' => 'Upload Besar',
            'gambar' => $largeFile,
            'harga_jual' => 25000,
            'harga_beli' => 12000,
            'tipe' => 'jual',
            'satuan' => 'pcs',
            'stock' => 10,
            'status_aktif' => 1,
        ])->assertSessionHasErrors('gambar');

        @unlink($phpFile);
    }

    public function test_nt11_sql_injection_and_xss_are_safe(): void
    {
        $this->actingAs($this->admin, 'karyawan')
            ->get('/produks?search=' . urlencode("' OR 1=1 --"))
            ->assertOk()
            ->assertDontSee('SQLSTATE');

        Produk::create([
            'supplier_id' => $this->supplier->id,
            'kategori_produk_id' => $this->kategori->id,
            'nama_produk' => '<script>alert(1)</script>',
            'harga_jual' => 1000,
            'harga_beli' => 500,
            'tipe' => 'jual',
            'satuan' => 'pcs',
            'stock' => 1,
            'status_aktif' => true,
        ]);

        $this->actingAs($this->admin, 'karyawan')
            ->get('/produks')
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_nt12_expired_qris_cancels_order_and_restores_stock(): void
    {
        $response = $this->postJson('/api/transaksis', $this->apiOrder([
            'metode_pembayaran' => 'QRIS',
            'session_id' => 'SESI-EXPIRED',
            'items' => [['produk_id' => $this->produk->id, 'qty' => 2, 'customization' => null]],
        ]))->assertCreated();

        $transaksi = Transaksi::findOrFail($response->json('data.id'));
        $transaksi->update(['waktu_transaksi' => now()->subMinutes(20)]);
        $stockAfterOrder = $this->produk->fresh()->stock;

        $this->postJson('/midtrans/notification', $this->midtransPayload($transaksi, 'expire'))->assertOk();

        $this->assertNotSame('pending', $transaksi->fresh()->status_pesanan, 'Pesanan QRIS kedaluwarsa harus dibatalkan.');
        $this->assertSame($stockAfterOrder + 2, $this->produk->fresh()->stock, 'Stok harus dikembalikan saat QRIS kedaluwarsa.');
    }

    public function test_nt13_duplicate_callback_is_idempotent(): void
    {
        $response = $this->postJson('/api/transaksis', $this->apiOrder([
            'metode_pembayaran' => 'QRIS',
            'session_id' => 'SESI-DUPLIKAT',
        ]))->assertCreated();

        $transaksi = Transaksi::findOrFail($response->json('data.id'));
        $stock = $this->produk->fresh()->stock;
        $payload = $this->midtransPayload($transaksi);

        $this->postJson('/midtrans/notification', $payload)->assertOk();
        $this->postJson('/midtrans/notification', $payload)->assertOk();

        $this->assertSame('completed', $transaksi->fresh()->status_pesanan);
        $this->assertSame(1, Pembayaran::where('transaksi_id', $transaksi->id)->count());
        $this->assertSame($stock, $this->produk->fresh()->stock);
    }

    public function test_nt14_not_found_and_server_error_pages_are_safe(): void
    {
        $this->get('/halaman-tidak-ada-12345')
            ->assertNotFound()
            ->assertDontSee('SQLSTATE')
            ->assertDontSee('Stack trace');

        Route::get('/sprint3-force-error', function () {
            throw new \RuntimeException('Simulasi error server');
        });
        config(['app.debug' => false]);

        $this->get('/sprint3-force-error')
            ->assertStatus(500)
            ->assertDontSee('Simulasi error server')
            ->assertDontSee('SQLSTATE')
            ->assertDontSee('Stack trace');
    }
}
