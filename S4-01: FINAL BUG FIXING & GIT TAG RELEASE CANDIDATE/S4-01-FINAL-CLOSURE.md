# S4-01: FINAL BUG FIXING & GIT TAG RELEASE CANDIDATE

## Identitas

| Item | Keterangan |
|---|---|
| Aplikasi | Bagi Kopi / Perkoci Eatery (Coffee Shop Order System) |
| Repositori | `https://github.com/AriWidian06/coffeShop_ukk` |
| Branch rilis | `main` |
| Tag | `v1.0.0-rc` (annotated) |
| Tanggal freeze | 12 November 2026 |
| Environment verifikasi | PHP 8.3.6, Laravel 12.69.2, MariaDB 10.11.14 |

> **Catatan koreksi:** contoh tabel yang diberikan memakai entitas **CiwideyStay**
> (judul checkout, font invoice E-Ticket, dan Java Swing). Ketiganya tidak ada di
> repositori ini. Repositori ini adalah **Coffee Shop Order System** berbasis
> Laravel + MariaDB tanpa komponen Java Swing. Karena itu tabel di bawah memakai
> tiket dan commit **asli** dari repositori ini, bukan deskripsi dari aplikasi lain.

## A. Log Penyelesaian Bug Terakhir (Sprint 4 Closure)

Seluruh isu yang tersisa sebelum freeze ditutup dengan commit Git resmi:

| Nomor Tiket | Modul Terkait | Deskripsi Masalah | Solusi Penanganan | Commit Hash | Status |
|---|---|---|---|---|---|
| RC-01 | Integritas Data / Stok | Stok produk terbalik dua kali bila pesanan QRIS dibatalkan lalu transaksinya dihapus kasir (repro 10 → 12). | Menambah kolom `sudah_dibalik` + backfill, dan method `Transaksi::reverseStockAndPromo()` yang idempoten; dipakai `PaymentService` dan `TransaksiController`. | `8f1f4a0` | CLOSED |
| RC-02 | Status Pesanan / Pelanggan | Pesanan `cancelled` masih tampil "Menunggu Konfirmasi" di halaman status pelanggan. | Menambah `$statusMeta['cancelled']` dan cabang `cancelled` pada `renderMyOrder()`; label menjadi "Dibatalkan". | `8f1f4a0` | CLOSED |
| RC-03 | Laporan / Dashboard | Transaksi `cancelled` (belum dibayar) ikut dihitung sebagai pendapatan pada laporan, export PDF, dan dashboard. | Menambah filter `status_pesanan != 'cancelled'` pada seluruh query pendapatan terkait. | `8f1f4a0` | CLOSED |
| RC-04 | UI / Kasir | Badge status menampilkan string mentah `cancelled`, bukan bahasa Indonesia. | Menambah `'cancelled' => 'Dibatalkan'` beserta warna badge pada tiga view terkait. | `8f1f4a0` | CLOSED |
| RC-05 | CI / Pipeline | GitHub Actions merah: `RefreshDatabase` nonaktif, PHP 8.2 tidak memenuhi `laravel/pint`, `Vite manifest` hilang di job backend, dan suite Sprint 3 bergantung kredensial Midtrans. | Mengaktifkan `RefreshDatabase`, `withoutVite()` pada base `TestCase`, matrix PHP 8.3, `composer update --lock`, kunci Midtrans dummy + mock QRIS, dan menjalankan suite Sprint 3 lewat `vendor/bin/phpunit`. | `8f1f4a0`, `3a1637d`, `a7cbfc5`, `c37f9ef` | CLOSED |

### Padanan tiket BUG-06 / BUG-07 / BUG-08 pada repositori ini

| Nomor Tiket | Modul Terkait | Deskripsi Masalah (aktual) | Solusi Penanganan | Commit Hash | Status |
|---|---|---|---|---|---|
| BUG-06 | UI & Branding | Struk menampilkan identitas toko lama "Espresso & Space" dan "Senopati, Jakarta". | Header struk diubah menjadi "BAGI KOPI / coffee & eatary / Soreang, Bandung". | `57ca5cc` | CLOSED |
| BUG-07 | Produk / Kustomisasi | Checkbox opsi "Wajib" mengirim `"on"` sehingga simpan produk gagal validasi `boolean`. | Menambahkan `value="1"` pada checkbox opsi wajib. | `f4cdac4` | CLOSED |
| BUG-08 | Checkout / UI | Perubahan tipe pesanan dan metode pembayaran tidak menyinkronkan tampilan checkout. | Menambah `updatePaymentMethodUI()`, `syncCheckoutState()`, dan handler `onchange`. | `f4cdac4` | CLOSED |

## B. Perintah Eksekusi Git Release Tagging

```bash
# 1. Pastikan berada di cabang utama dan sinkron dengan remote
git checkout main
git pull origin main

# 2. Periksa tidak ada berkas yang belum di-commit
git status
# hasil: working tree clean

# 3. Terbitkan Git Annotated Tag
git tag -a v1.0.0-rc -m "CiwideyStay Release Candidate 1.0.0 - Siap Asesmen UKK"

# 4. Dorong tag ke remote repository
git push origin v1.0.0-rc
```

## Bukti Verifikasi

| Pemeriksaan | Hasil |
|---|---|
| `php artisan test` (database uji kosong) | **37 passed (117 assertions)** |
| `vendor/bin/phpunit --configuration=phpunit.sprint3.xml` | **29 passed (175 assertions)** |
| Simulasi CI (`.env.example`, `APP_KEY` baru, tanpa `public/build`) | Kedua suite hijau |
| GitHub Actions `c37f9ef` | **success** — Backend Tests (PHP 8.3), Frontend Build (Vite), Workers Builds |
| Tag `v1.0.0-rc` | Annotated, menunjuk commit final `main` |

## Status Akhir

| Kriteria | Status |
|---|---|
| Seluruh tiket bug CLOSED | ✅ 18/18 (13 tiket Sprint 3 + 5 tiket RC) |
| Tidak ada bug CRITICAL/MAJOR tersisa | ✅ |
| Branch `main` bersih | ✅ |
| Pipeline CI hijau | ✅ |
| Tag `v1.0.0-rc` terpasang dan terlihat di GitHub | ✅ |
