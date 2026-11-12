# S4-01: RELEASE CANDIDATE v1.0.0-rc — RELEASE NOTES

## Identitas Rilis

| Item | Keterangan |
|---|---|
| Aplikasi | Bagi Kopi / Perkoci Eatery (Coffee Shop Order System) |
| Versi | `v1.0.0-rc` (Release Candidate 1) |
| Tag Git | `v1.0.0-rc` (annotated) |
| Branch rilis | `main` |
| Remote | `https://github.com/AriWidian06/coffeShop_ukk` |
| Tanggal | 12 November 2026 |
| Environment uji | PHP 8.3.6, Laravel 12.69.2, MariaDB 10.11.14, Composer 2.10.3 |

## Ringkasan

Release Candidate ini menutup 5 temuan baru hasil audit akhir Sprint 3 (RC-01 s/d RC-05) yang ditemukan setelah papan issue tracker Sprint 3 terakhir diperbarui. Empat di antaranya berakar pada satu perubahan di commit `24fad18` — penambahan status pesanan `cancelled` — yang belum ikut diperbarui pada alur pembatalan stok, halaman status pelanggan, perhitungan pendapatan, maupun label antarmuka. Temuan kelima adalah pipeline CI yang memang merah di branch `main` sejak commit `1a59973`.

Tidak ada perubahan fungsional di luar lima perbaikan tersebut.

## Daftar Perubahan

### Perbaikan (RC-01 s/d RC-05)

| ID | Sev | Ringkasan | File Utama |
|---|---|---|---|
| RC-01 | MAJOR | Stok produk tidak lagi terbalik dua kali ketika pesanan dibatalkan (QRIS expired) lalu dihapus kasir. | `database/migrations/2026_11_12_000000_add_sudah_dibalik_to_transaksis_table.php`, `app/Models/Transaksi.php`, `app/Http/Controllers/TransaksiController.php`, `app/Services/PaymentService.php` |
| RC-02 | MAJOR | Pesanan berstatus `cancelled` kini tampil sebagai **"Dibatalkan"** di halaman status pelanggan, bukan "Menunggu Konfirmasi". | `app/Http/Controllers/Api/TransaksiController.php`, `resources/views/welcome.blade.php` |
| RC-03 | MAJOR | Transaksi `cancelled` (belum dibayar) tidak lagi dihitung sebagai pendapatan pada laporan, export PDF, dan dashboard manager/admin. | `app/Http/Controllers/LaporanController.php`, `app/Http/Controllers/DashboardController.php`, `routes/web.php` |
| RC-04 | MINOR | Badge status `cancelled` tampil sebagai "Dibatalkan" (bukan string Inggris mentah) di daftar transaksi, detail transaksi, dan dashboard kasir. | `resources/views/transaksis/index.blade.php`, `resources/views/transaksis/show.blade.php`, `resources/views/dashboard/kasir.blade.php` |
| RC-05 | MAJOR | GitHub Actions kembali hijau: `RefreshDatabase` diaktifkan, Vite dinonaktifkan di base `TestCase`, matrix CI disederhanakan ke PHP 8.3, `composer.lock` disinkronkan, dan suite Sprint 3 dibuat deterministik (kunci Midtrans dummy + mock QRIS) serta dijalankan lewat `vendor/bin/phpunit`. | `tests/Feature/ExampleTest.php`, `tests/TestCase.php`, `phpunit.xml`, `phpunit.sprint3.xml`, `tests/Sprint3/Sprint3FunctionalTest.php`, `app/Http/Controllers/Api/TransaksiController.php`, `.github/workflows/tests.yml`, `composer.lock` |

### Cleanup Kode Sumber

- **Tidak ada sisa debugging.** Pemindaian seluruh file yang ter-track (`app/`, `resources/`, `routes/`, `database/`, `tests/`, `config/`, `bootstrap/`) menemukan **0** fungsi `dd()`, `dump()`, `var_dump()`, `print_r()`, `console.log()`, maupun `System.out.println()`.
- Tiga panggilan `console.error()` pada `resources/views/dashboard/kasir.blade.php` (baris 1319, 1445, 1535) **dipertahankan** karena berada di dalam blok `catch` sebagai pencatatan error runtime, bukan sisa debugging.
- **Perbaikan PSR-4:** `app/Http/middleware/` → `app/Http/Middleware/` dan `app/Http/request/uploadPaymentrequest.php` → `app/Http/Requests/UploadPaymentRequest.php`. Kedua kelas sebelumnya tidak bisa di-autoload pada filesystem case-sensitive (tidak masuk classmap Composer) sehingga akan memicu fatal error bila pernah dipakai. Sekarang keduanya dapat di-autoload dan warning PSR-4 hilang dari proses `composer install`.

### Dokumentasi

- `S3-04: ISSUE TRACKING & RESOLVED BUGS LOG/S3-04-ISSUE-TRACKING-RESOLVED-BUGS.md` — dipulihkan (dokumen ini terhapus pada commit `e2755c1`) dan diperbarui: 13 tiket Sprint 3 hasil re-verifikasi + 5 tiket RC baru = **18 tiket, 18 CLOSED, 0 OPEN** (CRITICAL 1, MAJOR 13, MINOR 4).

## Hasil Pengujian

| Suite | Perintah | Hasil |
|---|---|---|
| Feature & Unit | `php artisan test` | **37 passed (117 assertions)** |
| Sprint 3 | `vendor/bin/phpunit --configuration=phpunit.sprint3.xml` | **29 passed (175 assertions)** |
| Regression RC | `php artisan test --filter=ReleaseCandidateTest` | **7 passed (27 assertions)** |

