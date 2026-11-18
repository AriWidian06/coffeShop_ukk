# User Flow Admin — BagiKopi

| Item | Keterangan |
|---|---|
| **Peran** | Admin |
| **Autentikasi** | Login staf dengan guard `karyawan` |
| **Titik masuk** | `GET /login` → `GET /dashboard` |
| **Layout** | `layouts.app` |
| **Dokumen terkait** | [`README.md`](./README.md), [`../PRD.md`](../PRD.md), [`../DESIGN.md`](../DESIGN.md) |
| **Diagram** | draw.io (1 file, 1 halaman, tanpa SVG) |

---

## 1. Ringkasan

Admin memegang kendali operasional dan data master: menyiapkan menu, kategori, supplier, meja beserta QR, promo, dan akun karyawan. Admin juga dapat memantau meja secara live, membuat pesanan POS, melihat detail transaksi, mencatat pembayaran, dan mengekspor laporan PDF. Semua halaman CRUD admin dilindungi gate `access-admin-panel` yang hanya mengizinkan role `admin`.

---

## 2. Prasyarat dan Batasan

- Akun harus memiliki role `admin` dan berhasil login.
- CRUD master data hanya untuk admin. Kasir dan manager akan ditolak bila membuka route admin.
- Admin dapat mengakses POS (`access-pos`) dan transaksi (`view-transactions`).
- Manager memakai layout dan dashboard terpisah; admin tidak memakai halaman manager.
- Beberapa aksi hapus memiliki efek berantai ke data lain (lihat §7).
- Tidak ada tombol hapus transaksi di UI admin saat ini. Route `DELETE /transaksis/{id}` tersedia untuk kebutuhan internal.

---

## 3. Alur Utama

### 3.1 Login

1. Admin membuka `/login`.
2. Bila browser masih memiliki sesi QR pelanggan, halaman login menampilkan 403. Admin membuka `/login?staff=1` untuk membersihkan sesi pelanggan, lalu login.
3. Server memeriksa kredensial pada guard `karyawan`.
4. Role harus `admin`, `kasir`, atau `manager`. Role di luar itu ditolak.
5. Setelah berhasil, admin diarahkan ke `/dashboard`.

### 3.2 Dashboard operasional

Dashboard admin menampilkan:

1. **Total Penjualan Hari Ini** — omzet transaksi hari ini.
2. **Total Pesanan Selesai** — jumlah tiket `completed` hari ini.
3. **Status Meja Terisi** — jumlah meja yang punya transaksi aktif dibanding total meja, beserta persentase okupansi.
4. **Peringatan Inventori** — jumlah produk dengan stok di bawah 10 (label UI: “Bahan Baku” dan “Perlu Reorder”).
5. **Pantauan Meja Live** — kartu semua meja aktif:
   - meja kosong → klik membuka form pesanan baru dengan meja terpilih;
   - meja terisi → klik membuka detail transaksi aktif terbaru.
6. **Penjualan Real-time per Jam** — grafik omzet/volume hari ini, dapat diganti antara **Nilai (Rp)** dan **Volume**, diperbarui otomatis setiap 30 detik.
7. **Daftar Pesanan Terkini** — pesanan terbaru beserta status dan tautan detail.
8. **Karyawan Bertugas** — jumlah pesanan selesai dan omzet per karyawan hari ini.

Dari dashboard, admin memilih salah satu pekerjaan: master data, meja & QR, operasional POS/transaksi, atau ekspor laporan.

### 3.3 Mengelola master data

Pola umum setiap modul:

1. Buka menu di sidebar.
2. Gunakan pencarian/filter pada halaman index.
3. Klik **Tambah** untuk membuat data baru, atau **Edit** pada baris data.
4. Isi form; server memvalidasi field dan aturan unik.
5. Simpan; server mengarahkan kembali ke index dengan pesan sukses.
6. Untuk menghapus, klik aksi hapus dan konfirmasi. Server menghapus data dan mengembalikan ke index.

#### Produk

