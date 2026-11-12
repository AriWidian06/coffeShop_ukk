# S3-04: ISSUE TRACKING & RESOLVED BUGS LOG

## Informasi Laporan

| Item | Keterangan |
|---|---|
| Aplikasi | Bagi Kopi / Perkoci Eatery (Coffee Shop Order System) |
| Framework | Laravel 12.69.2 |
| PHP | 8.3.6 |
| Database verifikasi | MariaDB 10.11 |
| Periode commit | 7 November 2026 (Sprint 3) – 12 November 2026 (S4-01 Release Candidate) |
| Sumber data | Commit `6f18adc`, `57ca5cc`, `f4cdac4`, `8766955` (Sprint 3) dan `24fad18`, `1a59973` (audit S4-01) |
| Tanggal verifikasi | 8 November 2026 (Sprint 3) – 12 November 2026 (re-verifikasi S4-01) |
| Total bug | 18 |
| Status akhir | 18 CLOSED, 0 OPEN |
| Tag rilis | `v1.0.0-rc` |

## Ringkasan Status

| Status | Jumlah |
|---|---:|
| CLOSED | 18 |
| OPEN | 0 |
| **Total** | **18** |

| Tingkat Keparahan | Jumlah |
|---|---:|
| CRITICAL | 1 |
| MAJOR | 13 |
| MINOR | 4 |
| **Total** | **18** |

## Tabel Issue Tracking — Sprint 3 (BUG-01 s/d BUG-13)

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

## Tabel Issue Tracking — Audit Release Candidate (RC-01 s/d RC-05)

| Bug ID | Modul Terkait | Deskripsi Bug & Kondisi Gagal | Tingkat Keparahan | Akar Masalah (Root Cause) | Tindakan Perbaikan (Fixing Action) | Status | Commit Hash Git |
|---|---|---|---|---|---|---|---|
| RC-01 | Integritas Data / Stok | Pesanan QRIS yang dibatalkan (notifikasi `expire`/`deny`/`cancel`) sudah mengembalikan stok produk. Ketika kasir kemudian menghapus transaksi tersebut, stok dikembalikan **dua kali** sehingga jumlah stok produk naik melebihi jumlah sebenarnya (stok awal 10 → 12 pada repro). | MAJOR | `restoreStock()` dan `releasePromoUsage()` tidak idempoten dan dipanggil dari dua jalur berbeda (pembatalan QRIS & penghapusan transaksi) tanpa penanda bahwa pembalikan sudah terjadi. | Menambah kolom penanda `transaksis.sudah_dibalik` (boolean) beserta backfill untuk transaksi yang sudah `cancelled`, dan method `Transaksi::reverseStockAndPromo()` yang hanya membalik stok & kuota promo satu kali. `PaymentService::markAsCancelled()` dan `TransaksiController::destroy()` kini memakai method tersebut. | CLOSED | commit rilis `v1.0.0-rc` |
| RC-02 | Status Pesanan / Pelanggan | Pesanan berstatus `cancelled` (pembayaran gagal) tetap dikembalikan endpoint `GET /api/pesanan-saya` dengan label **"Menunggu Konfirmasi"** (step 1). Pelanggan melihat pesanan yang sudah dibatalkan masih menunggu, tanpa informasi bahwa pembayarannya gagal. | MAJOR | `myOrder()` tidak memfilter status transaksi dan array `$statusMeta` belum memiliki entri `cancelled`, sehingga terjadi fallback ke status `pending`. | Menambahkan `$statusMeta['cancelled'] = ['label' => 'Dibatalkan', 'step' => 0]` dan percabangan `cancelled` pada `renderMyOrder()` di `welcome.blade.php` (setara cabang `completed`) sehingga pelanggan melihat "Pesanan Anda dibatalkan karena pembayaran tidak berhasil. Silakan buat pesanan baru." | CLOSED | commit rilis `v1.0.0-rc` |
| RC-03 | Laporan / Dashboard | Transaksi berstatus `cancelled` (belum dibayar) **tetap dihitung sebagai pendapatan** pada `LaporanController` (ringkasan & export PDF) serta dashboard manager dan admin. Omzet yang dilaporkan menjadi lebih besar dari nilai transaksi yang benar-benar dibayar. | MAJOR | Query agregasi (`sum('total_harga')`, `COUNT`, dan join `topCategories`) tidak menyaring status `cancelled` yang baru diperkenalkan pada commit `24fad18`. | Menambahkan filter `status_pesanan != 'cancelled'` pada seluruh query pendapatan di `LaporanController`, closure dashboard manager/admin di `routes/web.php`, serta `DashboardController`. | CLOSED | commit rilis `v1.0.0-rc` |
| RC-04 | UI / Kasir | Badge status pada daftar transaksi, detail transaksi, dan dashboard kasir menampilkan string mentah **`cancelled`** (bahasa Inggris) karena `$statusLabels` belum memuat status tersebut. | MINOR | Status `cancelled` ditambahkan di backend tanpa pembaruan peta label & warna pada view. | Menambahkan `'cancelled' => 'Dibatalkan'` pada `$statusLabels` dan warna `bg-rose-*` pada `$statusColors` di `transaksis/index.blade.php`, `transaksis/show.blade.php`, dan `dashboard/kasir.blade.php`. | CLOSED | commit rilis `v1.0.0-rc` |
| RC-05 | CI / Pipeline | GitHub Actions di branch `main` **berstatus gagal** (merah) pada dua job: PHP 8.3 gagal di step "Run feature & unit test suite" karena tabel `mejas` belum ada di database uji yang kosong, dan PHP 8.2 gagal di step "Install PHP dependencies" karena `laravel/pint` v1.32.1 memerlukan PHP `^8.3`. | MAJOR | Trait `RefreshDatabase` pada `tests/Feature/ExampleTest.php` dikomentari sehingga test pertama yang dijalankan tidak menyiapkan skema; matrix CI masih memuat PHP 8.2 yang tidak memenuhi syarat dependensi dev. | Mengaktifkan kembali `RefreshDatabase` pada `tests/Feature/ExampleTest.php`, menyederhanakan matrix CI menjadi PHP `8.3` (image Docker produksi memakai `composer install --no-dev` sehingga paket dev tidak ikut terpasang), dan menyinkronkan `composer.lock` (`composer update --lock`) agar warning "lock file is not up to date" hilang. | CLOSED | commit rilis `v1.0.0-rc` |

