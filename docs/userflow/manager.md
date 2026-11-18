# User Flow Manager — BagiKopi

| Item | Keterangan |
|---|---|
| **Peran** | Manager |
| **Autentikasi** | Login staf dengan guard `karyawan` |
| **Titik masuk** | `GET /login` → `GET /dashboard` |
| **Layout** | `layouts.manager` |
| **Dokumen terkait** | [`README.md`](./README.md), [`../PRD.md`](../PRD.md), [`../DESIGN.md`](../DESIGN.md) |
| **Diagram** | draw.io (1 file, 1 halaman, tanpa SVG) |

---

## 1. Ringkasan

Manager memakai BagiKopi untuk membaca performa penjualan, bukan untuk mengubah data operasional. Dashboard manager menyajikan ringkasan hari ini, statistik periode yang dapat difilter, grafik per jam, distribusi pembayaran, dan top 5 produk. Manager juga dapat membuka daftar seluruh transaksi dan mengekspor laporan PDF sesuai periode.

---

## 2. Prasyarat dan Batasan

- Akun harus memiliki role `manager` dan berhasil login.
- Manager memakai layout `layouts.manager`, bukan layout admin atau kasir.
- Manager dapat melihat seluruh transaksi melalui policy, tetapi **tidak dapat**:
  - membuat pesanan POS;
  - mengubah status pesanan;
  - mencatat pembayaran;
  - mencetak struk dari halaman detail (tombol tidak dirender untuk manager);
  - mengelola master data.
- Semua perhitungan laporan memakai zona waktu `Asia/Jakarta` (WIB).
- Transaksi berstatus `cancelled` tidak dihitung sebagai pendapatan, transaksi, item, maupun omzet produk.

---

## 3. Alur Utama

### 3.1 Login

1. Manager membuka `/login`.
2. Bila browser masih menyimpan sesi QR pelanggan, halaman login menampilkan 403. Manager membuka `/login?staff=1` untuk membersihkan sesi, lalu login.
3. Setelah kredensial valid dan role `manager`, manager diarahkan ke `/dashboard`.
4. Server memilih tampilan `dashboard.manager` berdasarkan role.

### 3.2 Memilih periode laporan

1. Dashboard default memakai periode **Bulanan** dan tanggal hari ini.
2. Manager memilih salah satu periode:
   - **Harian** — satu hari kalender dari `00:00` sampai `23:59:59` WIB.
   - **Mingguan** — Senin sampai Minggu yang memuat tanggal terpilih.
   - **Bulanan** — tanggal 1 sampai akhir bulan yang memuat tanggal terpilih.
3. Manager dapat:
   - memilih tanggal lewat input date lalu menekan **Terapkan**;
   - menekan panah kiri/kanan untuk periode sebelumnya/berikutnya;
   - menekan **Hari Ini** untuk kembali ke tanggal sekarang dengan periode yang sama.
4. Parameter disimpan di query string: `/dashboard?periode=bulanan&tanggal=2026-11-18`.
5. Periode tidak valid jatuh kembali ke `bulanan`; tanggal tidak valid jatuh kembali ke tanggal sekarang.

### 3.3 Membaca snapshot hari ini

Bagian atas dashboard selalu menampilkan kondisi **hari ini**, terlepas dari periode yang dipilih:

- total pendapatan hari ini;
- jumlah transaksi hari ini;
- jumlah item terjual hari ini;
- shift aktif berdasarkan jam WIB:
  - Pagi `06:00–14:00`;
  - Siang/Sore `14:00–22:00`;
  - Malam `22:00–06:00`.

### 3.4 Membaca statistik periode

Kartu statistik periode menampilkan:

1. **Total Pendapatan** — omzet periode terpilih, rata-rata pendapatan per hari, dan pertumbuhan dibanding periode sebelumnya.
2. **Jumlah Transaksi** — total tiket, total item, rata-rata transaksi per hari, dan jam puncak.
3. **Nilai Rata-rata Transaksi (ATV)** — pendapatan periode dibagi jumlah transaksi, serta rata-rata item per transaksi.
4. **Pertumbuhan** — bila periode sebelumnya tidak memiliki data, badge menampilkan “Belum ada pembanding”.
5. **Top 5 Produk Terlaris** — berdasarkan kuantitas terjual, lengkap dengan pendapatan dan kategori.
6. **Metode Pembayaran** — distribusi jumlah transaksi dan nominal per metode (`cash`, `QRIS`, dan metode lain yang tercatat).
7. **Grafik per Jam** — omzet atau volume transaksi per jam WIB. Manager dapat berganti antara **Nilai (Rp)** dan **Volume**; grafik diperbarui otomatis setiap 30 detik.

### 3.5 Menelusuri transaksi

1. Manager menekan **Lihat Transaksi** pada panel top produk atau menu **Laporan** di sidebar.
2. Halaman `/transaksis` menampilkan seluruh transaksi, bukan hanya milik satu kasir.
3. Filter yang tersedia:
   - pencarian berdasarkan ID transaksi atau nomor meja;
   - status pesanan.
4. Klik transaksi untuk membuka detail read-only:
   - item, subtotal, pajak, total;
   - status pesanan;
   - data pembayaran;
   - meja dan kasir.
5. Manager tidak melihat tombol ubah status, bayar, atau cetak struk karena `$canProcess` hanya untuk kasir dan admin.

### 3.6 Ekspor laporan PDF

1. Manager menekan **Export PDF** di header dashboard atau **Ekspor Laporan (PDF)** di sidebar.
2. Browser membuka:

   ```text
   GET /laporan/export?periode={harian|mingguan|bulanan}&tanggal={YYYY-MM-DD}
   ```

