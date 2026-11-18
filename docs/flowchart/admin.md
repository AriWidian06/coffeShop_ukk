# Flowchart Admin — BagiKopi

| Item | Keterangan |
|---|---|
| **Peran** | Admin |
| **Sumber narasi** | [`../userflow/admin.md`](../userflow/admin.md) |
| **File draw.io** | [`./diagrams/admin.drawio`](./diagrams/admin.drawio) |
| **Gaya** | Flowchart biasa, hitam putih |

Login, dashboard, master data, meja & QR, POS/transaksi, dan ekspor laporan.

## 1. Flowchart Mermaid

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','secondaryColor':'#ffffff','tertiaryColor':'#ffffff','edgeLabelBackground':'#ffffff','fontFamily':'Arial','fontSize':'11px'}}}%%
flowchart TD
    a0(["Mulai"])
    a1["Buka /login"]
    a2{"Sesi QR masih aktif?"}
    a3["Buka /login?staff=1<br/>untuk hapus sesi QR"]
    a4["Isi username &amp; password"]
    a5{"Kredensial &amp; role staf valid?"}
    a6["Tampilkan pesan error"]
    a7["Dashboard admin"]
    a8{"Pilih pekerjaan"}
    b1["Buka menu master data"]
    b2["Cari / Tambah / Edit"]
    b3["Isi form; validasi server"]
    b4{"Data valid?"}
    b5["Simpan; kembali ke index"]
    c1["Kelola meja &amp; QR"]
    c2["Nomor unik, kapasitas, status aktif"]
    c3["Sistem buat QR<br/>/?meja_id={id}"]
    c4["Tampilkan QR / salin URL"]
    c5["Pelanggan scan QR"]
    d1["Pantau meja live / buat POS"]
    d2{"Meja kosong?"}
    d3["Form POS: produk, jumlah,<br/>opsi, meja, metode"]
    d4["Sistem: pending, stok -,<br/>pajak 10%"]
    d5{"Metode QRIS?"}
    d6["Halaman QRIS Midtrans"]
    d7["Bayar tunai / antrean kasir"]
    d8["Detail transaksi: status,<br/>pembayaran, struk"]
    e1["Export Laporan"]
    e2["GET /laporan/export"]
    e3{"Parameter lengkap?"}
    e4["Gunakan bulan berjalan WIB"]
    e5["Hitung data non-cancelled"]
    e6["Unduh PDF"]
    h1["Kembali ke dashboard"]
    h2(["Logout → /login"])
    a0 --> a1
    a1 --> a2
    a2 -->|Ya| a3
    a2 -->|Tidak| a4
    a3 --> a4
    a4 --> a5
    a5 -->|Tidak| a6
    a5 -->|Ya| a7
    a6 -->|Ulangi| a1
    a7 --> a8
    a8 -->|Master data| b1
    a8 -->|Meja &amp; QR| c1
    a8 -->|POS / transaksi| d1
    a8 -->|Laporan| e1
    b1 --> b2
    b2 --> b3
    b3 --> b4
    b4 -->|Ya| b5
    b4 -->|Tidak| b2
    c1 --> c2
    c2 --> c3
    c3 --> c4
    c4 --> c5
    d1 --> d2
    d2 -->|Ya| d3
    d2 -->|Tidak| d8
    d3 --> d4
    d4 --> d5
    d5 -->|Ya| d6
    d5 -->|Tidak| d7
    d6 -->|Lunas / gagal| d8
    d7 -->|Selesai| d8
    e1 --> e2
    e2 --> e3
    e3 -->|Tidak| e4
    e3 -->|Ya| e5
    e4 --> e5
    e5 --> e6
    b5 -->|Selesai| h1
    c5 -->|Selesai| h1
    d8 -->|Selesai| h1
    e6 -->|Selesai| h1
    h1 -->|Pilih lagi| a8
    h1 -->|Selesai| h2
    classDef bw fill:#ffffff,stroke:#000000,color:#000000,stroke-width:1.5px;
    class a0,a1,a2,a3,a4,a5,a6,a7,a8,b1,b2,b3,b4,b5,c1,c2,c3,c4,c5,d1,d2,d3,d4,d5,d6,d7,d8,e1,e2,e3,e4,e5,e6,h1,h2 bw;
```

## 2. File draw.io

Buka [`admin.drawio`](./diagrams/admin.drawio) untuk versi draw.io dari flowchart ini. File tersebut berisi satu halaman, satu alur lengkap per role, dan hanya memakai hitam putih.
