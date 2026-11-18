# Flowchart Kasir — BagiKopi

| Item | Keterangan |
|---|---|
| **Peran** | Kasir |
| **Sumber narasi** | [`../userflow/kasir.md`](../userflow/kasir.md) |
| **File draw.io** | [`./diagrams/kasir.drawio`](./diagrams/kasir.drawio) |
| **Gaya** | Flowchart biasa, hitam putih |

POS, denah meja, pesanan aktif, pembayaran tunai, QRIS otomatis, struk, dan riwayat.

## 1. Flowchart Mermaid

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','secondaryColor':'#ffffff','tertiaryColor':'#ffffff','edgeLabelBackground':'#ffffff','fontFamily':'Arial','fontSize':'11px'}}}%%
flowchart TD
    k0(["Mulai"])
    k1["Buka /login"]
    k2{"Sesi QR masih aktif?"}
    k3["Buka /login?staff=1"]
    k4["Isi kredensial kasir"]
    k5{"Login valid &amp; role kasir?"}
    k6["Tampilkan error login"]
    k7["Dashboard kasir"]
    k8{"Pilih area kerja"}
    m1["Kasir &amp; Menu: pilih produk,<br/>opsi, jumlah"]
    m2["Keranjang + pajak 10%"]
    m3{"Tipe pesanan?"}
    m4["Pilih meja aktif"]
    m5["Take-away: tanpa meja"]
    m6["POST /transaksis (cash)"]
    m7{"Meja &amp; stok valid?"}
    m8["Error meja/stok;<br/>kembali ke keranjang"]
    m9["Transaksi pending; stok -"]
    f1["Denah Meja"]
    f2{"Meja terisi?"}
    f3["Klik meja kosong → POS<br/>dengan meja terpilih"]
    f4["Inspector: item, total,<br/>status, pembayaran"]
    f5["Aksi: ubah status / bayar / struk"]
    f6["Sinkronisasi 15 detik / manual"]
    o1["Pesanan Aktif (FIFO)"]
    o2["Filter status / pencarian"]
    o3["pending → prosesed → ready"]
    o4{"Sudah dibayar?"}
    o5["Bayar &amp; Selesai →<br/>modal pembayaran"]
    o6{"Metode pembayaran?"}
    o7["Tunai: uang diterima,<br/>kembalian"]
    o8["QRIS manual<br/>(bukan gateway)"]
    o9["POST /pembayarans"]
    o10{"Pembayaran valid?"}
    o11["Error: sudah dibayar /<br/>kurang bayar"]
    o12["Paid; transaksi completed"]
    o13["Cetak struk"]
    o14["Riwayat Transaksi"]
    o15(["Logout → /login"])
    q1["POS standalone / buka QRIS"]
    q2["GET /kasir/transaksi/{id}/qris"]
    q3{"Snap token berhasil?"}
    q4["Kembali + pesan error"]
    q5["Halaman QR + Bayar Sekarang"]
    q6["Pelanggan bayar via Snap"]
    q7{"Status Midtrans?"}
    q8["capture / settlement → paid"]
    q9["pending"]
    q10["Cek status / tunggu webhook"]
    q11["deny / expire / cancel"]
    q12["Transaksi completed"]
    q13["cancelled; stok/promo kembali"]
    k0 --> k1
    k1 --> k2
    k2 -->|Ya| k3
    k2 -->|Tidak| k4
    k3 --> k4
    k4 --> k5
    k5 -->|Tidak| k6
    k5 -->|Ya| k7
    k6 -->|Ulangi| k1
    k7 --> k8
    k8 -->|Buat pesanan| m1
    k8 -->|Denah Meja| f1
    k8 -->|Pesanan Aktif| o1
    k8 -->|QRIS otomatis| q1
    k8 -->|Riwayat| o14
    m1 --> m2
    m2 --> m3
    m3 -->|Dine-in| m4
    m3 -->|Take-away| m5
    m4 --> m6
    m5 --> m6
    m6 --> m7
    m7 -->|Tidak| m8
    m7 -->|Ya| m9
    m8 -->|Perbaiki| m2
    m9 -->|Masuk antrean| o1
    f1 --> f2
    f2 -->|Tidak| f3
    f2 -->|Ya| f4
    f4 --> f5
    f5 --> o3
    f6 -->|Sinkron| f2
    o1 --> o2
    o2 --> o3
    o3 --> o4
    o4 -->|Belum| o5
    o4 -->|Ya| o12
    o5 --> o6
    o6 -->|Tunai| o7
    o6 -->|QRIS manual| o8
    o7 --> o9
    o8 --> o9
    o9 --> o10
    o10 -->|Tidak| o11
    o10 -->|Ya| o12
    o11 -->|Ulangi| o5
    o12 --> o13
    o13 --> o14
    o14 --> o15
    q1 --> q2
    q2 --> q3
    q3 -->|Tidak| q4
    q3 -->|Ya| q5
    q4 -->|Ulangi| q1
    q5 --> q6
    q6 --> q7
    q7 -->|capture / settlement| q8
    q7 -->|pending| q9
    q7 -->|deny / expire| q11
    q9 --> q10
    q10 -->|Cek lagi| q7
    q8 --> q12
    q11 --> q13
    q12 -->|Struk| o13
    q13 -->|Riwayat| o14
    classDef bw fill:#ffffff,stroke:#000000,color:#000000,stroke-width:1.5px;
    class k0,k1,k2,k3,k4,k5,k6,k7,k8,m1,m2,m3,m4,m5,m6,m7,m8,m9,f1,f2,f3,f4,f5,f6,o1,o2,o3,o4,o5,o6,o7,o8,o9,o10,o11,o12,o13,o14,o15,q1,q2,q3,q4,q5,q6,q7,q8,q9,q10,q11,q12,q13 bw;
```

## 2. File draw.io

Buka [`kasir.drawio`](./diagrams/kasir.drawio) untuk versi draw.io dari flowchart ini. File tersebut berisi satu halaman, satu alur lengkap per role, dan hanya memakai hitam putih.