- Menu **Produk** (`/produks`).
- Filter: pencarian nama, kategori, tipe (`jual`/`bahan baku`), dan status stok (`habis`, `menipis`, `tersedia`).
- Statistik atas: menu siap jual, stok tipis (<10), stok habis.
- Field utama: supplier, kategori, nama, deskripsi, gambar (JPEG/PNG/JPG maks. 2 MB), harga jual, harga beli, tipe, satuan, stok, status aktif, dan opsi kustomisasi.
- Opsi kustomisasi memiliki kategori, tipe pilihan (`single`/`multiple`), wajib/tidak, nama opsi, dan harga tambahan.
- Produk nonaktif tidak tampil di katalog pelanggan maupun POS.
- Gambar disimpan di storage publik dan disalin ke database; bila file hilang, endpoint `/produk/{id}/gambar` menyajikan salinan dari database.

#### Kategori

- Menu **Kategori** (`/kategori-produks`).
- Field: nama kategori, harus unik.
- Halaman index menampilkan jumlah produk per kategori.
- Menghapus kategori juga menghapus produk di dalamnya karena foreign key `cascade`.

#### Meja & QR

- Menu **Meja & QR** (`/mejas`).
- Filter area: lantai 1 (nomor ≤ 10) dan lantai 2 (nomor > 10).
- Statistik: total meja, meja aktif, meja nonaktif, total kapasitas.
- Field: nomor meja (unik), kapasitas, status aktif.
- Setiap kartu meja menampilkan QR dengan URL `/?meja_id={id}`, token, dan tombol salin URL.
- QR dibuat melalui layanan eksternal `api.qrserver.com`.
- Meja nonaktif tidak muncul di pilihan pelanggan dan POS.
- Menghapus meja juga menghapus transaksi yang terhubung karena foreign key `cascade`.

#### Supplier

- Menu **Supplier** (`/suppliers`).
- Field: nama supplier, alamat, nomor telepon.
- Supplier dipakai sebagai relasi wajib saat membuat produk.
- Menghapus supplier juga menghapus produk yang memakainya.

#### Promo Diskon

- Menu **Promo Diskon** (`/promo-kodes`).
- Field: kode (unik, otomatis uppercase), nama promo, nilai diskon persen (1–100), minimal belanja, maksimal diskon, tanggal mulai, tanggal berakhir, kuota, dan status aktif.
- Tanggal diisi dalam WIB, disimpan sebagai UTC.
- Kode promo dipakai pelanggan di checkout web, bukan di POS.
- Menghapus promo juga menghapus riwayat pemakaian promo (`promo_usages`).

#### Karyawan

- Menu **Karyawan** (`/karyawans`).
- Field: nama, nomor telepon, alamat, role (`admin`/`kasir`/`manager`), username (unik), dan password.
- Password minimal 6 karakter; saat edit, kosongkan password bila tidak ingin mengubah.
- Jabatan diisi otomatis mengikuti role.
- Menghapus karyawan juga menghapus transaksi yang tercatat atas namanya karena foreign key `cascade`.

### 3.4 Meja dan QR

1. Admin menambah meja baru dengan nomor unik, kapasitas, dan status aktif.
2. Halaman index menampilkan QR siap pakai untuk setiap meja.
3. Admin menyalin URL atau mengunduh gambar QR untuk ditempel di meja.
4. Pelanggan yang memindai QR akan masuk ke halaman utama dengan meja terkunci.
5. Bila meja dinonaktifkan, QR lama tidak lagi menampilkan meja tersebut sebagai pilihan aktif.
6. Bila meja dihapus, riwayat transaksi meja tersebut ikut terhapus.

> Catatan UI: tombol **Unduh QR**, **Unduh Semua QR (ZIP/PDF)**, **Pratinjau Mockup Akrilik**, dan **Unduh Kit Cetak** belum memiliki handler di tampilan saat ini. Yang berfungsi adalah tampilan QR, tautan URL, dan tombol salin URL.

### 3.5 Operasional POS dan transaksi

