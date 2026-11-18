# User Flow Pelanggan — BagiKopi

| Item | Keterangan |
|---|---|
| **Peran** | Pelanggan / tamu |
| **Autentikasi** | Tidak perlu akun |
| **Titik masuk** | `GET /` atau `GET /?meja_id={id}` dari QR meja |
| **Layout** | Halaman publik `welcome.blade.php` |
| **Dokumen terkait** | [`README.md`](./README.md), [`../PRD.md`](../PRD.md), [`../DESIGN.md`](../DESIGN.md) |
| **Diagram** | draw.io (1 file, 1 halaman, tanpa SVG) |

---

## 1. Ringkasan

Pelanggan memesan tanpa login. Alur dimulai dari memindai QR meja (atau membuka situs langsung), memilih menu, mengisi keranjang, memakai promo bila ada, memilih QRIS atau tunai, lalu memantau status pesanan secara otomatis. Pesanan web dicatat dengan `sumber_pesanan = web` dan `karyawan_id = null`, sehingga masuk ke antrean kasir sebagai pesanan `pending`.

---

## 2. Prasyarat dan Batasan

- Meja harus aktif (`status_aktif = true`) agar bisa dipilih.
- Satu meja hanya boleh memiliki satu transaksi aktif (`pending`, `prosesed`, atau `ready`).
- Produk yang tampil hanya produk dengan `tipe = jual` dan `status_aktif = true`.
- Pembelian dibatasi stok produk. Pengecekan dilakukan di browser dan diulang di server saat checkout.
- Pajak & layanan sebesar **10%** ditambahkan ke subtotal.
- Promo hanya dapat dipakai satu kali per meja aktif, atau satu kali per sesi browser untuk take-away.
- Tidak ada halaman login pelanggan. Halaman `/login` khusus staf.

---

## 3. Alur Utama

### 3.1 Masuk dari QR meja

1. Pelanggan memindai QR yang berisi URL `/?meja_id={id}`.
2. Server mencari meja aktif dengan ID tersebut, lalu menyimpan `meja_id` dan `meja_nomor` di sesi.
3. Server juga menulis cookie `qr_meja_id` dan `qr_meja_nomor` selama 7 hari sebagai cadangan.
4. Meja yang berasal dari QR dikunci di halaman checkout: pelanggan tidak dapat menggantinya ke meja lain selama sesi QR aktif.
5. Bila pelanggan membuka situs tanpa QR, ia tetap dapat memilih meja aktif secara manual atau memilih **Takeaway**.

### 3.2 Memilih menu

1. Katalog menampilkan kategori, pencarian, harga, stok, dan status ketersediaan.
2. Pelanggan membuka detail produk untuk melihat deskripsi, opsi kustomisasi (suhu, gula, susu, dan sebagainya), lalu menentukan jumlah.
3. Produk dengan opsi kustomisasi menambahkan harga opsi ke harga dasar.
4. Jumlah di keranjang dibatasi stok. Bila produk yang sama ditambahkan dari beberapa baris, total kuantitas tetap tidak boleh melebihi stok.
5. Item masuk ke keranjang dan badge jumlah diperbarui.

### 3.3 Checkout

1. Pelanggan membuka halaman **Keranjang & Checkout**.
2. Pelanggan memilih tipe layanan:
   - **Dine-in** wajib memilih meja.
   - **Takeaway** tidak membutuhkan meja.
3. Bila datang dari QR, meja sudah terpilih dan tidak dapat diubah. Bila tidak, daftar meja menampilkan semua meja aktif; meja yang sedang terisi dinonaktifkan.
4. Pelanggan dapat menambahkan catatan pesanan (maksimal 500 karakter) dan kode promo.
5. Tombol checkout tetap nonaktif sampai keranjang berisi item dan, untuk dine-in, meja sudah dipilih.

### 3.4 Memakai promo

1. Pelanggan menekan **Terapkan** setelah mengisi kode.
2. Browser memanggil `POST /api/promo/validate` dengan kode, subtotal, `meja_id`, dan `session_id`.
3. Server memeriksa:
   - promo aktif;
   - tanggal mulai dan berakhir;
   - kuota tersisa;
   - minimal belanja;
   - belum ada promo aktif untuk meja tersebut;
   - untuk take-away, belum ada promo aktif untuk sesi browser tersebut.
