# User Flow & Flowchart BagiKopi

Dokumentasi ini memetakan **alur pengguna nyata** di website BagiKopi untuk empat peran: **pelanggan**, **kasir**, **admin**, dan **manager**. Isi dokumen disusun dari route, controller, policy, dan tampilan Blade yang ada di repositori, bukan dari asumsi alur ideal.

| Item | Keterangan |
|---|---|
| **Produk** | BagiKopi — Coffee Shop Order System |
| **Versi dokumen** | 1.0 |
| **Tanggal** | 18 November 2026 |
| **Sumber acuan** | `routes/web.php`, `routes/api.php`, controller, policy, dan view Blade |
| **Dokumen terkait** | [`../PRD.md`](../PRD.md), [`../DESIGN.md`](../DESIGN.md) |

---

## 1. Daftar Dokumen

| Peran | Dokumen | Fokus |
|---|---|---|
| Pelanggan | [`pelanggan.md`](./pelanggan.md) | Scan QR, katalog, keranjang, promo, checkout, pembayaran, status pesanan |
| Kasir | [`kasir.md`](./kasir.md) | POS, denah meja, pesanan aktif, pembayaran, QRIS, struk, riwayat |
| Admin | [`admin.md`](./admin.md) | Dashboard operasional, master data, meja & QR, transaksi, ekspor laporan |
| Manager | [`manager.md`](./manager.md) | Laporan penjualan per periode, analitik, ekspor PDF |

---

## 2. Peta Peran

| Peran | Autentikasi | Titik masuk | Kemampuan utama | Batasan utama |
|---|---|---|---|---|
| **Pelanggan** | Tidak punya akun | `/` atau `/?meja_id={id}` dari QR meja | Melihat katalog, memesan, memakai promo, memilih QRIS/tunai, memantau status | Tidak dapat membuka dashboard staf |
| **Kasir** | Guard `karyawan` | `/login` → `/dashboard` | Membuat pesanan POS, memproses status, mencatat pembayaran, mencetak struk, melihat riwayat | Hanya dapat memproses transaksi miliknya atau pesanan web |
| **Admin** | Guard `karyawan` | `/login` → `/dashboard` | Seluruh master data, meja & QR, POS, transaksi, ekspor laporan | Berfokus pada operasional; dashboard analitik manager terpisah |
| **Manager** | Guard `karyawan` | `/login` → `/dashboard` | Analitik periode, laporan transaksi, ekspor PDF | Read-only terhadap transaksi; tidak dapat POS atau CRUD master data |

> Login staf hanya menerima role `admin`, `kasir`, dan `manager`. Akun dengan role lain ditolak di `AuthController`.

---

## 3. Flowchart per Peran

| Peran | File draw.io |
|---|---|
| Pelanggan | [`pelanggan.drawio`](./diagrams/pelanggan.drawio) |
| Kasir | [`kasir.drawio`](./diagrams/kasir.drawio) |
| Admin | [`admin.drawio`](./diagrams/admin.drawio) |
| Manager | [`manager.drawio`](./diagrams/manager.drawio) |

Tiap file berisi satu flowchart satu halaman. Alur QRIS kasir dan alur status pelanggan sudah tergabung di file role masing-masing.

Alur singkatnya:

1. Admin menyiapkan menu, meja, QR, promo, supplier, dan akun staf.
2. Pelanggan memindai QR meja, memesan, lalu memilih QRIS atau tunai.
3. Pesanan web masuk ke antrean kasir dengan status `pending`.
4. Kasir memproses `pending → prosesed → ready`, lalu mencatat pembayaran.
5. Pembayaran menandai transaksi `completed` dan struk dapat dicetak.
6. Manager membaca data periode dan mengekspor laporan PDF.

---

## 4. Status yang Dipakai

### 4.1 Status pesanan

| Nilai | Label UI | Arti |
|---|---|---|
| `pending` | Menunggu | Pesanan dibuat, menunggu konfirmasi/pengerjaan kasir |
| `prosesed` | Diproses | Pesanan sedang dibuat |
| `ready` | Siap | Pesanan siap diantar/diambil |
| `completed` | Selesai | Pesanan selesai dan pembayaran sudah tercatat |
| `cancelled` | Dibatalkan | Pesanan batal, umumnya karena QRIS gagal/kedaluwarsa |

Alur yang dipakai UI utama terlihat pada flowchart pelanggan dan kasir:

[`pelanggan.drawio`](./diagrams/pelanggan.drawio) dan [`kasir.drawio`](./diagrams/kasir.drawio) menunjukkan posisi status ini dalam alur lengkap masing-masing peran.

> Endpoint web `PATCH /transaksis/{id}/status` menerima `pending`, `prosesed`, `ready`, dan `completed` tanpa memaksa urutan. Endpoint ini tidak menerima `cancelled`; pembatalan berasal dari webhook Midtrans atau endpoint API staf. Karena itu, urutan di atas adalah alur yang digunakan UI, bukan aturan transisi yang dipaksakan server.

### 4.2 Status pembayaran

| Nilai | Arti |
|---|---|
| `pending` | Belum dibayar |
| `paid` | Lunas |

Tidak ada status `refunded` atau `failed` pada tabel pembayaran. Kegagalan QRIS dicatat sebagai transaksi `cancelled`.

---

## 5. Aturan Lintas Peran

1. **Pajak & layanan** dihitung 10% dari subtotal, baik di checkout web maupun POS.
2. **Stok** diperiksa dan dikurangi saat pesanan dibuat. Stok dikembalikan hanya saat transaksi dibatalkan atau dihapus.
3. **Satu meja satu pesanan aktif.** Meja dianggap terisi bila memiliki transaksi berstatus `pending`, `prosesed`, atau `ready`.
4. **Promo** hanya tersedia di checkout web. Satu meja atau satu sesi browser hanya boleh memiliki satu promo aktif.
5. **QRIS** memakai Midtrans Snap. Status lunas dapat masuk lewat polling, cek manual, atau webhook `/midtrans/notification`.
6. **Pembayaran tunai** dicatat kasir dan langsung menandai transaksi `completed`.
7. **Laporan** mengabaikan transaksi `cancelled` dan memakai zona waktu `Asia/Jakarta`.

---

## 6. Cara Membaca Flowchart

- Buka berkas `.drawio` langsung di draw.io untuk melihat dan mengedit flowchart.
- Satu berkas untuk satu peran, dengan satu halaman di dalamnya.
- Bentuk oval menunjukkan awal atau akhir alur.
- Kotak persegi menunjukkan aksi atau layar.
- Belah ketupat menunjukkan keputusan.
- Label pada panah menunjukkan kondisi atau metode yang dipilih.
- Seluruh diagram memakai hitam putih.
- Tidak disediakan berkas SVG; Markdown hanya menautkan berkas `.drawio`.

Untuk detail teknis status, API, dan skema data, lihat [`../DESIGN.md`](../DESIGN.md). Untuk kebutuhan produk dan acceptance criteria, lihat [`../PRD.md`](../PRD.md).
