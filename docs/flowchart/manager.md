# Flowchart Manager — BagiKopi

| Item | Keterangan |
|---|---|
| **Peran** | Manager |
| **Sumber narasi** | [`../userflow/manager.md`](../userflow/manager.md) |
| **File draw.io** | [`./diagrams/manager.drawio`](./diagrams/manager.drawio) |
| **Gaya** | Flowchart biasa, hitam putih |

Login, pemilihan periode, statistik, penelusuran transaksi, dan ekspor PDF.

## 1. Flowchart Mermaid

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','secondaryColor':'#ffffff','tertiaryColor':'#ffffff','edgeLabelBackground':'#ffffff','fontFamily':'Arial','fontSize':'11px'}}}%%
flowchart TD
    g0(["Mulai"])
    g1["Buka /login"]
    g2{"Sesi QR masih aktif?"}
    g3["Buka /login?staff=1"]
    g4["Isi kredensial manager"]
    g5{"Login valid &amp; role manager?"}
    g6["Tampilkan error login"]
    g7["Dashboard manager"]
    g8["Pilih periode: harian / mingguan / bulanan"]
    g9["Pilih tanggal / panah / Hari Ini"]
    g10{"Parameter valid?"}
    g11["Fallback: bulanan / hari ini"]
    g12["Muat data periode (WIB)"]
    g13["Snapshot hari ini"]
    g14["Kartu pendapatan,<br/>transaksi, ATV"]
    g15["Grafik per jam + top produk"]
    g16["Distribusi metode pembayaran"]
    g17{"Analisis lanjutan?"}
    g18["Lihat Transaksi"]
    g19["Detail transaksi read-only"]
    g20["Export PDF"]
    g21["GET /laporan/export<br/>?periode=&amp;tanggal="]
    g22["Unduh PDF A4 landscape"]
    g23(["Logout → /login"])
    g0 --> g1
    g1 --> g2
    g2 -->|Ya| g3
    g2 -->|Tidak| g4
    g3 --> g4
    g4 --> g5
    g5 -->|Tidak| g6
    g5 -->|Ya| g7
    g6 -->|Ulangi| g1
    g7 --> g8
    g8 --> g9
    g9 --> g10
    g10 -->|Tidak| g11
    g10 -->|Ya| g12
    g11 --> g12
    g12 --> g13
    g12 --> g14
    g12 --> g15
    g12 --> g16
    g13 --> g17
    g14 --> g17
    g15 --> g17
    g16 --> g17
    g17 -->|Lihat transaksi| g18
    g17 -->|Export PDF| g20
    g17 -->|Selesai| g23
    g18 --> g19
    g19 --> g23
    g20 --> g21
    g21 --> g22
    g22 --> g23
    classDef bw fill:#ffffff,stroke:#000000,color:#000000,stroke-width:1.5px;
    class g0,g1,g2,g3,g4,g5,g6,g7,g8,g9,g10,g11,g12,g13,g14,g15,g16,g17,g18,g19,g20,g21,g22,g23 bw;
```

## 2. File draw.io

Buka [`manager.drawio`](./diagrams/manager.drawio) untuk versi draw.io dari flowchart ini. File tersebut berisi satu halaman, satu alur lengkap per role, dan hanya memakai hitam putih.