1. Dari dashboard, admin mengklik meja kosong untuk membuka `/transaksis/create?meja_id={id}`.
2. Admin memilih produk, jumlah, opsi, tipe pesanan, meja, catatan, dan metode pembayaran (tunai/QRIS).
3. Menyimpan pesanan menghasilkan transaksi `pending`, mengurangi stok, dan menghitung pajak 10%.
4. Untuk QRIS, admin diarahkan ke halaman pembayaran QRIS. Untuk tunai, pesanan masuk antrean kasir.
5. Admin dapat membuka `/transaksis` untuk melihat seluruh transaksi, mencari berdasarkan ID/nomor meja, dan memfilter status.
6. Halaman detail `/transaksis/{id}` menampilkan item, total, status, dan pembayaran.
7. Admin dapat mengubah status ke `pending`, `prosesed`, `ready`, atau `completed`.
8. Admin dapat mencatat pembayaran tunai/QRIS. Pembayaran langsung menandai transaksi `completed`.
9. Struk dapat dicetak dari halaman detail.

### 3.6 Ekspor laporan

1. Admin menekan **Export Laporan** di dashboard.
2. Browser membuka `GET /laporan/export`.
3. Tanpa parameter, laporan memakai rentang bulan berjalan (WIB).
4. Route juga menerima `start_date` dan `end_date` (format `YYYY-MM-DD`). Bila tanggal akhir lebih awal dari tanggal mulai, keduanya ditukar.
5. Laporan PDF memuat ringkasan pendapatan, jumlah transaksi, diskon, item, ATV, distribusi pembayaran, top 5 produk, dan daftar transaksi.
6. Transaksi `cancelled` tidak dihitung dan tidak dimasukkan ke laporan.

### 3.7 Logout

Admin menekan **Keluar / Log Out** di sidebar. Sesi dihapus dan diarahkan ke `/login`.

---

## 4. Flowchart

Buka [`admin.drawio`](./diagrams/admin.drawio) untuk flowchart satu halaman yang mencakup login, dashboard, master data, meja & QR, POS/transaksi, dan ekspor laporan.

---

## 5. Layar dan Endpoint

| Aksi | Layar | Endpoint | Method |
|---|---|---|---|
| Login | Form login | `/login` | `GET`, `POST` |
| Dashboard | Ringkasan operasional | `/dashboard` | `GET` |
| Grafik penjualan | Dashboard | `/dashboard/sales-chart` | `GET` |
| Produk | Index/create/edit | `/produks` | `GET`, `POST`, `PUT/PATCH`, `DELETE` |
| Kategori | Index/create/edit | `/kategori-produks` | `GET`, `POST`, `PUT/PATCH`, `DELETE` |
| Meja & QR | Index/create/edit | `/mejas` | `GET`, `POST`, `PUT/PATCH`, `DELETE` |
| Supplier | Index/create/edit | `/suppliers` | `GET`, `POST`, `PUT/PATCH`, `DELETE` |
| Promo | Index/create/edit | `/promo-kodes` | `GET`, `POST`, `PUT/PATCH`, `DELETE` |
| Karyawan | Index/create/edit | `/karyawans` | `GET`, `POST`, `PUT/PATCH`, `DELETE` |
| POS | Form pesanan | `/transaksis/create` | `GET` |
| Simpan pesanan | POS | `/transaksis` | `POST` |
| Daftar transaksi | Riwayat | `/transaksis` | `GET` |
| Detail transaksi | Detail | `/transaksis/{id}` | `GET` |
| Ubah status | Detail | `/transaksis/{id}/status` | `PATCH` |
| Catat pembayaran | Detail | `/pembayarans` | `POST` |
| Cetak struk | Struk | `/transaksis/{id}/receipt` | `GET` |
| Ekspor laporan | Laporan | `/laporan/export` | `GET` |

---

## 6. Aturan Bisnis