## Daftar Commit

| Hash | Tanggal | Pesan Commit | Bug Terkait |
|---|---|---|---|
| `6f18adc` | 2026-11-08 | fix:halaman error,dockerfile,dan lainnya | BUG-01, BUG-02, BUG-03, BUG-04 |
| `57ca5cc` | 2026-11-07 | Improve exported PDF layout and footer numbering | BUG-05, BUG-06 |
| `f4cdac4` | 2026-11-07 | Fix product photo and customization inputs, default product seeders, storage link startup | BUG-04, BUG-07, BUG-08, BUG-09 |
| `8766955` | 2026-11-07 | Implement WIB reporting, live order status, PDF export, manager filters and real dashboard data | BUG-10, BUG-11, BUG-12, BUG-13 |
| `24fad18` | 2026-11-11 | fix(security): perbaiki vulnerability & integritas data pada alur order | sumber RC-01, RC-02, RC-03 (perkenalan status `cancelled`) |
| `1a59973` | 2026-11-11 | fix status pada halaman pelanggan | pemisahan database uji pada `phpunit.xml` |
| `v1.0.0-rc` | 2026-11-12 | Release Candidate 1.0.0 CiwideyStay Final Exam | RC-01, RC-02, RC-03, RC-04, RC-05 |

## Bukti Pengujian

Verifikasi ulang pada 12 November 2026 (Release Candidate):

- `php artisan test` → **37 passed (117 assertions)**.
- `vendor/bin/phpunit --configuration=phpunit.sprint3.xml` → **29 passed (175 assertions)**.
- Kedua suite dijalankan pada `db_perkoci_test` yang **dibuat kosong dari nol** (simulasi runner GitHub Actions) → hijau, sesuai kriteria RC-05.
- `tests/Feature/ReleaseCandidateTest.php` (7 test baru) dibuktikan **gagal** pada kode sebelum perbaikan (6 dari 7 test gagal) dan **lulus** sesudahnya.
- Bukti manual RC-01: stok produk 10 → 8 (pesanan dibuat) → 10 (QRIS dibatalkan) → **10** (transaksi dihapus, tidak terbalik lagi).
- Bukti manual RC-02: `GET /api/pesanan-saya` untuk transaksi `cancelled` kini mengembalikan `status_label = "Dibatalkan"` (sebelumnya `"Menunggu Konfirmasi"`).
- Environment pengujian: PHP 8.3.6, Laravel 12.69.2, MariaDB 10.11.14.
- GitHub Actions pada branch `main`: hijau (Frontend Build, Backend Tests PHP 8.3) setelah tag `v1.0.0-rc`.

## Catatan

- Seluruh 18 tiket berstatus **CLOSED**: 13 tiket Sprint 3 hasil Fixing Sprint 3 dan 5 tiket hasil audit Release Candidate S4-01.
- Dokumen ini pernah terhapus pada commit `e2755c1` ("update keamanan") dan dipulihkan kembali pada rilis `v1.0.0-rc` untuk menjadi bukti auditable bahwa seluruh tiket Sprint 3 berstatus CLOSED.
- RC-01 sampai RC-04 berakar pada satu perubahan di commit `24fad18`: penambahan status `cancelled` belum ikut diperbarui pada alur pembatalan stok, halaman status pelanggan, perhitungan pendapatan, serta label UI.
- RC-05 sebenarnya sudah merah sejak commit `1a59973` (12 November 2026) dan baru terdeteksi saat audit Release Candidate.

## Rekomendasi Lanjutan (di luar lingkup RC)

1. Menjalankan `composer update` penuh untuk menutup 10 advisory keamanan pada dependensi transitif `symfony/http-foundation`, `symfony/http-kernel`, dan `league/commonmark`. Rilis ini sengaja tidak melakukan upgrade 54 paket agar kandidat rilis tetap stabil; update penuh perlu stage pengujian tersendiri sebelum rilis v1.0.0 final.
2. Melepas `vendor/` (8.539 file) dan `node_modules/` (3.661 file) dari version control — keduanya sudah masuk `.gitignore` tetapi masih ter-track karena di-commit sebelum aturan tersebut dibuat.
3. Menghapus `app/Http/Controllers/DashboardController.php` (kode mati) atau memindahkannya ke dalam closure route agar tidak ada dua sumber perhitungan dashboard.
