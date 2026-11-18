# Activity Diagram Manager — BagiKopi

| Item | Keterangan |
|---|---|
| **Peran** | Manager |
| **Sumber narasi** | [`../userflow/manager.md`](../userflow/manager.md) |
| **File draw.io** | [`./diagrams/manager.drawio`](./diagrams/manager.drawio) |
| **Gaya** | Activity diagram 3 lane, hitam putih |

Autentikasi manager, pilih periode, muat data, membaca statistik, menelusuri transaksi, lalu mengekspor PDF.

## 1. Activity Diagram Markdown

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','secondaryColor':'#ffffff','tertiaryColor':'#ffffff','edgeLabelBackground':'#ffffff','fontFamily':'Arial','fontSize':'11px'}}}%%
flowchart LR
  subgraph DB["Database (MySQL)"]
    direction TB
    dbTrans("Baca Data Transaksi")
    dbLaporan("Agenda Data Periode")
  end
  subgraph SYS["Sistem Web (Laravel)"]
    direction TB
    dashboard("Tampil Dashboard Manager")
    validasi{"Validasi Parameter Periode"}
    fallback("Fallback Bulanan / Hari Ini")
    muat("Muat Data Periode")
    statistik("Statistik, Grafik &amp; Top Produk")
    daftar("Tampil Daftar Transaksi")
    detail("Detail Transaksi Read-Only")
    pdf("Buat Laporan PDF")
  end
  subgraph ROLE["Manager"]
    direction TB
    start(("Mulai"))
    auth("Autentikasi Manager")
    pilih{"Pilih Periode &amp; Tanggal"}
    lihat("Klik Lihat Transaksi")
    export("Klik Export PDF")
    logout("Keluar / Log Out")
    fin(("Selesai"))
  end
  start --> auth
  auth --> dashboard
  dashboard --> pilih
  pilih -->|Lihat Transaksi| lihat
  pilih -->|Export PDF| export
  pilih -->|Selesai| logout
  lihat --> daftar
  daftar --> detail
  export --> pdf
  pdf --> dbLaporan
  logout --> fin
  validasi -->|Tidak Valid| fallback
  fallback --> muat
  muat --> dbTrans
  dbTrans --> statistik
  statistik --> fin
  detail --> fin
  dbLaporan --> fin
  classDef bw fill:#ffffff,stroke:#000000,color:#000000,stroke-width:1.5px;
  classDef terminal fill:#000000,stroke:#000000,color:#000000,stroke-width:2px;
  class start,auth,pilih,lihat,export,logout,fin,dashboard,validasi,fallback,muat,statistik,daftar,detail,pdf,dbTrans,dbLaporan bw;
  class start,fin terminal;
  style DB fill:#ffffff,stroke:#000000,color:#000000;
  style SYS fill:#ffffff,stroke:#000000,color:#000000;
  style ROLE fill:#ffffff,stroke:#000000,color:#000000;
```

## 2. File draw.io

Buka [`manager.drawio`](./diagrams/manager.drawio) untuk versi draw.io dari activity diagram ini. File tersebut berisi satu halaman, tiga lane, dan hanya memakai hitam putih.
