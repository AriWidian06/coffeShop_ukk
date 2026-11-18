# Flowchart BagiKopi

Folder ini berisi flowchart per role secara terpisah dari dokumentasi
[`../userflow/README.md`](../userflow/README.md), supaya file user flow tidak ditimpa.

| Peran | Flowchart Markdown | File draw.io |
|---|---|---|
| Admin | [`admin.md`](./admin.md) | [`admin.drawio`](./diagrams/admin.drawio) |
| Manager | [`manager.md`](./manager.md) | [`manager.drawio`](./diagrams/manager.drawio) |
| Kasir | [`kasir.md`](./kasir.md) | [`kasir.drawio`](./diagrams/kasir.drawio) |
| Pelanggan | [`pelanggan.md`](./pelanggan.md) | [`pelanggan.drawio`](./diagrams/pelanggan.drawio) |

## Aturan Diagram

- Satu file `.drawio` untuk satu role.
- Satu file `.drawio` hanya berisi satu halaman.
- Flowchart memakai node dan panah biasa, bukan tabel atau swimlane.
- Alur QRIS kasir tergabung di flowchart kasir.
- Alur status dan pembayaran pelanggan tergabung di flowchart pelanggan.
- Semua node, garis, dan teks hanya memakai hitam dan putih.
- Tidak ada berkas SVG; Markdown memakai blok Mermaid dan tautan `.drawio`.

## Cara Membaca

- Bentuk oval menunjukkan awal atau akhir alur.
- Kotak persegi menunjukkan aksi atau layar.
- Belah ketupat menunjukkan keputusan.
- Label pada panah menunjukkan kondisi atau metode yang dipilih.
- Blok Mermaid di file `.md` adalah sumber tekstual; file `.drawio` adalah versi yang dapat diedit di draw.io.
