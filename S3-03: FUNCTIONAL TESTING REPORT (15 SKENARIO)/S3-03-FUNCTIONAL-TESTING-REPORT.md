# S3-03: FUNCTIONAL TESTING REPORT (15 SKENARIO)

## Informasi Pengujian

| Item | Keterangan |
|---|---|
| Aplikasi | Bagi Kopi (Coffee Shop Order System) |
| Framework | Laravel 12.69.2 |
| PHP | 8.3.6 |
| Database pengujian | MariaDB 10.11 (database uji terisolasi `db_perkoci`, bukan data produksi) |
| Tanggal eksekusi | 08 November 2026, 18:56 WIB |
| Metode | Automated feature test (`php artisan test`) |
| Test class | `tests/Feature/FunctionalReportTest.php` |
| Total skenario | 15 |
| Hasil | **15 PASS, 0 FAIL** |

## Ringkasan Hasil

| Status | Jumlah |
|---|---:|
| PASS | 15 |
| FAIL | 0 |
| NOT RUN | 0 |

## Tabel Hasil Pengujian

| No | Kode Skenario | Modul / Fitur | Deskripsi Pengujian & Kondisi Awal | Langkah Pengujian & Data Masukan | Hasil yang Diharapkan | Hasil Aktual | Status |
|---:|---|---|---|---|---|---|---|
| 1 | TC-AUTH-01 | Otentikasi | Login dengan kredensial valid.<br>Kondisi awal: karyawan `farhan` role kasir terdaftar; browser di halaman `/login`. | 1. Buka `/login`.<br>2. Isi username `farhan`.<br>3. Isi password `Password123!`.<br>4. Klik **Login**. | Redirect ke `/dashboard`; sesi `karyawan` aktif; user terautentikasi; cookie sesi tersimpan. | Redirect ke `/dashboard`; `assertAuthenticatedAs()` berhasil untuk guard `karyawan`. | PASS |
| 2 | TC-AUTH-02 | Otentikasi | Login dengan password salah (uji negatif).<br>Kondisi awal: karyawan `farhan` terdaftar. | 1. Buka `/login`.<br>2. Isi username `farhan`.<br>3. Isi password `salah123`.<br>4. Klik **Login**. | Login ditolak; redirect kembali ke `/login`; pesan error pada field `username`; tidak ada sesi aktif. | Redirect ke `/login`; session memiliki error `username`; user tetap guest. | PASS |
| 3 | TC-AUTH-03 | Otentikasi | Logout dan invalidasi sesi.<br>Kondisi awal: user login aktif. | 1. Kirim request logout.<br>2. Cek status autentikasi setelah logout. | Session di-invalidate; redirect ke `/login`; user kembali guest. | Redirect ke `/login`; `assertGuest('karyawan')` berhasil. | PASS |
| 4 | TC-AUTH-04 | Otorisasi | Kasir mencoba membuka area admin (uji negatif).<br>Kondisi awal: login sebagai kasir. | 1. Login sebagai kasir.<br>2. Akses `/karyawans` (route dengan Gate `access-admin-panel`). | Akses ditolak dengan HTTP 403; data admin tidak tampil. | HTTP 403 Forbidden. | PASS |
| 5 | TC-IDOR-01 | Keamanan Objek (IDOR) | Kasir A mencoba membuka transaksi milik kasir B melalui URL/ID (uji negatif).<br>Kondisi awal: dua kasir (A dan B); transaksi milik B telah dibuat. | 1. Login sebagai kasir A.<br>2. Akses langsung `/transaksis/{id_transaksi_milik_B}`. | Sistem menolak akses (HTTP 403) atau tidak menampilkan data transaksi milik kasir lain. | HTTP 403 Forbidden — akses ke transaksi milik kasir lain ditolak oleh `TransaksiPolicy`. | PASS |
| 6 | TC-PROD-01 | Produk & Kategori | Admin menambah kategori dan produk baru.<br>Kondisi awal: login admin; supplier tersedia. | 1. Tambah kategori `Seasonal Drinks`.<br>2. Tambah produk: nama `Americano`, kategori `Manual Brew`, harga jual `15000`, harga beli `8000`, stok `10`, tipe `jual`, status aktif.<br>3. Simpan. | Kategori dan produk tersimpan; produk muncul dengan harga/stok/status sesuai input. | Kategori `Seasonal Drinks` tersimpan; produk `Americano` tersimpan dengan harga `15000`, stok `10`, status aktif `1`. Redirect ke halaman index berhasil. | PASS |
| 7 | TC-MEJA-01 | Meja | Admin menambah meja lalu menonaktifkannya.<br>Kondisi awal: login admin. | 1. Tambah meja nomor `99`, kapasitas `4`, status aktif `1`.<br>2. Ubah status aktif menjadi `0`. | Meja tersimpan dengan nomor unik; status aktif berubah menjadi nonaktif. | Meja nomor `99` tersimpan aktif; setelah update, `status_aktif` menjadi `false`. | PASS |
| 8 | TC-POS-01 | POS Transaksi | Transaksi dine-in dengan item dan kustomisasi.<br>Kondisi awal: login kasir; menu `Americano` harga `20000` stok `10`; opsi `Oat Milk` +`5000`; meja `1` tersedia. | 1. Buat transaksi dine-in meja `1`.<br>2. Item `Americano` qty `2` dengan kustomisasi `Oat Milk (+5000)`.<br>3. Metode pembayaran `cash`.<br>4. Simpan transaksi. | Transaksi tersimpan status `pending`; total = (2 × (20000 + 5000)) + pajak 10% = `55000`; detail subtotal `50000`; stok produk berkurang menjadi `8`. | Transaksi tersimpan dengan total `55000`, status `pending`; detail transaksi `QTY 2`, subtotal `50000`, kustomisasi tersimpan; stok produk menjadi `8`. | PASS |
| 9 | TC-POS-02 | POS Transaksi | Transaksi take-away.<br>Kondisi awal: login kasir; menu `Americano` harga `15000` stok `5`. | 1. Buat transaksi tipe `take-away` tanpa meja.<br>2. Item `Americano` qty `1`.<br>3. Metode pembayaran `cash`.<br>4. Simpan. | Transaksi tersimpan dengan `meja_id = null`, tipe `take-away`, status `pending`, total = `15000` + pajak 10% = `16500`. | Transaksi tersimpan dengan `meja_id = null`, tipe `take-away`, total `16500`, status `pending`. | PASS |
| 10 | TC-PAY-01 | Pembayaran | Pembayaran tunai dengan uang lebih.<br>Kondisi awal: transaksi milik kasir dengan total `50000`, status `pending`. | 1. Buka pembayaran transaksi.<br>2. Isi jumlah bayar `100000`.<br>3. Pilih metode `cash`.<br>4. Konfirmasi pembayaran. | Pembayaran tercatat `cash` status `paid`; kembalian `50000`; transaksi berubah menjadi `completed`. | Pembayaran tersimpan (`jumlah_bayar 100000`, `kembalian 50000`, `status_pembayaran paid`); status transaksi menjadi `completed`. Redirect ke detail transaksi berhasil. | PASS |
| 11 | TC-PAY-02 | Pembayaran QRIS | Generate Snap token untuk pembayaran QRIS.<br>Kondisi awal: login kasir; transaksi total `25000`; gateway Midtrans dimock pada pengujian otomatis. | 1. Akses `/kasir/transaksi/{id}/qris`.<br>2. Sistem memanggil `PaymentService::createSnapToken()`.<br>3. Periksa response halaman QRIS. | Snap token berhasil dibuat; halaman QRIS menampilkan token/QR; tidak terjadi error. | Token mock `fake-snap-token` berhasil dibuat dan ditampilkan pada halaman QRIS (HTTP 200). Integrasi live ke Midtrans sandbox belum diuji. | PASS |
| 12 | TC-PAY-03 | Validasi Pembayaran (Negatif) | Pembayaran kurang dari total tagihan.<br>Kondisi awal: transaksi total `50000`, status `pending`. | 1. Isi jumlah bayar `30000`.<br>2. Pilih metode `cash`.<br>3. Konfirmasi. | Sistem menolak; pesan error `jumlah_bayar`; tidak ada record pembayaran baru; status transaksi tetap `pending`. | Request ditolak, session memiliki error `jumlah_bayar`, tidak ada record pembayaran, status transaksi tetap `pending`. | PASS |
| 13 | TC-ORD-01 | Manajemen Pesanan | Update status pesanan menjadi completed.<br>Kondisi awal: transaksi status `pending`. | 1. Kirim `PATCH /transaksis/{id}/status`.<br>2. Isi `status_pesanan = completed`.<br>3. Simpan. | Status transaksi berubah menjadi `completed`; response redirect/JSON sukses. | Status transaksi berubah menjadi `completed`; response redirect sukses. | PASS |
| 14 | TC-REP-01 | Laporan | Manager mengekspor laporan penjualan.<br>Kondisi awal: login manager; terdapat transaksi pada periode berjalan. | 1. Akses `/laporan/export`.<br>2. Periksa response file laporan. | Response HTTP 200 dengan file PDF; isi file valid diawali `%PDF`. | HTTP 200; header `Content-Type: application/pdf`; konten diawali `%PDF`. | PASS |
| 15 | TC-ERR-01 | Error Handling | Halaman error kustom 404 dan 500.<br>Kondisi awal: aplikasi berjalan; mode debug dinonaktifkan untuk simulasi 500. | 1. Akses URL yang tidak terdaftar.<br>2. Simulasikan exception server pada route uji.<br>3. Periksa tampilan error. | URL tidak ditemukan menampilkan halaman 404 kustom; exception server menampilkan halaman 500 kustom dengan tema aplikasi. | Halaman 404 menampilkan teks `Halaman Tidak Ditemukan`; halaman 500 menampilkan teks `Mohon Maaf, Terjadi Kendala`. | PASS |

