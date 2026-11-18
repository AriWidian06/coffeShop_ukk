# ERD & Database Schema BagiKopi

Dokumen ERD dan database schema dipisah menjadi dua berkas, masing-masing dengan
satu berkas draw.io. Keduanya menggabungkan seluruh peran (admin, manager, kasir,
dan pelanggan).

| Dokumen | Markdown | File draw.io |
|---|---|---|
| ERD | [`erd.md`](./erd.md) | [`erd.drawio`](./diagrams/erd.drawio) |
| Database schema | [`database-schema.md`](./database-schema.md) | [`database-schema.drawio`](./diagrams/database-schema.drawio) |

## Perbedaan

- **`erd.md` + `erd.drawio`** berisi entity dan relasi beserta kardinalitasnya.
- **`database-schema.md` + `database-schema.drawio`** berisi struktur kolom, tipe data, dan kunci tanpa panah relasi.

## Aturan

- Satu berkas `.md` = satu berkas `.drawio`.
- Satu berkas `.drawio` hanya satu halaman.
- Diagram memakai bentuk biasa (kotak entity dan panah), bukan swimlane.
- Semua elemen hanya memakai hitam dan putih.
- Tidak ada berkas SVG.
- Sumber skema: [`../../database/migrations`](../../database/migrations).