4. Bila valid, diskon ditampilkan dan total diperbarui. Bila tidak, pesan alasan ditampilkan dan promo tidak diterapkan.
5. Server memvalidasi ulang promo saat checkout. Perubahan harga, kuota, atau status promo di antara validasi dan checkout dapat membuat pesanan ditolak.

### 3.5 Memilih pembayaran dan mengirim pesanan

1. Pelanggan memilih:
   - **QRIS Instan** — pembayaran langsung melalui Midtrans Snap.
   - **Bayar di Kasir / Tunai** — pesanan dibayar di kasir.
2. Browser mengirim `POST /api/transaksis` berisi meja, tipe pesanan, metode pembayaran, catatan, `session_id`, kode promo, dan daftar item.
3. Server, dalam satu transaksi database:
   - mengunci meja dan menolak bila sudah ada pesanan aktif;
   - mengunci produk dan memeriksa stok gabungan per produk;
   - menghitung harga produk + opsi kustomisasi;
   - menghitung pajak 10%;
   - memvalidasi promo dan menghitung diskon;
   - membuat transaksi `pending`;
   - menyimpan detail transaksi;
   - mengurangi stok;
   - menambah `kuota_terpakai` dan mencatat `PromoUsage` bila promo dipakai.
4. Bila berhasil, modal struk ditampilkan berisi nomor pesanan, item, total, dan status.
5. Untuk QRIS, server membuat Snap token. Bila QR berhasil dibuat, gambar QR ditampilkan dan status pembayaran dipolling setiap 3 detik. Bila pembuatan QR gagal, pesanan tetap tersimpan dan pelanggan diarahkan membayar di kasir.
6. Untuk tunai, pelanggan diminta membayar di kasir dengan menunjukkan nomor meja/pesanan.

### 3.6 Memantau status

1. Pelanggan membuka tab **Status**.
2. Browser memanggil `GET /api/pesanan-saya?session_id=...&meja_id=...`.
3. Pencarian dilakukan berdasarkan `session_id` terlebih dahulu; bila tidak ditemukan, server mencari transaksi aktif berdasarkan `meja_id`.
4. Tab status menampilkan:
   - status `pending` — “Menunggu Konfirmasi”;
   - status `prosesed` — “Sedang Dibuat”;
   - status `ready` — “Siap Diantar”;
   - status `completed` — pesanan dianggap selesai dan tampilan status dikosongkan;
   - status `cancelled` — pesan bahwa pesanan dibatalkan karena pembayaran tidak berhasil.
5. Polling berjalan setiap 5 detik, tetapi hanya saat tab Status aktif dan halaman sedang terlihat. Pelanggan juga dapat menekan tombol refresh manual.
6. Setelah pesanan selesai atau dibatalkan, pelanggan dapat membuat pesanan baru.

---

## 4. Flowchart

Buka [`pelanggan.drawio`](./diagrams/pelanggan.drawio) untuk flowchart satu halaman yang mencakup QR meja, katalog, keranjang, promo, checkout, QRIS/tunai, dan status pesanan.

---

## 5. Layar dan Endpoint

| Aksi | Layar | Endpoint | Method |
|---|---|---|---|
| Buka katalog / QR meja | Halaman utama | `/` atau `/?meja_id={id}` | `GET` |
| Ambil gambar produk | Katalog | `/produk/{produk}/gambar` | `GET` |
| Validasi promo | Keranjang | `/api/promo/validate` | `POST` |
| Kirim pesanan | Keranjang | `/api/transaksis` | `POST` |
| Ambil status pesanan aktif | Tab Status | `/api/pesanan-saya` | `GET` |
| Cek status QRIS | Modal struk / tab Status | `/api/transaksis/{id}/check-status` | `POST` |
| Webhook Midtrans | Server-to-server | `/midtrans/notification` | `POST` |

---

## 6. Aturan Bisnis yang Terlihat oleh Pelanggan

