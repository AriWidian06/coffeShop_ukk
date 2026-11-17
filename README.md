<div align="center">

<img src="public/images/logo.jpeg" alt="Logo BagiKopi Coffee Shop Order System" width="160" />

# ☕ BagiKopi — Coffee Shop Order System

**Sistem Pemesanan, Dashboard & Point of Sale (POS)**<br>
Order mandiri dari meja (QR Code) + terminal kasir + dashboard admin/manager.

[![Release](https://img.shields.io/badge/Release-v1.0.0--rc-0D9488?style=for-the-badge)](https://github.com/AriWidian06/coffeShop_ukk/releases/tag/v1.0.0-rc)
[![Build Status](https://github.com/AriWidian06/coffeShop_ukk/actions/workflows/tests.yml/badge.svg?branch=main&style=for-the-badge)](https://github.com/AriWidian06/coffeShop_ukk/actions/workflows/tests.yml)
[![Security Audit](https://img.shields.io/badge/Security-Protected-3B82F6?style=for-the-badge)](https://github.com/AriWidian06/coffeShop_ukk/security)
[![License](https://img.shields.io/badge/License-MIT-slate?style=for-the-badge)](LICENSE)

> Proyek Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak / PPLG — Tahun Ajaran 2026/2027<br>
> **Tema:** Local Digital Solution &bull; **Mitra:** BagiKopi × Perkoci Eatery, Soreang

🌐 **Demo Online:** [bagikopiordersystem.onrender.com](https://bagikopiordersystem.onrender.com)

</div>

---

## 📌 1. Latar Belakang Masalah & Solusi

Proses pemesanan di kedai kopi masih sering mengandalkan pencatatan manual: pelanggan memanggil barista, pesanan ditulis di kertas, lalu rekap penjualan dihitung ulang di akhir hari. Pola ini rawan menimbulkan **antrean panjang**, **salah komunikasi pesanan**, **status meja yang tidak real-time**, **stok bahan baku tidak terkontrol**, dan **laporan penjualan yang lambat disusun**.

**BagiKopi** hadir sebagai solusi digital terintegrasi yang menyatukan alur pelanggan, kasir, dan manajemen dalam satu sistem:

1. **Self-Order via QR Code Meja:** Pelanggan memindai QR di meja, memilih menu beserta kustomisasinya, menerapkan kode promo, lalu memesan tanpa perlu mengantre di kasir.
2. **POS Terminal & Denah Meja Real-time:** Kasir dapat membuat pesanan manual, memantau status meja, memproses pembayaran tunai/QRIS, dan mencetak struk.
3. **Dashboard Admin & Manager:** Admin mengelola produk, kategori, meja, karyawan, supplier, dan promo; Manager memantau omzet, produk terlaris, serta mengekspor laporan PDF.

| Alur Pengguna | Fitur Utama |
|---|---|
| **Pelanggan** | Scan QR meja, katalog + kustomisasi menu, keranjang & promo, status pesanan real-time (polling 5 detik), pembayaran QRIS. |
| **Kasir** | Terminal POS, denah meja live, alur status pesanan satu klik, pembayaran tunai dengan kembalian, QRIS, cetak struk. |
| **Manager** | Omzet harian/bulanan, rata-rata transaksi, produk terlaris, grafik penjualan per jam, perbandingan periode, ekspor PDF. |
| **Admin** | CRUD master data (produk, kategori, meja, karyawan, supplier, promo), dashboard operasional, riwayat transaksi, foto produk tersimpan di database. |

---

## 🛠️ 2. Tech Stack & Architecture

* **Frontend:** Blade Templating, Tailwind CSS 4, Vite 7, JavaScript (Alpine/vanilla), desain responsif untuk mobile & desktop.
* **Backend:** PHP 8.2+ dengan Laravel 12 (MVC), Service Layer untuk pembayaran, Policy & Gate untuk otorisasi.
* **Database:** MySQL 8 / MariaDB 10.11, 22 migrasi, relasi transaksi ternormalisasi, dan penyimpanan binary gambar produk sebagai fallback deployment.
* **Payment Gateway:** Midtrans Snap (QRIS) dengan verifikasi **signature hash** pada webhook.
* **Reporting:** Dompdf 3.x untuk ekspor laporan penjualan PDF.
* **Security:** Bcrypt password hashing, CSRF protection, rate limiting login, Role Middleware (RBAC), `TransaksiPolicy` anti-IDOR, dan validasi input server-side.
* **Testing & CI:** PHPUnit 11, Laravel Testing (`RefreshDatabase`), GitHub Actions untuk suite utama dan suite Sprint 3.
* **Deployment:** Docker (Apache + Node build stage) dan Render.

### Arsitektur Singkat

```text
┌──────────────────┐      ┌────────────────────────┐      ┌─────────────────────┐
│  Pelanggan / QR  │─────▶│  Laravel 12 (PHP 8.2)  │─────▶│  MySQL / MariaDB    │
│  Kasir / Admin   │◀─────│  Blade + Tailwind 4    │◀─────│  data transaksi     │
└──────────────────┘      └───────────┬────────────┘      └─────────────────────┘
                                      │
                          ┌───────────┴────────────┐
                          │ Midtrans Snap (QRIS)   │
                          │ Webhook + signature    │
                          └────────────────────────┘
```

> 📘 **Dokumentasi API:** Spesifikasi OpenAPI 3.0 dan Postman Collection tersedia di [`docs/04-api/`](docs/04-api/README.md).

---

## 🚀 3. Panduan Instalasi Lokal (Quick Local Setup)

Ikuti langkah berikut untuk menjalankan aplikasi di lingkungan pengembang lokal.

### Prasyarat Sistem

* PHP **≥ 8.2** untuk runtime produksi; **PHP 8.3** disarankan untuk pengembangan karena `laravel/pint` membutuhkan PHP `^8.3`
  (ekstensi: `pdo_mysql`, `mbstring`, `bcmath`, `gd`, `dom`, `curl`, `zip`)
* Composer 2
* Node.js 18+ & npm
* MySQL 8 atau MariaDB 10.11 (Laragon / XAMPP)
* Git CLI

> Jika lingkungan pengembangan hanya tersedia PHP 8.2, gunakan `composer install --no-dev` untuk menjalankan aplikasi tanpa paket pengembangan (Pint/PHPUnit).

### Langkah Instalasi

```bash
# 1. Klon repositori ini
git clone https://github.com/AriWidian06/coffeShop_ukk.git
cd coffeShop_ukk

# 2. Pasang dependensi PHP & JavaScript
composer install
npm install

# 3. Salin berkas environment dan generate application key
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi basis data pada berkas .env
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=db_perkoci
#    DB_USERNAME=root
#    DB_PASSWORD=
#    ADMIN_PASSWORD=admin123

# 5. Jalankan migrasi dan data awal
php artisan migrate --seed

# 6. (Opsional) Muat data demo lengkap: 30 menu kafe, meja, supplier, promo
php artisan db:seed --class=DemoDataSeeder

# 7. Hubungkan storage symlink untuk upload gambar produk & struk
php artisan storage:link

# 8. Build aset frontend
npm run build

# 9. Jalankan server lokal
php artisan serve
```

Akses aplikasi melalui browser pada tautan: **<http://127.0.0.1:8000>**.

> **Shortcut:** `composer setup` menjalankan instalasi dependensi, `.env`, key, migrasi, dan build aset secara otomatis.
> Untuk mode pengembangan gunakan `composer dev` (server, queue, log viewer, dan Vite berjalan sekaligus).

### Menjalankan Pengujian

```bash
# Buat database uji terpisah (wajib — RefreshDatabase akan mengosongkan database)
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS db_perkoci_test;"

# Suite utama (Feature + Unit)
php artisan test

# Suite Sprint 3
vendor/bin/phpunit --configuration=phpunit.sprint3.xml
```

> Kedua suite memakai database uji `db_perkoci_test`, bukan `db_perkoci`, sehingga data utama tetap aman. Suite utama mencakup alur order, promo, QRIS, RBAC, keamanan, dan regression test Release Candidate. Suite Sprint 3 mencakup FT-01…FT-15 serta NT-01…NT-14 (skenario negatif, IDOR, dan injection).

---

## 🔑 4. Akun Pengujian Default (Credentials)

Gunakan kredensial berikut untuk menguji hak akses multi-role. Aplikasi ini menggunakan **username**, bukan email.

**Contoh kredensial default:** `admin` / `admin123` (set `ADMIN_PASSWORD=admin123` pada `.env` sebelum seeding).

| Role Pengguna | Username | Password | Hak Akses Utama |
|---|---|---|---|
| **Admin** | `admin` | Nilai `ADMIN_PASSWORD` di `.env` | Seluruh modul: master data, transaksi, laporan, pengaturan karyawan. |
| **Manager** | Dibuat oleh Admin | Ditentukan saat membuat akun | Dashboard omzet, laporan, ekspor PDF, dan riwayat transaksi. |
| **Kasir** | Dibuat oleh Admin | Ditentukan saat membuat akun | Terminal POS, denah meja, pembayaran, cetak struk, dan transaksi. |
| **Pelanggan** | Tanpa login | — | Scan QR meja, pesan menu, pantau status, dan bayar QRIS. |

> **Cara mendapatkan password admin:**
> 1. Isi `ADMIN_PASSWORD=admin123` pada `.env` **sebelum** menjalankan `php artisan migrate --seed`.
> 2. Jika `ADMIN_PASSWORD` kosong, `DatabaseSeeder` membuat password acak yang ditampilkan **sekali** pada log instalasi.
> 3. `php artisan db:seed --class=DemoDataSeeder` juga membuat akun `admin` dengan password `admin123`, tetapi hanya jika akun admin belum ada.
>
> **Cara membuat akun Manager/Kasir:** login sebagai Admin → menu **Karyawan** → tambah karyawan → pilih role `manager` atau `kasir`.
> **Ganti password default sebelum dipakai di lingkungan produksi.**
>
> **Katalog menu:** `CafeMenuSeeder` memasang 30 menu kafe beserta gambarnya dan dijalankan otomatis melalui `DatabaseSeeder`. Gambar katalog tersimpan di `public/images/menu/` sehingga ikut ter-deploy tanpa bergantung pada storage symlink. Menu lama yang sudah dipakai transaksi akan dinonaktifkan (tidak dihapus) agar riwayat pesanan tetap utuh; menu lama tanpa riwayat transaksi akan dihapus. Gambar unggahan admin tidak ditimpa saat seeder dijalankan ulang.

### Konfigurasi Pembayaran QRIS (Midtrans)

Isi bagian berikut pada `.env`:

```dotenv
MIDTRANS_MERCHANT_ID=M698699555
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

Alur pembayaran: pelanggan/kasir memilih QRIS → `PaymentService` membuat Snap token → pembayaran diproses di modal Midtrans → status dicek melalui API → notifikasi masuk ke `POST /midtrans/notification` dan diverifikasi signature sebelum status transaksi diubah.

---

## 👥 5. Anggota Tim Pengembang

| Nama | Peran | NISN |
|---|---|---|
| **Putra Ari Widian** | Backend Architect & Database Administrator | `[0018505224/242510318]` |
| **Muh.Rapa aditya** | Frontend Specialist & UI/UX Designer | `[0084566332/25261101]` |

**Pembimbing UKK:** `[Nurdiansyah Permana,S.T.]`<br>
**SMK `[Budi Bakti Ciwidey]`** — 2026



---

<div align="center">

Dibangun dengan Kopi dan Laravel untuk UKK Rekayasa Perangkat Lunak / PPLG.

© 2026 **Spark Industri** — Pengembang Aplikasi..

</div>
