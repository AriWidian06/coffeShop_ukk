# S4-05 — Spesifikasi Dokumentasi API BagiKopi

**Versi:** `v1.0.0-rc`  
**Base URL lokal:** `http://127.0.0.1:8000`  
**Demo online:** `https://bagikopiordersystem.onrender.com`  
**Format:** JSON (`Accept: application/json`)

Dokumen ini melengkapi artefak **S4-05: Spesifikasi Dokumentasi API (Postman / OpenAPI)** untuk proyek **BagiKopi — Coffee Shop Order System**.

BagiKopi memiliki dua kelompok API:

1. **API Pelanggan Publik** — tanpa token, dipakai halaman self-order pelanggan.
2. **API Staf v1** — memakai **Bearer token Sanctum**, mencakup modul Admin, Kasir/POS, Manager, laporan, dan dashboard.

---

## 📁 Berkas

| Berkas | Format | Fungsi |
|---|---|---|
| [`bagikopi_openapi.json`](bagikopi_openapi.json) | OpenAPI 3.0.0 | Spesifikasi resmi API publik dan API staf v1: path, schema, request/response, Bearer auth, dan error. |
| [`bagikopi_api_collection.json`](bagikopi_api_collection.json) | Postman Collection v2.1 | 38 request siap pakai: API publik + folder `05 - Staff API v1`. |
| [`bagikopi_local.postman_environment.json`](bagikopi_local.postman_environment.json) | Postman Environment | Variabel lokal `http://127.0.0.1:8000`. |
| [`bagikopi_online.postman_environment.json`](bagikopi_online.postman_environment.json) | Postman Environment | Variabel demo online Render. |
| `README.md` | Markdown | Panduan import, autentikasi, aturan bisnis, contoh cURL, dan checklist S4-05. |

---

## 🔌 Ringkasan Endpoint

### 1. API Pelanggan Publik (tanpa token)

| Method | Endpoint | Fungsi | Proteksi |
|---|---|---|---|
| `POST` | `/api/transaksis` | Buat pesanan baru dari halaman pelanggan | Rate limit `api` |
| `GET` | `/api/pesanan-saya` | Ambil pesanan aktif berdasarkan `session_id` / `meja_id` | Rate limit `api` |
| `POST` | `/api/transaksis/{id}/check-status` | Cek status pembayaran QRIS ke Midtrans | Rate limit `api` + validasi `order_id` |
| `POST` | `/api/promo/validate` | Validasi kode promo dan hitung diskon | Rate limit `api` |
| `POST` | `/midtrans/notification` | Webhook internal Midtrans | Signature SHA-512 |
| `GET` | `/health` | Health check server dan database | Publik |

Rate limit API publik: **30 request/menit per IP**.

### 2. API Staf v1 (Bearer token)

Semua endpoint di bawah memakai prefix `/api/v1` dan header:

```http
Authorization: Bearer <access_token>
Accept: application/json
```

#### Auth

| Method | Endpoint | Fungsi | Role |
|---|---|---|---|
| `POST` | `/api/v1/auth/login` | Login staf, membuat token Sanctum | Publik (rate limit 5/menit) |
| `GET` | `/api/v1/auth/me` | Profil staf pemilik token | Semua staf |
| `POST` | `/api/v1/auth/logout` | Cabut token aktif | Semua staf |

#### Katalog & Meja

| Method | Endpoint | Fungsi | Role |
|---|---|---|---|
| `GET` | `/api/v1/produks` | Daftar produk | Semua staf |
| `GET` | `/api/v1/produks/{produk}` | Detail produk | Semua staf |
| `POST` | `/api/v1/produks` | Tambah produk | Admin |
| `PUT/PATCH` | `/api/v1/produks/{produk}` | Ubah produk | Admin |
| `DELETE` | `/api/v1/produks/{produk}` | Hapus produk | Admin |
| `GET` | `/api/v1/kategori-produks` | Daftar kategori | Semua staf |
| `GET` | `/api/v1/kategori-produks/{kategoriProduk}` | Detail kategori | Semua staf |
| `POST` | `/api/v1/kategori-produks` | Tambah kategori | Admin |
| `PUT/PATCH` | `/api/v1/kategori-produks/{kategoriProduk}` | Ubah kategori | Admin |
| `DELETE` | `/api/v1/kategori-produks/{kategoriProduk}` | Hapus kategori | Admin |
| `GET` | `/api/v1/mejas/status` | Status seluruh meja + okupansi | Semua staf |
| `GET` | `/api/v1/mejas` | Daftar meja | Semua staf |
| `GET` | `/api/v1/mejas/{meja}` | Detail meja | Semua staf |
| `POST` | `/api/v1/mejas` | Tambah meja | Admin |
| `PUT/PATCH` | `/api/v1/mejas/{meja}` | Ubah meja | Admin |
| `DELETE` | `/api/v1/mejas/{meja}` | Hapus meja | Admin |

