<?php

namespace Tests\Feature;

use App\Models\DetailTransaksi;
use App\Models\Karyawan;
use App\Models\KategoriProduk;
use App\Models\Meja;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Regression test untuk perbaikan Release Candidate v1.0.0-rc (S4-01).
 *
 * Setiap test di sini GAGAL pada versi sebelum perbaikan dan LULUS setelahnya:
 *  - RC-01: stok produk tidak boleh terbalik dua kali (batal lalu dihapus)
 *  - RC-02: pesanan berstatus 'cancelled' tidak boleh tampil sebagai "Menunggu Konfirmasi"
 *  - RC-03: transaksi 'cancelled' (belum dibayar) tidak boleh dihitung sebagai pendapatan
 *  - RC-04: label status 'cancelled' harus tampil dalam bahasa Indonesia
 *  - RC-05: pipeline CI harus hijau pada database uji yang masih kosong
 */
class ReleaseCandidateTest extends TestCase
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

    /** Transaksi web (tanpa meja) berisi 1 item produk. */
    private function transaksiWeb(Produk $produk, int $qty = 2, string $sessionId = 'SESI-RC'): Transaksi
    {
        $trx = Transaksi::create([
            'sumber_pesanan' => 'web',
            'tipe_pesanan' => 'take-away',
            'total_harga' => $produk->harga_jual * $qty,
            'status_pesanan' => 'pending',
            'waktu_transaksi' => now(),
            'session_id' => $sessionId,
        ]);

        DetailTransaksi::create([
            'transaksi_id' => $trx->id,
            'produk_id' => $produk->id,
            'QTY' => $qty,
            'subtotal' => $produk->harga_jual * $qty,
        ]);

        $produk->decrement('stock', $qty);

        return $trx;
    }

    private function midtransPayload(Transaksi $transaksi, string $status = 'expire'): array
    {
        $orderId = 'TRX-'.$transaksi->id.'-'.time();
        $gross = number_format((float) $transaksi->total_harga, 2, '.', '');
        $signature = hash('sha512', $orderId.'200'.$gross.config('services.midtrans.server_key'));

        return [
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => $gross,
            'transaction_status' => $status,
            'fraud_status' => 'accept',
            'signature_key' => $signature,
        ];
    }

    /**
     * RC-01: transaksi yang dibatalkan (QRIS expired) lalu dihapus kasir tidak boleh
     * mengembalikan stok dua kali. Sebelumnya stok naik melebihi jumlah sebenarnya.
     */
    public function test_rc01_stok_tidak_terbalik_dua_kali_setelah_batal_lalu_dihapus(): void
    {
        config(['services.midtrans.server_key' => 'server-key-uji']);

        $admin = $this->karyawan('admin', 'adminRC1');
        $produk = $this->produk(20000, 50);
        $trx = $this->transaksiWeb($produk, 2, 'SESI-RC01');

        $this->assertSame(48, (int) $produk->fresh()->stock, 'Stok harus berkurang setelah pesanan dibuat.');

        // 1) Pembayaran QRIS kedaluwarsa -> pesanan dibatalkan, stok dikembalikan.
        $this->postJson('/midtrans/notification', $this->midtransPayload($trx, 'expire'))->assertOk();

        $this->assertSame('cancelled', $trx->fresh()->status_pesanan, 'Pesanan QRIS expired harus berstatus cancelled.');
        $this->assertSame(50, (int) $produk->fresh()->stock, 'Stok harus dikembalikan saat pesanan dibatalkan.');

        // 2) Kasir menghapus transaksi yang sudah dibatalkan.
        $this->actingAs($admin, 'karyawan')
            ->delete('/transaksis/'.$trx->id)
            ->assertRedirect();

        $this->assertSame(
            50,
            (int) $produk->fresh()->stock,
            'Stok tidak boleh dikembalikan lagi saat transaksi yang sudah dibatalkan dihapus.'
        );
        $this->assertNull(Transaksi::find($trx->id), 'Transaksi harus benar-benar terhapus.');
    }

    /**
     * RC-01 (pelengkap): pembalikan stok tetap idempoten walau dipanggil berulang.
     */
    public function test_rc01_pembalikan_stok_bersifat_idempoten(): void
    {
        $produk = $this->produk(20000, 50);
        $trx = $this->transaksiWeb($produk, 3, 'SESI-RC01B');

        $trx->reverseStockAndPromo();
        $this->assertSame(50, (int) $produk->fresh()->stock);

        $trx->reverseStockAndPromo();
        $this->assertSame(50, (int) $produk->fresh()->stock, 'Pemanggilan kedua tidak boleh menambah stok lagi.');

        $this->assertTrue($trx->fresh()->sudah_dibalik, 'Transaksi harus ditandai sudah dibalik.');
    }

    /**
     * RC-02: pesanan berstatus 'cancelled' harus dilaporkan sebagai "Dibatalkan",
     * bukan "Menunggu Konfirmasi" yang menyesatkan pelanggan.
     */
    public function test_rc02_pesanan_batal_dilaporkan_sebagai_dibatalkan(): void
    {
        $produk = $this->produk(20000, 50);
        $trx = $this->transaksiWeb($produk, 1, 'SESI-RC02');
        $trx->update(['status_pesanan' => 'cancelled']);

        $response = $this->getJson('/api/pesanan-saya?session_id=SESI-RC02');

        $response->assertOk();
        $this->assertSame('cancelled', $response->json('data.status_pesanan'));
        $this->assertSame('Dibatalkan', $response->json('data.status_label'));
        $this->assertNotSame(
            'Menunggu Konfirmasi',
            $response->json('data.status_label'),
            'Pesanan batal tidak boleh tampil sebagai menunggu konfirmasi.'
        );
    }

    /**
     * RC-03: transaksi berstatus 'cancelled' (belum dibayar) tidak boleh dihitung
     * sebagai pendapatan pada laporan manager maupun dashboard.
     */
    public function test_rc03_transaksi_batal_tidak_terhitung_sebagai_pendapatan(): void
    {
        $manager = $this->karyawan('manager', 'managerRC3');
        $produk = $this->produk(20000, 50);

        // Transaksi batal: 90.000 (belum dibayar) -> tidak boleh jadi pendapatan.
        $batal = Transaksi::create([
            'total_harga' => 90000,
            'status_pesanan' => 'cancelled',
            'waktu_transaksi' => now(),
        ]);
        DetailTransaksi::create([
            'transaksi_id' => $batal->id,
            'produk_id' => $produk->id,
            'QTY' => 3,
            'subtotal' => 90000,
        ]);

        // Transaksi sah: 50.000 -> satu-satunya pendapatan yang valid.
        $valid = Transaksi::create([
            'total_harga' => 50000,
            'status_pesanan' => 'completed',
            'waktu_transaksi' => now(),
        ]);
        DetailTransaksi::create([
            'transaksi_id' => $valid->id,
            'produk_id' => $produk->id,
            'QTY' => 2,
            'subtotal' => 50000,
        ]);

        $response = $this->actingAs($manager, 'karyawan')->get('/dashboard?periode=harian&tanggal='.now()->format('Y-m-d'));

        $response->assertOk();
        $this->assertStringContainsString(
            number_format(50000, 0, ',', '.'),
            $response->getContent(),
            'Pendapatan harus hanya menghitung transaksi yang tidak dibatalkan.'
        );
        $this->assertStringNotContainsString(
            number_format(140000, 0, ',', '.'),
            $response->getContent(),
            'Transaksi batal tidak boleh ikut dihitung sebagai pendapatan.'
        );
    }

    /**
     * RC-03 (pelengkap): laporan export juga harus mengabaikan transaksi batal.
     */
    public function test_rc03_laporan_export_tidak_menghitung_transaksi_batal(): void
    {
        $manager = $this->karyawan('manager', 'managerRC3B');

        Transaksi::create([
            'total_harga' => 90000,
            'status_pesanan' => 'cancelled',
            'waktu_transaksi' => now(),
        ]);
        Transaksi::create([
            'total_harga' => 50000,
            'status_pesanan' => 'completed',
            'waktu_transaksi' => now(),
        ]);

        $response = $this->actingAs($manager, 'karyawan')
            ->get('/laporan/export?periode=harian&tanggal='.now()->format('Y-m-d'));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));

        $teks = $this->teksPdf($response->getContent());

        $this->assertStringContainsString(
            '50.000',
            $teks,
            'Pendapatan sah harus tetap tampil di laporan PDF.'
        );
        $this->assertStringNotContainsString(
            '140.000',
            $teks,
            'Total pendapatan pada laporan PDF tidak boleh termasuk transaksi batal.'
        );
    }

    /**
     * Ekstrak teks dari PDF Dompdf.
     * Dompdf menulis teks sebagai array operator "TJ" di dalam stream yang
     * dikompresi (Flate) dan setiap karakter diikuti byte null.
     */
    private function teksPdf(string $pdf): string
    {
        $hasil = [];

        if (preg_match_all('/stream(.*?)endstream/s', $pdf, $streams)) {
            foreach ($streams[1] as $stream) {
                $stream = ltrim($stream, "\r\n");
                $data = @gzuncompress($stream);

                if ($data === false) {
                    continue;
                }

                if (preg_match_all('/\[((?:[^\[\]\\\\]|\\\\.)*)\]\s*TJ/s', $data, $teks)) {
                    foreach ($teks[1] as $potongan) {
                        $hasil[] = str_replace("\0", '', $potongan);
                    }
                }
            }
        }

        $gabung = implode(' ', $hasil);

        // Buka escape sequence PDF: \( \) \\ dan \. (titik desimal pada font subset).
        return preg_replace_callback('/\\\\(.)/', fn ($m) => $m[1] === '.' ? '.' : $m[1], $gabung);
    }

    /**
     * RC-04: label status 'cancelled' harus tampil dalam bahasa Indonesia, bukan
     * string mentah "cancelled" yang membingungkan kasir.
     */
    public function test_rc04_label_status_batal_tampil_dalam_bahasa_indonesia(): void
    {
        $kasir = $this->karyawan('kasir', 'kasirRC4');
        $meja = $this->meja();
        $produk = $this->produk(20000, 50);

        $trx = Transaksi::create([
            'meja_id' => $meja->id,
            'karyawan_id' => $kasir->id,
            'tipe_pesanan' => 'dine-in',
            'total_harga' => 20000,
            'status_pesanan' => 'cancelled',
            'waktu_transaksi' => now(),
        ]);
        DetailTransaksi::create([
            'transaksi_id' => $trx->id,
            'produk_id' => $produk->id,
            'QTY' => 1,
            'subtotal' => 20000,
        ]);

        $response = $this->actingAs($kasir, 'karyawan')->get('/transaksis');

        $response->assertOk();
        $response->assertSee('Dibatalkan');
        $response->assertDontSee('>cancelled<');
    }

    /**
     * RC-05: pipeline CI harus hijau pada database uji yang masih kosong.
     * Test ini menjalankan migrasi dari nol, sama seperti runner GitHub Actions.
     */
    public function test_rc05_database_uji_kosong_tetap_lolos(): void
    {
        $this->assertTrue(
            Schema::hasTable('mejas'),
            'Migrasi harus membuat tabel mejas pada database uji yang kosong.'
        );
        $this->assertTrue(
            Schema::hasColumn('transaksis', 'sudah_dibalik'),
            'Kolom penanda pembalikan harus tersedia untuk mencegah pembalikan ganda.'
        );

        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
