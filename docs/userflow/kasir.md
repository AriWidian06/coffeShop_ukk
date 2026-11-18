# User Flow Kasir — BagiKopi

| Item | Keterangan |
|---|---|
| **Peran** | Kasir |
| **Autentikasi** | Login staf dengan guard `karyawan` |
| **Titik masuk** | `GET /login` → `GET /dashboard` |
| **Layout** | `layouts.kasir` |
| **Dokumen terkait** | [`README.md`](./README.md), [`../PRD.md`](../PRD.md), [`../DESIGN.md`](../DESIGN.md) |
| **Diagram** | draw.io (1 file, 1 halaman, tanpa SVG) |

---

## 1. Ringkasan

Kasir mengelola pesanan yang masuk dari pelanggan web maupun pesanan yang dibuat langsung di POS. Pekerjaan utama kasir ada di dashboard dengan tiga tab: **Kasir & Menu**, **Denah Meja**, dan **Pesanan Aktif**. Dari sana kasir membuat pesanan, memproses status, mencatat pembayaran, membuka QRIS Midtrans, dan mencetak struk.

---

## 2. Prasyarat dan Batasan

- Akun harus memiliki role `kasir` dan berhasil login melalui `AuthController`.
- Halaman POS, status, pembayaran, dan struk memerlukan izin `access-pos` (kasir dan admin).
- Kasir hanya dapat melihat dan mengubah transaksi yang:
  - memiliki `karyawan_id` miliknya, atau
  - berasal dari pemesanan web (`sumber_pesanan = web`).
- Denah meja dan daftar pesanan aktif menampilkan seluruh antrean aktif, tetapi aksi pada pesanan kasir lain dapat ditolak oleh policy.
- Pajak & layanan 10% dihitung dari subtotal dan ditampilkan di keranjang POS.
- POS dashboard menyimpan pesanan sebagai **tunai**. QRIS otomatis tersedia melalui halaman pembayaran QRIS atau form POS standalone.
- Tidak ada tombol batal/hapus transaksi di UI kasir saat ini.

---

## 3. Alur Utama

### 3.1 Login dan dashboard

1. Kasir membuka `/login`.
2. Bila browser masih menyimpan sesi QR pelanggan, halaman login menampilkan 403. Kasir membuka `/login?staff=1` untuk membersihkan sesi pelanggan, lalu login.
3. Setelah kredensial valid dan role `kasir`, kasir diarahkan ke `/dashboard`.
4. Dashboard menampilkan tiga tab:
   - **Kasir & Menu** — katalog dan keranjang untuk membuat pesanan.
   - **Denah Meja** — peta meja live dengan inspector pesanan.
   - **Pesanan Aktif** — antrean pesanan `pending`, `prosesed`, dan `ready`.
5. Sidebar juga menyediakan **Riwayat Transaksi** dan tombol logout.

### 3.2 Membuat pesanan dari tab Kasir & Menu

1. Kasir memilih produk dari katalog. Produk dengan opsi kustomisasi membuka modal untuk memilih opsi dan jumlah.
2. Jumlah dibatasi stok yang ditampilkan. Bila produk yang sama sudah ada di keranjang, kuantitas digabung dan tetap dibatasi stok.
3. Kasir memilih tipe pesanan:
   - **Makan di Tempat (Dine-in)** — wajib memilih meja.
   - **Bawa Pulang (Take-away)** — meja tidak diperlukan dan disembunyikan.
4. Kasir dapat menambahkan catatan pesanan.
5. Ringkasan menampilkan subtotal, pajak & layanan 10%, dan total bayar.
6. Tombol **Simpan Pesanan** aktif bila keranjang berisi item dan, untuk dine-in, meja sudah dipilih.
7. Form mengirim `POST /transaksis` dengan `metode_pembayaran = cash`.
8. Server memeriksa meja, stok, dan produk, lalu membuat transaksi `pending`, menyimpan detail, dan mengurangi stok.
9. Kasir diarahkan kembali ke dashboard dengan pesan sukses. Pesanan muncul di Denah Meja dan Pesanan Aktif.

### 3.3 Memakai Denah Meja

1. Kasir membuka tab **Denah Meja**.
2. Kartu meja menunjukkan status:
   - **Tersedia** — tidak ada transaksi aktif.
   - **Terisi** — ada transaksi `pending`, `prosesed`, atau `ready`.
3. Filter dapat menampilkan semua meja, hanya yang tersedia, atau hanya yang terisi.
4. Klik meja kosong:
   - inspector menampilkan aksi untuk membuat pesanan baru;
   - kasir diarahkan ke POS dengan meja tersebut sudah terpilih.
5. Klik meja terisi:
   - inspector menampilkan nomor pesanan, item, total, status, dan status pembayaran;
   - tersedia aksi ubah status, bayar, dan cetak struk.
6. Denah dan daftar pesanan disinkronkan otomatis setiap 15 detik; kasir juga dapat menekan tombol sinkronisasi manual.

### 3.4 Memproses Pesanan Aktif