#### Master Data Admin

| Method | Endpoint | Fungsi |
|---|---|---|
| `GET/POST` | `/api/v1/suppliers` | Daftar / tambah supplier |
| `GET/PUT/PATCH/DELETE` | `/api/v1/suppliers/{supplier}` | Detail / ubah / hapus supplier |
| `GET/POST` | `/api/v1/karyawans` | Daftar / tambah akun karyawan |
| `GET/PUT/PATCH/DELETE` | `/api/v1/karyawans/{karyawan}` | Detail / ubah / hapus karyawan |
| `GET/POST` | `/api/v1/promo-kodes` | Daftar / tambah kode promo |
| `GET/PUT/PATCH/DELETE` | `/api/v1/promo-kodes/{promoKode}` | Detail / ubah / hapus kode promo |

#### POS & Transaksi

| Method | Endpoint | Fungsi | Role |
|---|---|---|---|
| `GET` | `/api/v1/pos/active-orders` | Antrean pesanan aktif (FIFO) | Admin, Kasir |
| `GET` | `/api/v1/transaksis` | Daftar transaksi | Semua staf (kasir dibatasi) |
| `POST` | `/api/v1/transaksis` | Buat transaksi POS | Admin, Kasir |
| `GET` | `/api/v1/transaksis/{transaksi}` | Detail transaksi | Semua staf (sesuai policy) |
| `PATCH` | `/api/v1/transaksis/{transaksi}/status` | Ubah status pesanan | Admin, Kasir |
| `DELETE` | `/api/v1/transaksis/{transaksi}` | Hapus transaksi & kembalikan stok | Admin, Kasir |
| `POST` | `/api/v1/transaksis/{transaksi}/payment` | Konfirmasi pembayaran cash/QRIS | Admin, Kasir |
| `GET` | `/api/v1/transaksis/{transaksi}/receipt` | Struk transaksi (JSON) | Semua staf (sesuai policy) |
| `POST` | `/api/v1/transaksis/{transaksi}/qris/charge` | Buat QRIS Midtrans | Admin, Kasir |
| `POST` | `/api/v1/transaksis/{transaksi}/qris/check` | Cek status QRIS | Admin, Kasir |

#### Laporan & Dashboard

| Method | Endpoint | Fungsi | Role |
|---|---|---|---|
| `GET` | `/api/v1/laporan/summary` | Ringkasan pendapatan, item, promo, produk terlaris | Admin, Manager |
| `GET` | `/api/v1/dashboard/summary` | Ringkasan dashboard sesuai role | Semua staf |
| `GET` | `/api/v1/dashboard/sales-chart` | Grafik penjualan per jam | Semua staf |

Rate limit API staf: **120 request/menit per pengguna/token**.

---

## 🔐 Autentikasi Staf

### Login

```bash
curl -s -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "admin123",
    "device_name": "postman"
  }'
```

Response berisi `data.access_token`. Simpan token tersebut, lalu kirim pada request berikutnya:

```http
Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxx
```

### Logout

