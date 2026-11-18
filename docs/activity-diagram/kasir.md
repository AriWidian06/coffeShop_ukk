# Activity Diagram Kasir — BagiKopi

| Item | Keterangan |
|---|---|
| **Peran** | Kasir |
| **Sumber narasi** | [`../userflow/kasir.md`](../userflow/kasir.md) |
| **File draw.io** | [`./diagrams/kasir.drawio`](./diagrams/kasir.drawio) |
| **Gaya** | Activity diagram 3 lane, hitam putih |

Autentikasi kasir, memilih area kerja, membuat dan memproses pesanan, mencatat pembayaran tunai/QRIS, lalu mencetak struk.

## 1. Activity Diagram Markdown

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','secondaryColor':'#ffffff','tertiaryColor':'#ffffff','edgeLabelBackground':'#ffffff','fontFamily':'Arial','fontSize':'11px'}}}%%
flowchart LR
  subgraph DB["Database (MySQL)"]
    direction TB
    dbStok("Simpan Transaksi &amp; Kurangi Stok")
    dbBayar("Simpan Pembayaran")
    dbQris("Sinkron Status Midtrans")
  end
  subgraph SYS["Sistem Web / POS (Laravel)"]
    direction TB
    dashboard("Tampil Dashboard Kasir")
    keranjang("Keranjang + Pajak 10%")
    validasi{"Validasi Meja &amp; Stok"}
    pending("Transaksi Pending")
    proses("Proses Status")
    inspector("Inspector Meja / Antrean")
    bayarValid{"Validasi Pembayaran"}
    paid("Paid → Completed")
    token("Buat Snap Token Midtrans")
    cek{"Status Midtrans"}
    completed("Transaksi Completed")
    cancelled("cancelled; stok/promo kembali")
  end
  subgraph ROLE["Kasir"]
    direction TB
    start(("Mulai"))
    login("Login Kasir")
    pilih{"Pilih Area Kerja"}
    pos("Kasir &amp; Menu: Pilih Produk")
    denah("Denah Meja")
    aktif("Pesanan Aktif")
    bayar("Catat Pembayaran")
    qris("Buka QRIS Otomatis")
    struk("Cetak Struk")
    riwayat("Riwayat Transaksi")
    fin(("Selesai"))
  end
  start --> login
  login --> dashboard
  dashboard --> pilih
  pilih -->|Buat Pesanan| pos
  pilih -->|Denah Meja| denah
  pilih -->|Pesanan Aktif| aktif
  pilih -->|QRIS Otomatis| qris
  pilih -->|Riwayat| riwayat
  pos --> keranjang
  keranjang --> validasi
  validasi -->|Valid| pending
  validasi -->|Tidak Valid| keranjang
  pending --> dbStok
  dbStok --> proses
  denah --> inspector
  inspector --> aktif
  aktif --> proses
  bayar --> bayarValid
  bayarValid -->|Valid| dbBayar
  bayarValid -->|Gagal| bayar
  dbBayar --> paid
  paid --> struk
  qris --> token
  token --> cek
  cek -->|capture/settlement| completed
  cek -->|deny/expire| cancelled
  cek -->|pending| token
  completed --> struk
  cancelled --> riwayat
  token --> dbQris
  dbQris --> cek
  struk --> riwayat
  riwayat --> fin
  classDef bw fill:#ffffff,stroke:#000000,color:#000000,stroke-width:1.5px;
  classDef terminal fill:#000000,stroke:#000000,color:#000000,stroke-width:2px;
  class start,login,pilih,pos,denah,aktif,bayar,qris,struk,riwayat,fin,dashboard,keranjang,validasi,pending,proses,inspector,bayarValid,paid,token,cek,completed,cancelled,dbStok,dbBayar,dbQris bw;
  class start,fin terminal;
  style DB fill:#ffffff,stroke:#000000,color:#000000;
  style SYS fill:#ffffff,stroke:#000000,color:#000000;
  style ROLE fill:#ffffff,stroke:#000000,color:#000000;
```

## 2. File draw.io

Buka [`kasir.drawio`](./diagrams/kasir.drawio) untuk versi draw.io dari activity diagram ini. File tersebut berisi satu halaman, tiga lane, dan hanya memakai hitam putih.