1. Kasir membuka tab **Pesanan Aktif**.
2. Daftar diurutkan FIFO berdasarkan `waktu_transaksi`, lalu `id`.
3. Tersedia filter status **Semua / Menunggu / Diproses / Siap**, pencarian, dan penghitung jumlah per status.
4. Setiap kartu menampilkan meja/take-away, waktu, item, total, status pembayaran, dan aksi.
5. Alur tombol utama:
   - `pending` → **Mulai Proses** → `prosesed`
   - `prosesed` → **Tandai Siap** → `ready`
   - `ready` dan sudah dibayar → **Selesaikan** → `completed`
   - `ready` dan belum dibayar → **Bayar & Selesai** → modal pembayaran
6. Kasir juga dapat memakai dropdown status untuk memilih `pending`, `prosesed`, `ready`, atau `completed` secara langsung. Server tidak memaksa urutan transisi.
7. Setiap perubahan status dikirim ke `PATCH /transaksis/{id}/status`.

### 3.5 Mencatat pembayaran

1. Pembayaran dapat dibuka dari:
   - tombol **Bayar** pada kartu pesanan aktif;
   - tombol **Bayar & Selesai** saat pesanan `ready`;
   - halaman detail transaksi.
2. Modal pembayaran menampilkan total tagihan dan dua metode: **Tunai** dan **QRIS**.
3. Untuk tunai:
   - kasir mengisi uang diterima;
   - tersedia tombol cepat **Pas**, **50k**, **100k**, dan **200k**;
   - kembalian dihitung otomatis.
4. Untuk QRIS di modal ini, kasir mencatat pembayaran QRIS secara manual; ini bukan pembayaran gateway Midtrans.
5. Form mengirim `POST /pembayarans` dengan `transaksi_id`, `jumlah_bayar`, dan `metode_pembayaran`.
6. Server menolak bila:
   - transaksi sudah memiliki pembayaran;
   - jumlah bayar kurang dari total.
7. Bila valid, server membuat pembayaran `paid`, menghitung kembalian (khusus tunai), dan langsung mengubah status transaksi menjadi `completed`.
8. Struk dibuka di tab baru. Kasir dapat mencetaknya dari halaman struk.

### 3.6 Pembayaran QRIS otomatis

1. QRIS otomatis dipakai bila pesanan dibuat melalui form POS standalone (`/transaksis/create`) dengan metode **QRIS**, atau kasir membuka `/kasir/transaksi/{id}/qris`.
2. Server membuat Snap token Midtrans dan menampilkan halaman QRIS.
3. Kasir menekan **Bayar Sekarang**, lalu pelanggan menyelesaikan pembayaran melalui Snap (QRIS, e-wallet, atau bank transfer).
4. Halaman QRIS menyediakan tombol **Cek Status**; sistem memanggil `POST /kasir/transaksi/{id}/qris/check`.
5. Bila status Midtrans `capture` atau `settlement`, server menandai pembayaran `paid` dan transaksi `completed`.
6. Bila pembayaran belum terkonfirmasi, kasir dapat mengecek ulang. Webhook `/midtrans/notification` juga memperbarui status secara otomatis.
7. Bila Midtrans mengirim `deny`, `expire`, atau `cancel`, transaksi menjadi `cancelled` dan stok/promo dikembalikan.

### 3.7 Riwayat dan struk

1. Kasir membuka **Riwayat Transaksi** (`/transaksis`).
2. Daftar hanya memuat transaksi milik kasir tersebut dan pesanan web.
3. Filter yang tersedia: pencarian berdasarkan ID/nomor meja dan filter status.
4. Klik transaksi untuk membuka detail:
   - rincian item dan total;
   - status pesanan;
   - data pembayaran;
   - aksi ubah status dan bayar bila diizinkan;
   - tombol cetak struk.
5. Kasir dapat membuka `/transaksis/create` untuk membuat pesanan dari form POS standalone.

### 3.8 Logout

Kasir menekan **Keluar / Log Out** di sidebar. Sesi `karyawan` dihapus dan kasir diarahkan ke `/login`.

---

## 4. Flowchart

Buka [`kasir.drawio`](./diagrams/kasir.drawio) untuk flowchart satu halaman yang mencakup POS, denah meja, pesanan aktif, pembayaran tunai, QRIS otomatis, struk, dan riwayat.

---

## 5. Layar dan Endpoint

| Aksi | Layar | Endpoint | Method |
|---|---|---|---|
| Login | Form login | `/login` | `GET`, `POST` |
| Dashboard | Tiga tab kasir | `/dashboard` | `GET` |
| Form POS standalone | POS | `/transaksis/create` | `GET` |
| Simpan pesanan POS | POS | `/transaksis` | `POST` |
| Ambil antrean aktif | Polling JSON | `/pos/active-orders` | `GET` |
| Ubah status | Kartu / detail | `/transaksis/{id}/status` | `PATCH` |
| Catat pembayaran | Modal / detail | `/pembayarans` | `POST` |
| Cetak struk | Halaman struk | `/transaksis/{id}/receipt` | `GET` |
| Halaman QRIS | QRIS | `/kasir/transaksi/{id}/qris` | `GET` |
| Cek status QRIS | QRIS | `/kasir/transaksi/{id}/qris/check` | `POST` |
| Riwayat transaksi | Riwayat | `/transaksis` | `GET` |
| Detail transaksi | Detail | `/transaksis/{id}` | `GET` |
| Hapus transaksi (internal) | Tidak ada tombol UI | `/transaksis/{id}` | `DELETE` |