## Perbaikan Anti-IDOR

Skenario `TC-IDOR-01` sebelumnya FAIL karena Gate `view-transaction` hanya memeriksa role. Perbaikan yang diterapkan:

- Menambahkan `app/Policies/TransaksiPolicy.php` dengan pengecekan kepemilikan data:
  - `admin`/`manager` boleh mengakses semua transaksi.
  - `kasir` hanya boleh mengakses transaksi miliknya (`karyawan_id`) atau transaksi pemesanan web.
- Mendaftarkan policy melalui `Gate::policy(Transaksi::class, TransaksiPolicy::class)` di `AppServiceProvider`.
- Mengganti `Gate::authorize('view-transaction', ...)` menjadi `$this->authorize('view', $transaksi)` pada `TransaksiController` dan `PembayaranController`.
- Menambahkan authorization pada endpoint QRIS (`showQrisPayment` dan `checkQrisPayment`).
- `destroy` memakai `$this->authorize('delete', $transaksi)`.

Setelah perbaikan, pengujian ulang menghasilkan HTTP 403 sesuai ekspektasi.

## Bukti Eksekusi

Perintah pengujian:

```bash
php artisan test --filter=FunctionalReportTest
```

Hasil:

```text
Tests: 15 passed (58 assertions)
```

Seluruh 15 skenario berstatus PASS setelah perbaikan anti-IDOR. Total seluruh test suite aplikasi: **22 passed (70 assertions)**.

## Catatan

- Pengujian menggunakan database uji terisolasi dan tidak mengubah data produksi.
- `TC-PAY-02` menggunakan mock gateway Midtrans; koneksi live ke Midtrans sandbox belum diuji.
- `TC-ERR-01` menguji halaman 404 dan 500 kustom; untuk 500, mode debug dinonaktifkan pada saat pengujian.
