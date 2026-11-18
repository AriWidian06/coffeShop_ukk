# ERD BagiKopi — Semua Role

| Item | Keterangan |
|---|---|
| **Cakupan** | Admin, manager, kasir, dan pelanggan (digabung) |
| **Sumber skema** | [`../../database/migrations`](../../database/migrations) |
| **File draw.io** | [`./diagrams/erd.drawio`](./diagrams/erd.drawio) |
| **Gaya** | ERD biasa, hitam putih |

ERD ini memetakan seluruh tabel dan relasi yang dipakai keempat peran. Kardinalitas
`1 : N` ditulis `||--o{`, sedangkan `1 : 0..1` ditulis `||--o|`.

## 1. Daftar Relasi

| Dari | Ke | Kardinalitas | Kolom penghubung |
|---|---|---|---|
| KARYAWANS | TRANSAKSIS | 1 : N | karyawan_id |
| MEJAS | TRANSAKSIS | 1 : N | meja_id |
| TRANSAKSIS | PEMBAYARANS | 1 : 0..1 | transaksi_id (1:1) |
| TRANSAKSIS | DETAIL_TRANSAKSIS | 1 : N | transaksi_id |
| PRODUKS | DETAIL_TRANSAKSIS | 1 : N | produk_id |
| PRODUKS | PRODUK_OPSIS | 1 : N | produk_id |
| PRODUKS | PRODUK_GAMBARS | 1 : 0..1 | produk_id (1:1) |
| SUPPLIERS | PRODUKS | 1 : N | supplier_id |
| KATEGORI_PRODUKS | PRODUKS | 1 : N | kategori_produk_id |
| PROMO_KODES | PROMO_USAGES | 1 : N | promo_kode_id |
| TRANSAKSIS | PROMO_USAGES | 1 : N | transaksi_id |
| MEJAS | PROMO_USAGES | 1 : N | meja_id |
| PROMO_KODES | TRANSAKSIS | 1 : N | promo_kode_id |

## 2. Diagram Mermaid

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','secondaryColor':'#ffffff','tertiaryColor':'#ffffff','fontFamily':'Arial','fontSize':'11px'}}}%%
erDiagram
    KARYAWANS {
        bigint id PK
        varchar nama_karyawan
        varchar jabatan
        varchar no_telepon
        text alamat
        enum role
        varchar username UK
        varchar password
        varchar remember_token
        timestamp created_at
        timestamp updated_at
    }
    MEJAS {
        bigint id PK
        int nomor_meja UK
        int kapasitas
        boolean status_aktif
        timestamp created_at
        timestamp updated_at
    }
    KATEGORI_PRODUKS {
        bigint id PK
        varchar nama_kategori
        timestamp created_at
        timestamp updated_at
    }
    SUPPLIERS {
        bigint id PK
        varchar nama_supplier
        varchar alamat
        varchar no_telp
        timestamp created_at
        timestamp updated_at
    }
    PRODUKS {
        bigint id PK
        bigint supplier_id FK
        bigint kategori_produk_id FK
        varchar nama_produk
        text deskripsi
        varchar gambar
        decimal harga_jual
        decimal harga_beli
        enum tipe
        varchar satuan
        int stock
        boolean status_aktif
        boolean is_customizable
        timestamp created_at
        timestamp updated_at
    }
    PRODUK_OPSIS {
        bigint id PK
        bigint produk_id FK
        varchar nama_opsi
        decimal harga_tambahan
        varchar kategori
        enum tipe
        boolean is_required
        timestamp created_at
        timestamp updated_at
    }
    PRODUK_GAMBARS {
        bigint id PK
        bigint produk_id FK,UK
        varchar mime
        text data
        timestamp created_at
        timestamp updated_at
    }
    PROMO_KODES {
        bigint id PK
        varchar kode UK
        varchar nama_promo
        decimal nilai_diskon
        decimal min_belanja
        decimal maks_diskon
        datetime tanggal_mulai
        datetime tanggal_berakhir
        int kuota
        int kuota_terpakai
        boolean is_aktif
        timestamp created_at
        timestamp updated_at
    }
    TRANSAKSIS {
        bigint id PK
        bigint meja_id FK
        bigint karyawan_id FK
        bigint promo_kode_id FK
        enum tipe_pesanan
        varchar sumber_pesanan
        text catatan
        decimal diskon
        varchar session_id
        decimal total_harga
        enum status_pesanan
        boolean sudah_dibalik
        timestamp waktu_transaksi
    }
    DETAIL_TRANSAKSIS {
        bigint id PK
        bigint transaksi_id FK
        bigint produk_id FK
        int QTY
        decimal subtotal
        varchar customization
        timestamp created_at
        timestamp updated_at
    }
    PEMBAYARANS {
        bigint id PK
        bigint transaksi_id FK,UK
        decimal jumlah_bayar
        enum metode_pembayaran
        enum status_pembayaran
        decimal kembalian
        timestamp waktu_pembayaran
    }
    PROMO_USAGES {
        bigint id PK
        bigint promo_kode_id FK
        bigint meja_id FK
        bigint transaksi_id FK
        varchar session_id
        decimal discount_amount
        varchar status
        timestamp used_at
        timestamp released_at
        timestamp created_at
        timestamp updated_at
    }
    SESSIONS {
        varchar id PK
        bigint user_id
        varchar ip_address
        text user_agent
        text payload
        int last_activity
    }

    KARYAWANS ||--o{ TRANSAKSIS : "karyawan_id"
    MEJAS ||--o{ TRANSAKSIS : "meja_id"
    TRANSAKSIS ||--o| PEMBAYARANS : "transaksi_id (1:1)"
    TRANSAKSIS ||--o{ DETAIL_TRANSAKSIS : "transaksi_id"
    PRODUKS ||--o{ DETAIL_TRANSAKSIS : "produk_id"
    PRODUKS ||--o{ PRODUK_OPSIS : "produk_id"
    PRODUKS ||--o| PRODUK_GAMBARS : "produk_id (1:1)"
    SUPPLIERS ||--o{ PRODUKS : "supplier_id"
    KATEGORI_PRODUKS ||--o{ PRODUKS : "kategori_produk_id"
    PROMO_KODES ||--o{ PROMO_USAGES : "promo_kode_id"
    TRANSAKSIS ||--o{ PROMO_USAGES : "transaksi_id"
    MEJAS ||--o{ PROMO_USAGES : "meja_id"
    PROMO_KODES ||--o{ TRANSAKSIS : "promo_kode_id"
```

## 3. File draw.io

Buka [`erd.drawio`](./diagrams/erd.drawio) untuk versi draw.io. File ini berisi satu halaman, seluruh entity dan relasi, dengan warna hitam putih saja.

## 4. Daftar Entity

`KARYAWANS`, `MEJAS`, `KATEGORI_PRODUKS`, `SUPPLIERS`, `PRODUKS`, `PRODUK_OPSIS`, `PRODUK_GAMBARS`, `PROMO_KODES`, `TRANSAKSIS`, `DETAIL_TRANSAKSIS`, `PEMBAYARANS`, `PROMO_USAGES`, `SESSIONS`