---

## 6. Aturan Bisnis

1. **Satu meja satu pesanan aktif.** Meja terisi bila memiliki transaksi `pending`, `prosesed`, atau `ready`.
2. **Stok dikurangi saat pesanan dibuat.** Pembatalan atau penghapusan mengembalikan stok satu kali melalui penanda `sudah_dibalik`.
3. **POS dashboard selalu tunai.** Form di dashboard menyimpan `metode_pembayaran = cash`; QRIS otomatis memakai form POS standalone atau halaman QRIS.
4. **Pembayaran langsung menyelesaikan pesanan.** `PembayaranController@store` membuat pembayaran `paid` dan mengubah status menjadi `completed` tanpa menunggu status `ready`.
5. **Pembayaran ganda ditolak.** Satu transaksi hanya boleh punya satu baris pembayaran.
6. **Jumlah bayar tidak boleh kurang.** Nominal di bawah total ditolak.
7. **Kembalian hanya dihitung untuk tunai.** QRIS manual memakai kembalian 0.
8. **Pelunasan via QRIS gateway membuat pembayaran `QRIS` otomatis.** Nominalnya sama dengan total transaksi.
9. **Promo tidak tersedia di POS.** Promo hanya diterapkan pada pesanan web.
10. **Pelepasan promo** terjadi saat transaksi dibatalkan atau saat status diubah menjadi `completed` melalui endpoint status.
11. **Kasir tidak dapat memproses pesanan kasir lain.** Policy mengizinkan transaksi miliknya sendiri atau pesanan web.
12. **Tidak ada pembatalan lewat UI.** Route `DELETE` tersedia, tetapi tidak ada tombol batal/hapus di tampilan kasir.

---

## 7. Edge Cases

| Situasi | Perilaku sistem |
|---|---|
| Meja sudah dipakai saat kasir menyimpan pesanan | Server menolak dengan error “Meja ini sudah memiliki pesanan aktif”. Kasir memilih meja lain atau take-away. |
| Tidak ada meja aktif | Form POS menampilkan peringatan; pesanan dine-in tidak dapat disimpan. |
| Stok tidak mencukupi | Server menolak seluruh pesanan dan menampilkan nama produk yang stoknya kurang. |
| Produk dinonaktifkan admin | Produk tidak muncul di katalog POS; bila ID dikirim langsung, server menolak. |
| Pembayaran kurang dari total | Ditolak dengan pesan jumlah pembayaran kurang. |
| Pembayaran kedua pada transaksi yang sama | Ditolak dengan pesan transaksi sudah memiliki pembayaran. |
| Pembayaran dicatat saat status masih `pending` | Transaksi langsung menjadi `completed`; ini perilaku aktual, bukan urutan ideal. |
| Kasir membuka pesanan kasir lain dari denah | Pesanan terlihat, tetapi aksi status/pembayaran dapat ditolak policy (403). |
| QRIS token gagal dibuat | Halaman QRIS kembali dengan error; pesanan tetap `pending` dan dapat dibayar tunai. |
| QRIS belum dibayar | Status tetap `pending`; kasir dapat mengecek ulang atau menunggu webhook. |
| QRIS kedaluwarsa/ditolak | Webhook mengubah transaksi menjadi `cancelled`, mengembalikan stok, dan melepas promo. |
| Pesanan `cancelled` | Tidak muncul di antrean aktif; tidak ada aksi reaktivasi. |
| Menghapus transaksi | Stok dan promo dikembalikan satu kali, lalu transaksi dihapus. Tidak tersedia dari UI. |
| Kasir logout saat ada pesanan aktif | Pesanan tetap tersimpan. Pesanan web dapat diproses kasir lain; pesanan POS tetap terikat pada kasir pembuatnya sesuai policy. |

---

## 8. Catatan Implementasi

- Pembuatan pesanan POS: `App\Http\Controllers\TransaksiController@store`.
- Perubahan status: `TransaksiController@updateStatus`; menerima `pending`, `prosesed`, `ready`, `completed`.
- Antrean aktif: `TransaksiController@activeOrdersJson`, hanya `pending`, `prosesed`, `ready`, diurutkan FIFO.
- Pembayaran: `App\Http\Controllers\PembayaranController@store`; struk: `printReceipt`.
- QRIS: `TransaksiController@showQrisPayment` dan `checkQrisPayment`; integrasi Midtrans di `App\Services\PaymentService`.
- Policy: `App\Policies\TransaksiPolicy` — admin/manager dapat mengakses semua transaksi; kasir hanya miliknya atau pesanan web.
- Transaksi POS memakai `sumber_pesanan = kasir` (default database) dan `karyawan_id` kasir yang membuat.
- Transaksi web memakai `sumber_pesanan = web` dan `karyawan_id = null`.