1. **Meja dari QR bersifat mengikat.** Pelanggan tidak dapat berpindah meja selama memakai tautan QR tersebut. Untuk membuka pesanan baru di meja lain, pelanggan perlu memindai QR meja tersebut.
2. **Meja terisi tidak bisa dipilih.** Status terisi dihitung dari transaksi aktif, bukan dari kolom status meja.
3. **Take-away tidak memakai meja.** Nilai `meja_id` dikirim `null`.
4. **Harga opsi kustomisasi ikut dihitung.** Baik di keranjang maupun di server.
5. **Stok dicek ulang di server.** Menambah item di browser tidak menjamin stok masih tersedia saat checkout.
6. **Diskon dihitung dari subtotal, lalu dikurangkan setelah pajak.** Total akhir = subtotal + pajak 10% − diskon, minimal 0.
7. **Pembayaran QRIS tidak langsung menyelesaikan pesanan.** Status baru menjadi `completed` setelah Midtrans mengirim `capture`/`settlement` atau webhook valid.
8. **Pembayaran tunai diselesaikan kasir.** Setelah kasir mencatat pembayaran, status transaksi menjadi `completed`.
9. **Status `completed` menghapus tampilan pesanan lama.** Tab Status kembali ke kondisi kosong agar pelanggan berikutnya tidak melihat pesanan sebelumnya.
10. **Status `cancelled` tidak menampilkan detail pesanan.** Pelanggan hanya melihat pesan pembatalan.

---

## 7. Edge Cases

| Situasi | Perilaku sistem |
|---|---|
| Meja diambil pelanggan lain di antara pemilihan dan checkout | Server menolak dengan pesan “Meja ini sudah memiliki pesanan aktif”. Pelanggan harus memilih meja lain atau take-away. |
| Semua meja terisi saat membuka situs tanpa QR | Daftar meja menampilkan opsi “Semua meja sudah terisi”; pelanggan masih dapat memilih take-away. Namun server tetap menolak bila meja yang dikirim sudah terpakai. |
| Stok berubah setelah item masuk keranjang | Checkout ditolak dengan pesan stok tidak mencukupi; pelanggan perlu menyesuaikan jumlah. |
| Produk dinonaktifkan admin saat pelanggan memesan | Server menolak item karena produk tidak lagi memenuhi `status_aktif` dan `tipe = jual`. |
| Kode promo salah, kedaluwarsa, kuota habis, atau di bawah minimal belanja | Pesan alasan ditampilkan; pesanan tidak dapat dikirim dengan promo tersebut. |
| Promo sudah dipakai di meja/sesi yang sama | Server menolak dengan pesan bahwa meja/sesi sudah memakai promo pada pesanan aktif. |
| QRIS tidak dapat dibuat (konfigurasi Midtrans bermasalah) | Pesanan tetap tersimpan sebagai `pending`; pelanggan diarahkan membayar di kasir. |
| QRIS kedaluwarsa, ditolak, atau dibatalkan | Webhook menandai transaksi `cancelled`, mengembalikan stok, dan melepas kuota promo. |
| Pelanggan kehilangan `session_id` di localStorage | Status masih dapat ditemukan lewat `meja_id` bila pesanan aktif terikat ke meja. Untuk take-away, status tidak dapat dipulihkan tanpa `session_id`. |
| Halaman di-refresh saat modal QRIS terbuka | Polling berhenti; pelanggan dapat membuka tab Status atau meminta kasir mengecek pembayaran. |
| Pelanggan membuat pesanan baru setelah pesanan sebelumnya selesai | Diperbolehkan; meja yang sama dapat dipakai lagi karena tidak ada transaksi aktif. |
| Terlalu banyak permintaan | Endpoint pelanggan dibatasi 30 permintaan per menit per IP. |

---

## 8. Catatan Implementasi

- Pesanan web dibuat oleh `App\Http\Controllers\Api\TransaksiController@store`.
- Pencarian status memakai `session_id` lebih dulu, lalu `meja_id`; hanya transaksi aktif (`pending`, `prosesed`, `ready`) yang dikembalikan oleh fallback meja.
- Metadata status untuk UI:

  | Status | Label | Langkah |
  |---|---|---|
  | `pending` | Menunggu Konfirmasi | 1 |
  | `prosesed` | Sedang Dibuat | 2 |
  | `ready` | Siap Diantar | 3 |
  | `completed` | Selesai | 4 |
  | `cancelled` | Dibatalkan | 0 |

- QRIS ditangani `App\Services\PaymentService`: pembuatan Snap token, sinkronisasi status, penandaan lunas, dan pembatalan.
- Webhook Midtrans memverifikasi signature dengan server key. Bila server key kosong, notifikasi ditolak.
- Stok dan promo dibalik secara idempoten melalui `Transaksi::reverseStockAndPromo()` dan penanda `sudah_dibalik`.
- Promo yang masih aktif dilepas saat transaksi dibatalkan atau saat status diubah menjadi `completed` melalui aksi status. Pembayaran yang otomatis menandai `completed` tidak memanggil pelepasan promo secara terpisah; ini perlu diperhatikan saat menguji skenario promo.
