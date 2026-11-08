# S3-04: ISSUE TRACKING & RESOLVED BUGS LOG

## Informasi Laporan

| Item | Keterangan |
|---|---|
| Aplikasi | Bagi Kopi / Perkoci Eatery (Coffee Shop Order System) |
| Framework | Laravel 12.69.2 |
| PHP | 8.3.6 |
| Database verifikasi | MariaDB 10.11 |
| Periode commit | 7 - 8 November 2026 |
| Sumber data | 4 commit terakhir: `6f18adc`, `57ca5cc`, `f4cdac4`, `8766955` |
| Tanggal verifikasi | 8 November 2026 |
| Total bug | 13 |
| Status akhir | 13 CLOSED, 0 OPEN |

## Ringkasan Status

| Status | Jumlah |
|---|---:|
| CLOSED | 13 |
| OPEN | 0 |
| **Total** | **13** |

| Tingkat Keparahan | Jumlah |
|---|---:|
| CRITICAL | 1 |
| MAJOR | 9 |
| MINOR | 3 |
| **Total** | **13** |

## Tabel Issue Tracking

| Bug ID | Modul Terkait | Deskripsi Bug & Kondisi Gagal | Tingkat Keparahan | Akar Masalah (Root Cause) | Tindakan Perbaikan (Fixing Action) | Status | Commit Hash Git |
|---|---|---|---|---|---|---|---|
| BUG-01 | Security / Transaksi | Kasir A dapat membuka detail transaksi milik kasir B melalui `/transaksis/{id}` dan mendapat HTTP 200; data transaksi ikut tampil. | CRITICAL | Gate `view-transaction` hanya memeriksa role, tidak membandingkan `transaksi->karyawan_id` dengan user yang login. | Membuat `TransaksiPolicy` dengan aturan kepemilikan, mendaftarkan `Gate::policy`, dan mengganti `Gate::authorize` menjadi `$this->authorize('view', $transaksi)`. | CLOSED | commit 6f18adc |
| BUG-02 | Security / QRIS | Endpoint `showQrisPayment` dan `checkQrisPayment` dapat diakses memakai ID transaksi kasir lain karena hanya memakai `findOrFail($id)`. | MAJOR | Tidak ada object-level authorization pada endpoint QRIS. | Menambahkan `$this->authorize('view', $transaksi)` pada kedua endpoint QRIS. | CLOSED | commit 6f18adc |
| BUG-03 | Error Handling | Halaman 403/404/419/429/500/503 memakai tampilan default Laravel; error 500 tidak konsisten dengan tema aplikasi. | MINOR | Belum ada view error kustom dan renderer exception global. | Menambahkan `resources/views/errors/*` dan exception renderer di `bootstrap/app.php`. | CLOSED | commit 6f18adc |
| BUG-04 | Deployment / Docker | Gambar produk 404 setelah container deploy/restart karena symlink `public/storage` tidak tersedia; `storage:link` juga bisa gagal saat symlink sudah ada. | MAJOR | `storage:link` belum dijalankan saat startup dan pemanggilannya belum idempotent/tanpa `--force`. | Menambahkan `storage:link` pada CMD, menghapus pemanggilan saat build, dan memakai `php artisan storage:link --force` saat container start. | CLOSED | commit f4cdac4, 6f18adc |
| BUG-05 | Laporan / PDF | Laporan PDF multi-halaman tidak menampilkan nomor halaman; layout padat dan footer tidak konsisten. | MINOR | Footer PDF hanya teks statis; belum ada `page_text()` Dompdf dan optimasi margin/font/page-break. | Menambahkan `Halaman {PAGE_NUM} dari {PAGE_COUNT}` dan merapikan layout `laporan/pdf.blade.php`. | CLOSED | commit 57ca5cc |
| BUG-06 | Struk / Branding | Struk menampilkan identitas toko lama: "Espresso & Space" dan "Senopati, Jakarta". | MINOR | Teks header struk masih hardcoded data lama. | Mengubah header menjadi "BAGI KOPI / coffee & eatary / Soreang, Bandung". | CLOSED | commit 57ca5cc |
| BUG-07 | Produk / Kustomisasi | Saat admin mencentang opsi "Wajib", penyimpanan produk gagal validasi `opsis.*.is_required`. | MAJOR | Checkbox tidak memiliki `value="1"`, sehingga mengirim `"on"` yang tidak lolos aturan `boolean`. | Menambahkan `value="1"` pada checkbox opsi wajib. | CLOSED | commit f4cdac4 |
| BUG-08 | Checkout / UI | Perubahan tipe pesanan dan metode pembayaran tidak menyinkronkan tampilan checkout; pilihan meja dan tombol bayar bisa tetap kondisi lama. | MAJOR | `setOrderType()` tidak menormalkan tipe dan tidak memanggil sinkronisasi; select meja/radio pembayaran belum punya handler `onchange`. | Menambahkan normalisasi tipe, `updatePaymentMethodUI()`, `syncCheckoutState()`, dan handler `onchange`. | CLOSED | commit f4cdac4 |
| BUG-09 | Seeder / Deployment | Instalasi baru tidak bisa menambah produk karena belum ada supplier dan kategori; form gagal akibat foreign key. | MAJOR | `DatabaseSeeder` hanya membuat akun admin, tanpa data dasar supplier/kategori. | Menambahkan `firstOrCreate` supplier "Supplier Utama" dan kategori default. | CLOSED | commit f4cdac4 |
| BUG-10 | Laporan / Timezone | Tanggal dan jam laporan/dashboard bergeser 7 jam; batas periode harian/mingguan/bulanan tidak sesuai WIB; jadwal promo juga bergeser. | MAJOR | Aplikasi memakai UTC; perhitungan tanggal memakai `Carbon::now()` tanpa `Asia/Jakarta`, dan input promo tidak dikonversi dari WIB ke UTC. | Menetapkan `$tz = 'Asia/Jakarta'`, konversi start/end ke UTC untuk query, menampilkan WIB, dan parse tanggal promo WIB lalu `utc()`. | CLOSED | commit 8766955 |
| BUG-11 | Dashboard | Dashboard manager menampilkan data statis/dummy ("Okt 2024", "2.4 mnt/cup", persentase tetap) dan filter periode/tanggal belum berfungsi; dashboard admin masih placeholder "Real-time Data". | MAJOR | View memakai nilai hardcoded dan controller belum menghitung statistik/filter dari database. | Menghitung statistik periode, pembanding periode sebelumnya, top produk, serta filter harian/mingguan/bulanan; mengganti placeholder admin dengan data aktual. | CLOSED | commit 8766955 |
| BUG-12 | Status Pesanan | Halaman status pelanggan statis ("Meja #07", progres tetap) dan tidak berubah saat status pesanan diupdate kasir. | MAJOR | View berisi markup statis; belum ada endpoint data pesanan aktif dan polling. | Menambahkan `GET /api/pesanan-saya`, method `myOrder()`, dan polling `refreshMyOrder()` pada tab status. | CLOSED | commit 8766955 |
| BUG-13 | Laporan / Export | Tombol unduh laporan hanya menghasilkan CSV; belum tersedia laporan PDF yang dibutuhkan manajemen. | MAJOR | Export memakai `Response::stream` CSV; Dompdf dan view PDF belum ada. | Menambahkan dependensi Dompdf, view `laporan/pdf.blade.php`, render PDF A4 landscape, dan response `application/pdf`. | CLOSED | commit 8766955 |