```bash
curl -s -X POST http://127.0.0.1:8000/api/v1/auth/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

Logout hanya mencabut token yang sedang dipakai; token perangkat lain tetap aktif.

### Hak Akses

| Role | Kemampuan API |
|---|---|
| **Admin** | Semua master data, POS/transaksi, laporan, dashboard. |
| **Kasir** | Baca produk/kategori/meja, POS, pembayaran, struk, transaksi miliknya/pesanan web, dashboard kasir. |
| **Manager** | Baca katalog, seluruh transaksi, laporan, dan dashboard manager. |
| **Pelanggan** | Hanya API publik tanpa token. |

Role diperiksa di server melalui middleware `auth:sanctum`, `api.role`, dan `TransaksiPolicy`. Menyembunyikan tombol di frontend tidak cukup; endpoint tetap menolak request dengan `403`.

---

## ⚙️ Aturan Bisnis Utama

1. **Pajak POS:** server menambahkan pajak **10%** dari subtotal.
2. **Total:** `total_harga = subtotal + pajak - diskon`.
3. **Promo pelanggan:** diskon dihitung dari subtotal, dibatasi `maks_diskon`, dan hanya boleh satu promo aktif per meja/sesi.
4. **Status pesanan:** `pending` → `prosesed` → `ready` → `completed`; `cancelled` untuk pembayaran QRIS gagal/expired atau pembatalan.
5. **Stok:** berkurang saat transaksi dibuat. Status `cancelled` dan hapus transaksi mengembalikan stok **satu kali** (idempoten melalui kolom `sudah_dibalik`).
6. **Pembayaran:** `jumlah_bayar` harus ≥ total. Kembalian hanya dihitung untuk `cash`. Transaksi otomatis menjadi `completed`.
7. **Meja:** satu meja hanya boleh memiliki satu pesanan aktif (`pending`, `prosesed`, `ready`).
8. **QRIS:** jika Midtrans gagal membuat charge, transaksi tetap dibuat dan response berisi `qris_error`; pembayaran dapat dilakukan di kasir.
9. **Order ID QRIS:** wajib diawali `TRX-{id_transaksi}-`.
10. **API publik** menerima `metode_pembayaran` `QRIS` atau `tunai`; **API POS staf** menerima `cash` atau `QRIS` (case-sensitive).

---

## 🚀 Import ke Postman

1. Buka Postman → **Import**:
   - `bagikopi_api_collection.json`
   - `bagikopi_local.postman_environment.json` (atau environment online).
2. Pilih environment **BagiKopi Local** / **BagiKopi Online** di kanan atas.
3. Jalankan folder **05 - Staff API v1 → 00 - Auth → Login Staf**.
   - Token otomatis disimpan ke variabel `staff_token`.
   - Default environment memakai `admin` / `admin123`. Pastikan akun admin sudah dibuat melalui seeder.
4. Jalankan urutan master data:
   - `01 - Master Data → Suppliers → Create Supplier`
   - `Categories → Create Category`
   - `Products → Create Product`
   - `Tables → Create Table`
   - ID yang dibuat disimpan otomatis ke `supplier_id`, `kategori_id`, `produk_api_id`, dan `meja_api_id`.
5. Jalankan alur POS:
   - `02 - POS & Transaksi → Active Orders`
   - `Create POS Transaction`
   - `Update Status`
   - `Payment Cash`
   - `Receipt JSON`
6. Jalankan `03 - Laporan & Dashboard`.
7. Folder `00`–`04` adalah API pelanggan publik dan tidak memerlukan token.

> Untuk menguji RBAC kasir: login sebagai admin → buat akun kasir melalui `Employees → Create Employee`, lalu ganti `staff_username`/`staff_password` di environment dan login ulang. Request ke endpoint admin akan menghasilkan `403`.

> **Variabel dinamis:** `staff_token` serta ID hasil create (supplier, kategori, produk, meja, karyawan, promo, transaksi) disimpan sebagai *collection variable* oleh test script. Environment hanya menyimpan `baseUrl`, kredensial, dan contoh input. Jangan menambahkan variabel dinamis tersebut ke environment karena nilai environment akan menimpa nilai collection.

> Jika meja contoh sudah terisi, gunakan meja lain atau selesaikan transaksi aktif terlebih dahulu. Meja yang masih memiliki pesanan aktif ditolak dengan `422`.

> Jika Midtrans sandbox belum dikonfigurasi, gunakan `metode_pembayaran: "cash"` pada POS, atau `"tunai"` pada API publik.

---

## 📖 Import OpenAPI

`bagikopi_openapi.json` dapat dibuka langsung di:

- **Swagger Editor** — drag & drop berkas untuk dokumentasi interaktif.
- **Postman** — Import → file OpenAPI → collection dibuat otomatis.
- **Redocly / Swagger UI** — untuk render dokumentasi statis.

Spesifikasi memakai `servers` lokal dan demo online. Skema keamanan `bearerAuth` menerapkan header:

```http
Authorization: Bearer <access_token>
```

---

## 🧪 Contoh cURL

### 1. Login dan simpan token

```bash
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123","device_name":"curl"}' \
  | php -r '$j=json_decode(stream_get_contents(STDIN), true); echo $j["data"]["access_token"] ?? "";')