3. Bila parameter tidak lengkap, laporan memakai bulan berjalan.
4. Server menghitung ulang data untuk periode tersebut, lalu mengembalikan PDF A4 landscape dengan nama file:

   ```text
   Laporan_BagiKopi_{Periode}_{tanggal}.pdf
   ```

5. Isi PDF:
   - identitas laporan dan periode;
   - ringkasan pendapatan, transaksi, diskon, item, dan ATV;
   - grafik omzet per jam;
   - distribusi metode pembayaran;
   - top produk;
   - daftar transaksi non-cancelled;
   - nama manager yang mencetak.
6. Tidak ada antrean background; PDF dibuat saat request dan langsung diunduh.

### 3.7 Logout

Manager menekan **Keluar / Log Out** di sidebar. Sesi dihapus dan manager diarahkan ke `/login`.

---

## 4. Flowchart

Buka [`manager.drawio`](./diagrams/manager.drawio) untuk flowchart satu halaman yang mencakup login, pemilihan periode, statistik, penelusuran transaksi, dan ekspor PDF.

---

## 5. Layar dan Endpoint

| Aksi | Layar | Endpoint | Method |
|---|---|---|---|
| Login | Form login | `/login` | `GET`, `POST` |
| Dashboard periode | Laporan penjualan | `/dashboard?periode=&tanggal=` | `GET` |
| Data grafik manager | Dashboard | `/dashboard/manager-sales-chart?periode=&tanggal=` | `GET` |
| Daftar transaksi | Laporan transaksi | `/transaksis` | `GET` |
| Detail transaksi | Detail read-only | `/transaksis/{id}` | `GET` |
| Ekspor PDF | Laporan | `/laporan/export?periode=&tanggal=` | `GET` |

---

## 6. Aturan Bisnis

1. **Periode mingguan selalu Senin–Minggu.** Tidak mengikuti awal minggu locale.
2. **Periode bulanan memakai bulan kalender.** Tanggal terpilih hanya menentukan bulan.
3. **Snapshot hari ini tidak terpengaruh filter periode.** Angka snapshot selalu hari ini.
4. **Pertumbuhan dibanding periode sebelumnya.** Harian membandingkan hari sebelumnya; mingguan membandingkan minggu sebelumnya; bulanan membandingkan bulan sebelumnya.
5. **Transaksi `cancelled` dikecualikan** dari semua agregat dan daftar laporan.
6. **Pembayaran dihitung dari tabel pembayaran.** Transaksi tanpa pembayaran tidak masuk distribusi metode pembayaran.
7. **Top produk berdasarkan kuantitas (`QTY`), bukan pendapatan.**
8. **Grafik memakai jam WIB.** Transaksi larut malam tetap masuk jam yang benar.
9. **Ekspor memakai periode yang sama dengan dashboard.** Sidebar meneruskan query `periode` dan `tanggal` yang sedang aktif.
10. **Manager bersifat read-only.** Tidak ada aksi tulis pada transaksi atau master data.
11. **Pendapatan dan item dihitung dari semua transaksi non-cancelled.** Pesanan `pending`, `prosesed`, dan `ready` yang belum dibayar tetap masuk agregat; angka ini bukan kas yang sudah diterima.

---

## 7. Edge Cases

| Situasi | Perilaku sistem |
|---|---|
| Parameter `periode` tidak valid | Fallback ke `bulanan`. |
| Parameter `tanggal` tidak valid | Fallback ke tanggal hari ini. |
| Tidak ada transaksi pada periode | Kartu menampilkan nol; grafik dan top produk menampilkan empty state. |
| Periode sebelumnya tidak punya data | Badge pertumbuhan menampilkan “Belum ada pembanding”. |
| Semua transaksi dibatalkan | Tidak dihitung sebagai pendapatan, transaksi, item, maupun omzet produk. |
| Manager membuka `/transaksis/create` | Ditolak gate `access-pos` (403). |
| Manager membuka CRUD admin | Ditolak gate `access-admin-panel` (403). |
| Manager membuka detail transaksi | Boleh, tetapi tanpa aksi status/pembayaran/struk. |
| Filter status **Dibatalkan** di daftar transaksi | Opsi tampil di UI, tetapi validasi controller web belum menerima `cancelled`; filter dapat menghasilkan error validasi. |
| Ekspor tanpa parameter | Memakai bulan berjalan. |
| Ekspor periode besar | Dibuat sinkron saat request; waktu tunggu bertambah sesuai jumlah transaksi. |
| PDF gagal dibuat | Request error; tidak ada fallback CSV atau file cache. |
| Transaksi tepat di batas hari WIB | Dihitung berdasarkan konversi UTC yang benar dari awal/akhir hari WIB. |

---

## 8. Catatan Implementasi

- Dashboard manager dihitung di closure `routes/web.php` dan memakai `DashboardSalesChart::forRange`.
- Endpoint grafik manager: `/dashboard/manager-sales-chart`, dilindungi gate `view-sales-report` (manager dan admin).
- Grafik diperbarui otomatis setiap 30 detik melalui JavaScript di `dashboard/manager.blade.php`.
- Ekspor PDF: `App\Http\Controllers\LaporanController@export`, memakai Dompdf dan view `laporan.pdf`.
- Nama file dan periode ditentukan dari role manager dan query `periode`/`tanggal`.
- Sidebar manager hanya memuat **Dashboard**, **Laporan**, dan **Ekspor Laporan (PDF)**; tidak ada menu POS atau master data.
- Manager dapat melihat semua transaksi melalui `TransaksiPolicy`, tetapi tampilan detail menyembunyikan aksi tulis karena `$canProcess` hanya untuk kasir dan admin.
