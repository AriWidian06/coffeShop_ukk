# Activity Diagram Admin — BagiKopi

| Item | Keterangan |
|---|---|
| **Peran** | Admin |
| **Sumber narasi** | [`../userflow/admin.md`](../userflow/admin.md) |
| **File draw.io** | [`./diagrams/admin.drawio`](./diagrams/admin.drawio) |
| **Gaya** | Activity diagram 3 lane, hitam putih |

Autentikasi admin, pilih menu manajemen, kelola karyawan/produk/meja/QR, POS dan transaksi, lalu ekspor laporan.

## 1. Activity Diagram Markdown

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','secondaryColor':'#ffffff','tertiaryColor':'#ffffff','edgeLabelBackground':'#ffffff','fontFamily':'Arial','fontSize':'11px'}}}%%
flowchart LR
  subgraph DB["Database (MySQL)"]
    direction TB
    dbAkun("Simpan Akun ke Database")
    dbProduk("Simpan Produk ke Database")
    dbMeja("Simpan Meja &amp; QR ke Database")
    dbTrans("Simpan Transaksi ke Database")
    dbLaporan("Baca Data Transaksi")
  end
  subgraph SYS["Sistem Web (Laravel)"]
    direction TB
    tampil("Tampil Panel Dashboard Utama")
    bukaK("Buka Halaman Data Karyawan")
    bukaM("Buka Halaman Menu &amp; Stok")
    valAkun{"Validasi Input Akun"}
    valProd{"Validasi Data Produk"}
    err1("Tampilkan Pesan Error")
    err2("Tampilkan Pesan Error")
    bukaMeja("Buka Halaman Meja &amp; QR")
    bukaPOS("Buka Form POS / Transaksi")
    laporan("Hitung Laporan &amp; Unduh PDF")
  end
  subgraph ROLE["Admin"]
    direction TB
    start(("Mulai"))
    auth("Autentikasi Administrator")
    pilih{"Pilih Menu Manajemen"}
    kTambah("Klik Tambah Karyawan")
    iAkun("Input Data: Nama, Role, Username, Password")
    kSimpanK("Klik Simpan Karyawan")
    iProduk("Input Data: Nama, Harga, Stok, Foto")
    kSimpanP("Klik Simpan Produk")
    kMeja("Kelola Meja &amp; QR")
    kPOS("Buat POS / Lihat Transaksi")
    kExport("Klik Export Laporan")
    fin(("Selesai"))
  end
  start --> auth
  auth --> tampil
  tampil --> pilih
  pilih -->|Kelola Karyawan| bukaK
  pilih -->|Kelola Menu/Produk| bukaM
  pilih -->|Meja &amp; QR| bukaMeja
  pilih -->|POS/Transaksi| bukaPOS
  pilih -->|Laporan| laporan
  bukaK --> kTambah
  kTambah --> iAkun
  iAkun --> kSimpanK
  kSimpanK --> valAkun
  bukaM --> iProduk
  iProduk --> kSimpanP
  kSimpanP --> valProd
  valAkun -->|Data Valid| dbAkun
  valAkun -->|Username Terpakai / Kosong| err1
  err1 --> iAkun
  valProd -->|Data Valid| dbProduk
  valProd -->|Data Tidak Lengkap| err2
  err2 --> iProduk
  kMeja --> bukaMeja
  bukaMeja --> dbMeja
  kPOS --> bukaPOS
  bukaPOS --> dbTrans
  kExport --> laporan
  laporan --> dbLaporan
  dbAkun --> fin
  dbProduk --> fin
  dbMeja --> fin
  dbTrans --> fin
  dbLaporan --> fin
  classDef bw fill:#ffffff,stroke:#000000,color:#000000,stroke-width:1.5px;
  classDef terminal fill:#000000,stroke:#000000,color:#000000,stroke-width:2px;
  class start,auth,pilih,kTambah,iAkun,kSimpanK,iProduk,kSimpanP,kMeja,kPOS,kExport,fin,tampil,bukaK,bukaM,valAkun,valProd,err1,err2,bukaMeja,bukaPOS,laporan,dbAkun,dbProduk,dbMeja,dbTrans,dbLaporan bw;
  class start,fin terminal;
  style DB fill:#ffffff,stroke:#000000,color:#000000;
  style SYS fill:#ffffff,stroke:#000000,color:#000000;
  style ROLE fill:#ffffff,stroke:#000000,color:#000000;
```

## 2. File draw.io

Buka [`admin.drawio`](./diagrams/admin.drawio) untuk versi draw.io dari activity diagram ini. File tersebut berisi satu halaman, tiga lane, dan hanya memakai hitam putih.