echo "$TOKEN"
```

### 2. Buat transaksi POS

```bash
curl -s -X POST http://127.0.0.1:8000/api/v1/transaksis \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "meja_id": 1,
    "tipe_pesanan": "dine-in",
    "metode_pembayaran": "cash",
    "catatan": "Dibuat dari cURL",
    "items": [
      { "produk_id": 1, "qty": 2 }
    ]
  }'
```

### 3. Konfirmasi pembayaran

```bash
curl -s -X POST http://127.0.0.1:8000/api/v1/transaksis/1/payment \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "jumlah_bayar": 100000,
    "metode_pembayaran": "cash"
  }'
```

### 4. Ringkasan laporan (admin/manager)

```bash
curl -s "http://127.0.0.1:8000/api/v1/laporan/summary?periode=bulanan" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

### 5. Health check publik

```bash
curl -s http://127.0.0.1:8000/health \
  -H "Accept: application/json"
```

---

## 🔐 Catatan Keamanan

- API pelanggan **tidak memakai Bearer token**; jangan pernah mengirim password karyawan ke endpoint publik.
- Token staf setara password: simpan sebagai variabel Postman, jangan commit token asli ke repository.
- Logout mencabut token aktif. Untuk mencabut semua token, admin dapat menghapus akun/karyawan atau token melalui database.
- Semua perhitungan harga, pajak, diskon, dan stok dilakukan ulang di server; harga dari client tidak dipercaya.
- Role dan kepemilikan transaksi diperiksa di server (anti-IDOR), bukan hanya di UI.
- Webhook Midtrans wajib menyertakan signature:
  `SHA-512(order_id + status_code + gross_amount + MIDTRANS_SERVER_KEY)`.
- Jangan menaruh `MIDTRANS_SERVER_KEY` di frontend atau koleksi Postman yang dibagikan.
- Selalu kirim `Accept: application/json` agar error validasi dikembalikan sebagai JSON, bukan redirect HTML.

---

## ✅ Checklist S4-05

- [x] OpenAPI 3.0.0 tersedia dan valid (33 path, 71 schema).
- [x] Postman Collection v2.1 dengan 38 request, variable, dan test script.
- [x] Environment lokal dan online tersedia.
- [x] API publik pelanggan terdokumentasi lengkap.
- [x] API staf v1 dengan Bearer token Sanctum terdokumentasi.
- [x] CRUD produk, kategori, meja, supplier, karyawan, dan promo.
- [x] POS: transaksi, status, pembayaran, struk, QRIS charge/check.
- [x] Laporan penjualan dan dashboard sesuai role.
- [x] Schema request/response dan contoh error `401`, `403`, `404`, `422`, `429`.
- [x] Aturan keamanan webhook, `order_id`, RBAC, dan anti-IDOR dijelaskan.
- [x] Feature test API v1: **11 test, 84 assertion**.
- [x] Seluruh test suite: **53 test, 252 assertion lulus**.

Perintah verifikasi:

```bash
php artisan test --filter=ApiV1Test
php artisan test
```

---

## 🗓️ Changelog

| Tanggal | Versi | Perubahan |
|---|---|---|
| 15 Nov 2026 | `v1.0.0-rc` | Dokumen awal S4-05: OpenAPI, Postman collection, dan panduan integrasi API publik. |
| 15 Nov 2026 | `v1.0.0-rc` | Menambahkan API staf v1 (Sanctum), CRUD Admin, POS/Kasir, laporan, dashboard, environment, dan panduan Bearer token. |

---

© 2026 **Spark Industri** — Pengembang Aplikasi. Dilisensikan di bawah MIT.