## Daftar Commit

| Hash | Tanggal | Pesan Commit | Bug Terkait |
|---|---|---|---|
| `6f18adc` | 2026-11-08 | fix:halaman error,dockerfile,dan lainnya | BUG-01, BUG-02, BUG-03, BUG-04 |
| `57ca5cc` | 2026-11-07 | Improve exported PDF layout and footer numbering | BUG-05, BUG-06 |
| `f4cdac4` | 2026-11-07 | Fix product photo and customization inputs, default product seeders, storage link startup | BUG-04, BUG-07, BUG-08, BUG-09 |
| `8766955` | 2026-11-07 | Implement WIB reporting, live order status, PDF export, manager filters and real dashboard data | BUG-10, BUG-11, BUG-12, BUG-13 |

## Bukti Pengujian

- `php artisan test --filter=FunctionalReportTest` → **15 passed (58 assertions)**.
- `php artisan test` → **22 passed (70 assertions)**.
- `TC-IDOR-01` (BUG-01) diverifikasi otomatis dan sekarang menghasilkan HTTP 403 sesuai ekspektasi.
- `TC-PAY-02` (QRIS) dan `TC-ERR-01` (halaman error) juga tercakup dalam test suite.
- Environment pengujian: PHP 8.3.6, Laravel 12.69.2, MariaDB 10.11.
- Commit HEAD `6f18adc` sudah di-push dan working tree bersih.

## Catatan

- Semua bug berstatus **CLOSED** karena perbaikannya sudah masuk ke commit yang dicantumkan dan sudah di-push.
- BUG-04 melibatkan dua commit: `f4cdac4` menambahkan `storage:link` saat startup, lalu `6f18adc` membuatnya idempotent dengan `--force`.
- Bug ID diurutkan berdasarkan modul dan prioritas, bukan urutan kronologis commit.
- Laporan disusun dari perubahan nyata pada empat commit terakhir:
  `6f18adc`, `57ca5cc`, `f4cdac4`, dan `8766955`.
