# Activity Diagram BagiKopi

Folder ini memakai layout activity diagram tiga lane mengikuti contoh
`Activity_admin.drawio`: **Database**, **Sistem Web**, dan peran.

| Peran | Activity Diagram Markdown | File draw.io |
|---|---|---|
| Admin | [`admin.md`](./admin.md) | [`admin.drawio`](./diagrams/admin.drawio) |
| Manager | [`manager.md`](./manager.md) | [`manager.drawio`](./diagrams/manager.drawio) |
| Kasir | [`kasir.md`](./kasir.md) | [`kasir.drawio`](./diagrams/kasir.drawio) |
| Pelanggan | [`pelanggan.md`](./pelanggan.md) | [`pelanggan.drawio`](./diagrams/pelanggan.drawio) |

## Aturan Diagram

- Satu file `.drawio` untuk satu role.
- Satu file `.drawio` hanya berisi satu halaman.
- Diagram memakai tiga swimlane vertikal, bukan tabel langkah.
- Node awal/akhir berbentuk elips hitam; aktivitas berbentuk kotak bulat putih; keputusan berbentuk belah ketupat putih.
- Error ditandai dengan garis putus-putus.
- Panah ortogonal berwarna hitam dengan label putih.
- Alur QRIS kasir dan status pelanggan tetap tergabung di file role masing-masing.
- Tidak ada berkas SVG; Markdown memakai blok Mermaid dan tautan `.drawio`.

## Cara Membaca

- Baris panah antarlane menunjukkan perpindahan kontrol antara aktor, sistem, dan database.
- Belah ketupat menunjukkan keputusan.
- Garis putus-putus pada kotak menunjukkan kondisi error.
- Blok Mermaid di file `.md` adalah sumber tekstual; file `.drawio` adalah versi yang dapat diedit di draw.io.
