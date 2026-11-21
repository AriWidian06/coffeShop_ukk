<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\KategoriProduk;
use App\Models\Meja;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresi untuk ekspor PDF berukuran besar.
 *
 * Sebelum perbaikan, 1.000 transaksi membuat Dompdf menghabiskan ± 478 MB
 * dan gagal pada memory_limit 128 MB (Allowed memory size exhausted).
 * Rincian transaksi kini digambar ke canvas PDF sehingga ekspor tetap
 * berhasil dengan memori < 64 MB.
 */
class LaporanExportLargeTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_pdf_dengan_seribu_transaksi_tetap_berhasil(): void
    {
        $manager = Karyawan::create([
            'nama_karyawan' => 'Manager Stress',
            'jabatan' => 'Manager',
            'role' => 'manager',
            'username' => 'manager-stress',
            'password' => 'Password123!',
        ]);

        $supplier = Supplier::create(['nama_supplier' => 'S', 'alamat' => 'A', 'no_telp' => '1']);
        $kategori = KategoriProduk::create(['nama_kategori' => 'K']);
        $produk = Produk::create([
            'supplier_id' => $supplier->id,
            'kategori_produk_id' => $kategori->id,
            'nama_produk' => 'Kopi Stress',
            'harga_jual' => 20000,
            'harga_beli' => 10000,
            'tipe' => 'jual',
            'satuan' => 'cup',
            'stock' => 5000,
            'status_aktif' => true,
        ]);
        $meja = Meja::create(['nomor_meja' => 1, 'kapasitas' => 4, 'status_aktif' => true]);

        $now = now();
        $statuses = ['pending', 'prosesed', 'ready', 'completed'];
        $transaksis = [];

        for ($i = 1; $i <= 1000; $i++) {
            $takeAway = $i % 5 === 0;
            $diskon = $i % 10 === 0 ? 5000 : 0;

            $transaksis[] = [
                'meja_id' => $takeAway ? null : $meja->id,
                'karyawan_id' => $manager->id,
                'tipe_pesanan' => $takeAway ? 'take-away' : 'dine-in',
                'total_harga' => 40000 - $diskon,
                'diskon' => $diskon,
                'status_pesanan' => $statuses[$i % 4],
                'waktu_transaksi' => $now,
            ];
        }

        foreach (array_chunk($transaksis, 250) as $chunk) {
            DB::table('transaksis')->insert($chunk);
        }

        $ids = DB::table('transaksis')->pluck('id');

        $details = [];
        $payments = [];

        foreach ($ids as $index => $id) {
            $qty = $index % 2 === 0 ? 1 : 2;

            $details[] = [
                'transaksi_id' => $id,
                'produk_id' => $produk->id,
                'QTY' => $qty,
                'subtotal' => 20000 * $qty,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Dua dari tiga transaksi sudah dibayar agar tampilan badge
            // "Lunas" beserta metode pembayarannya ikut teruji.
            if ($index % 3 !== 0) {
                $payments[] = [
                    'transaksi_id' => $id,
                    'jumlah_bayar' => 40000,
                    'metode_pembayaran' => $index % 2 === 0 ? 'cash' : 'QRIS',
                    'status_pembayaran' => 'paid',
                    'kembalian' => 0,
                    'waktu_pembayaran' => $now,
                ];
            }
        }

        foreach (array_chunk($details, 500) as $chunk) {
            DB::table('detail_transaksis')->insert($chunk);
        }

        foreach (array_chunk($payments, 500) as $chunk) {
            DB::table('pembayarans')->insert($chunk);
        }

        $response = $this->actingAs($manager, 'karyawan')
            ->get('/laporan/export?periode=bulanan');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());

        // PDF 30 halaman (ringkasan + lampiran 1.000 transaksi) jauh lebih
        // besar dari laporan biasa; pastikan seluruh lampiran ikut dihasilkan.
        $this->assertGreaterThan(300_000, strlen($response->getContent()));
    }
}