Kedua suite dijalankan pada database `db_perkoci_test` yang **dibuat kosong dari nol**, memakai `.env` hasil salinan `.env.example` + `APP_KEY` baru, dan **tanpa `public/build`** — replikasi persis langkah runner GitHub Actions. Sebelum perbaikan, suite utama gagal karena tabel `mejas` belum tersedia; setelah `RefreshDatabase` diaktifkan, masalah berikutnya adalah `ViteManifestNotFoundException` (job backend tidak membangun aset) yang ditutup dengan `withoutVite()` pada base `TestCase`.

Tujuh test pada `tests/Feature/ReleaseCandidateTest.php` dibuktikan **gagal pada kode sebelum perbaikan** (6 dari 7 test gagal) dan **lulus sesudahnya** — menandakan test tersebut benar-benar menangkap regresi, bukan sekadar assertion yang selalu benar.

### Bukti Manual

| Skenario | Sebelum | Sesudah |
|---|---|---|
| RC-01 stok (mulai 10, pesan 2) | 8 → 10 (batal) → **12** (hapus) | 8 → 10 (batal) → **10** (hapus) |
| RC-02 label status pelanggan | `Menunggu Konfirmasi` | `Dibatalkan` |
| RC-03 pendapatan hari ini | Rp 140.000 (termasuk batal) | **Rp 50.000** |

## Catatan Merge Branch

Prosedur S4-01 menyebut `git merge feature/backend feature/frontend`. Yang dilakukan:

- **`feature/backend` → di-merge** (hasil: *Already up to date*). Branch ini sudah merupakan ancestor dari `main`, tidak ada perubahan yang perlu digabung.
- **`feature/frontend` → TIDAK di-merge.** Alasan:
  1. **Riwayat tidak terkait.** `origin/feature/frontend` tidak memiliki *common ancestor* dengan `main`, sehingga `git merge` akan berhenti dengan pesan `refusing to merge unrelated histories`.
  2. **Isi sudah basi.** Branch tersebut terbukti merupakan *subset* dari `main`: seluruh file yang ada di sana sudah tersedia di `main` dengan versi yang lebih baru. Yang hilang di `feature/frontend` antara lain `app/Policies/TransaksiPolicy.php`, seluruh `resources/views/errors/*`, `resources/views/laporan/pdf.blade.php`, `resources/views/user/status.blade.php`, seluruh migrasi promo, dan dashboard admin versi terbaru.
  3. **Merge paksa akan merusak.** Flag `--allow-unrelated-histories` akan mengembalikan file-file basi tersebut ke `main` dan membatalkan hasil Sprint 3 (anti-IDOR, halaman error, export PDF, promo, dashboard).
  4. Commit unik pada branch tersebut (`e321604`) merupakan *empty commit* tanpa perubahan file.

Rekomendasi: branch `feature/frontend` dihapus dari remote atau di-rebase agar sinkron dengan `main`, bukan di-merge ke `main`.

## Known Limitations

1. **Advisory keamanan dependensi.** `composer audit` melaporkan 10 advisory pada 6 paket transitif (`symfony/http-foundation`, `symfony/http-kernel`, `league/commonmark`, dan lainnya). Rilis ini **sengaja tidak** menjalankan `composer update` penuh (54 paket akan ter-upgrade, termasuk `laravel/framework` 12.69.2 → 12.72.0) agar kandidat rilis tetap stabil dan teruji. Update penuh perlu stage pengujian tersendiri sebelum rilis v1.0.0 final.
2. **`vendor/` dan `node_modules/` masih ter-track** di Git (8.539 + 3.661 file) meskipun sudah ada di `.gitignore` — keduanya ter-commit sebelum aturan `.gitignore` dibuat. Tidak dilepas pada rilis ini karena akan menghasilkan diff resolusi besar.
3. **`app/Http/Controllers/DashboardController.php` adalah kode mati** — seluruh route dashboard memakai closure inline di `routes/web.php`. Controller ini sudah disinkronkan dengan filter `cancelled` (RC-03), namun sebaiknya dihapus atau dipindahkan agar tidak ada dua sumber perhitungan dashboard.
4. **Pesanan berstatus `pending` tanpa pembayaran masih dihitung sebagai pendapatan** pada dashboard dan laporan. Perilaku ini merupakan warisan dari Sprint 3 dan tidak diubah pada rilis ini; Direkomendasikan untuk ditinjau sebelum v1.0.0 final.

## Kriteria Selesai (DoD)

| Kriteria | Status | Bukti |
|---|---|---|
| Seluruh tiket bug berstatus CLOSED | ✅ | 18/18 CLOSED pada `S3-04-ISSUE-TRACKING-RESOLVED-BUGS.md` (13 tiket Sprint 3 + 5 tiket RC) |
| Tidak ada bug CRITICAL/MAJOR tersisa | ✅ | 1 CRITICAL + 13 MAJOR seluruhnya tertutup & terverifikasi lewat regression test |
| Branch `main` bersih | ✅ | `git status` bersih; kedua suite test hijau pada database kosong |
| Git Tag `v1.0.0-rc` terpasang | ✅ | Tag annotated `v1.0.0-rc` pada `main`, sudah di-push ke `origin` |

## Perintah Rilis

```bash
git checkout main
git merge feature/backend            # Already up to date
# feature/frontend: TIDAK di-merge (lihat "Catatan Merge Branch")
composer update --lock               # sinkronkan content-hash composer.lock
git add -A && git commit -m "fix(release): RC-01..RC-05 + restore issue tracker S3-04"
git tag -a v1.0.0-rc -m "Release Candidate 1.0.0 CiwideyStay Final Exam"
git push origin main --tags
```