1. **Role admin bersifat eksklusif untuk CRUD.** Gate `access-admin-panel` hanya meloloskan role `admin`.
2. **Semua field unik divalidasi server.** Nomor meja, username, kode promo, dan nama kategori tidak boleh duplikat.
3. **Ambang stok berbeda antarhalaman.** Di halaman Produk, stok tipis = `0 < stok < 10` dan stok habis = `0`; kartu Peringatan Inventori di dashboard memakai ambang `stok < 10` untuk semua produk.
4. **Produk nonaktif tidak dijual.** Katalog pelanggan dan POS hanya menampilkan `tipe = jual`, `status_aktif = true`.
5. **Meja terisi dihitung dari transaksi aktif.** Tidak ada kolom status okupansi manual.
6. **Satu meja satu pesanan aktif.** POS dan checkout web sama-sama menolak meja yang sudah terpakai.
7. **Pembayaran langsung menyelesaikan transaksi.** Tidak ada langkah verifikasi tambahan setelah pembayaran dicatat.
8. **Pembayaran ganda dan kurang bayar ditolak.**
9. **QRIS memakai Midtrans Snap.** Status lunas masuk lewat polling, cek manual, atau webhook.
10. **Laporan mengabaikan transaksi `cancelled`.**
11. **Ekspor PDF memakai WIB.** Batas hari/bulan dikonversi ke UTC saat query.
12. **Total Penjualan Hari Ini menghitung semua transaksi non-cancelled.** Angka ini mencakup pesanan yang belum dibayar, jadi bukan jumlah uang yang sudah diterima kasir. **Total Pesanan Selesai** tetap hanya menghitung status `completed`.

---

## 7. Edge Cases dan Risiko Operasional

| Situasi | Perilaku sistem |
|---|---|
| Non-admin membuka route CRUD | Ditolak gate `access-admin-panel` (403). |
| Browser admin masih menyimpan sesi QR pelanggan | `/login` menampilkan 403; gunakan `/login?staff=1`. |
| Menghapus produk | Detail transaksi lama yang memakai produk tersebut ikut terhapus karena `ON DELETE CASCADE`. Untuk menjaga riwayat, nonaktifkan produk daripada menghapusnya. |
| Menghapus kategori atau supplier | Produk yang terkait ikut terhapus karena cascade. |
| Menghapus meja | Transaksi yang terhubung ke meja tersebut ikut terhapus karena cascade. |
| Menghapus karyawan | Transaksi atas nama karyawan tersebut ikut terhapus karena cascade. |
| Menghapus promo | Riwayat `promo_usages` untuk promo tersebut ikut dihapus. |
| Meja dihapus saat masih ada pesanan aktif | Pesanan dan riwayatnya hilang; pastikan meja kosong sebelum menghapus. |
| Nomor meja / username / kode promo duplikat | Validasi server menolak dan menampilkan error pada form. |
| Gambar produk hilang dari storage | Endpoint gambar menyajikan salinan base64 dari database bila tersedia; jika tidak, 404. |
| QR meja gagal dimuat | Gambar berasal dari layanan eksternal; URL dan tombol salin tetap dapat dipakai. |
| Filter status **Dibatalkan** di daftar transaksi | UI menampilkan opsi tersebut, tetapi validasi controller web belum menerima `cancelled`; filter ini dapat menghasilkan error validasi. |
| Mengubah status langsung ke `completed` | Diizinkan; sistem melepas promo aktif melalui endpoint status. |
| Pembayaran dicatat saat status `pending` | Transaksi langsung menjadi `completed`. |
| Pembayaran kedua | Ditolak karena transaksi sudah memiliki pembayaran. |
| Ekspor tanpa parameter | Memakai bulan berjalan. |
| Rentang tanggal terbalik | Server menukar tanggal mulai dan akhir. |

---

## 8. Catatan Implementasi

- Login dan penolakan role: `App\Http\Controllers\AuthController`.
- Dashboard admin dihitung langsung di closure `routes/web.php` dan memakai `DashboardSalesChart` untuk grafik per jam.
- CRUD memakai resource controller: `ProdukController`, `KategoriProdukController`, `MejaController`, `SupplierController`, `PromoKodeController`, `KaryawanController`.
- POS dan transaksi memakai `TransaksiController`; pembayaran memakai `PembayaranController`.
- Ekspor PDF memakai `LaporanController` dan Dompdf.
- Gate didefinisikan di `App\Providers\AppServiceProvider`:
  - `access-admin-panel` → admin;
  - `access-pos` → kasir, admin;
  - `view-transactions` → kasir, manager, admin;
  - `view-sales-report` → manager, admin.
- `TransaksiPolicy` memberi admin dan manager akses ke semua transaksi; kasir hanya miliknya atau pesanan web.
- Route `/mejas` (index) dapat dibuka semua staf yang login, tetapi tombol tambah/edit/hapus hanya dirender untuk admin.
