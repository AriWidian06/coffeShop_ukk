# Responsive & Mobile Quality Check

Folder ini berisi **potongan kode (excerpt)** yang berkaitan dengan responsivitas aplikasi customer (mobile) dan area staff (desktop) pada aplikasi Bagi Kopi.

Bukan salinan penuh file — hanya bagian yang relevan. File asli tetap berada di lokasi aslinya dan tidak diubah.

## Kebijakan Responsivitas

- Pelanggan: mobile-first, fokus layar smartphone portrait/landscape.
- Admin, kasir, dan manager: desktop-first, minimum baseline `1024 px`.
- Tabel admin dipertahankan sebagai width-fixed + `overflow-x-auto`, bukan diubah jadi card mobile kecuali ada use case mobile.

## Daftar Isi

| File di folder ini | Sumber asli | Fokus |
|---|---|---|
| `welcome.viewport.blade.php` | `resources/views/welcome.blade.php` | Viewport scale dan zoom accessibility customer. |
| `welcome.category-pills.blade.php` | `resources/views/welcome.blade.php` | Horizontal filter, `overflow-x-auto`, affordance scroll. |
| `welcome.product-card.blade.php` | `resources/views/welcome.blade.php` | Card produk mobile, gambar, tap target. |
| `welcome.bottom-nav.blade.php` | `resources/views/welcome.blade.php` | Bottom sheet cart, fixed nav, safe area. |
| `user.keranjang.checkout.blade.php` | `resources/views/user/keranjang.blade.php` | Checkout full-page, opsi pesanan, fixed payment. |
| `user.status.mobile.blade.php` | `resources/views/user/status.blade.php` | Status live mobile, loading/empty, timeline. |
| `user.costume.modal.blade.php` | `resources/views/user/costume.blade.php` | Modal kustomisasi dan pemilihan opsi. |
| `staff-layout.sidebar.blade.php` | `layouts/app.blade.php`, `layouts/manager.blade.php`, `layouts/kasir.blade.php` | Sidebar desktop-only. |
| `app-css.safe-area.css` | `resources/css/app.css` | Utility safe area dan scroll. |

## Ringkasan Mekanisme

### Pelanggan (Mobile)
- Viewport umum memakai `width=device-width, initial-scale=1`.
- Halaman customer utama memakai shell `max-w-md mx-auto`.
- Bottom navigation memakai `fixed bottom-0 left-0 right-0`, `max-w-md mx-auto`, dan `safe-area-pb`.
- Filter kategori memakai `overflow-x-auto hide-scrollbar`.
- Product card memakai layout horizontal, gambar `w-24 h-24`, dan tap target icon `w-8 h-8`.

### Staff (Desktop)
- Sidebar bersifat `hidden lg:block`.
- Main content memakai `lg:ml-64`.
- Dashboard dan tabel CRUD dilindungi dengan `overflow-x-auto`.
- POS menu memakai grid responsive (`grid-cols-2 md:grid-cols-3 lg:grid-cols-4`).

## Temuan Kualitas Mobile

| ID | Severity | Lokasi | Temuan | Rekomendasi |
|---|---|---|---|---|
| R-01 | Major | `welcome.blade.php` viewport | Lock zoom dengan `maximum-scale=1, user-scalable=no`. | Hapus pembatasan; biarkan zoom pengguna. |
| R-02 | Medium | Category pills | Scroll disembunyikan (`hide-scrollbar`) tanpa affordance visual. | Tambahkan fade, arrow kecil, atau indikator geser. |
| R-03 | Medium | Product card | Tap target ikon `w-8 h-8` di bawah target ideal. | Gunakan target sentuh minimal 44 px. |
| R-04 | Medium | `keranjang.blade.php` | Payment button dan navbar tambahan dapat menubruki layar pendek. | Hitung tinggi total fixed layer; pertahankan `pb-24/pb-28`. |
| R-05 | Medium | `costume.blade.php` | Modal kustomisasi berpotensi tertutup keyboard virtual. | Gunakan bottom sheet dengan `max-h-[85vh]` dan tombol aksi sticky. |
| R-06 | Minor | `app.css` / Blade | `safe-area-pb` hanya didefinisikan/inline pada halaman tertentu. | Pindahkan ke CSS global agar reusable. |
| R-07 | Minor / by design | Customer shell | `max-w-md` membuat tampilan customer tetap sempit di tablet/desktop. | Pertahankan jika target mobile-only, atau tambahkan breakpoint. |
| R-08 | Info / by policy | Staff layout | Sidebar staff tidak tampil sebelum `lg`. | Dokumentasikan staff console sebagai desktop-only atau sediakan drawer. |
| R-09 | Minor | Badge dan label kecil | Banyak `text-[10px]`, `text-[11px]`, `text-xs`. | Pastikan informasi aksi utama tidak terlalu kecil. |
| R-10 | Low / by design | Tabel CRUD | Tabel membutuhkan scroll horizontal. | Pertahankan `overflow-x-auto`; test tombol aksi pada width 320 px. |

## Aturan Pakai Pola Responsif
- Customer mobile: prioritas layout vertikal card dan bottom sheet.
- Admin/Kasir/Manager desktop: pertahankan desktop table dengan `overflow-x-auto`.
- Patokan responsif:
  - `md:hidden` untuk tampilan mobile.
  - `hidden md:block` untuk tampilan desktop/tablet.
  - Tombol aksi mobile sebaiknya `w-full`, `block`, `text-center`, dan memiliki area sentuh nyaman.

## Catatan Audit
- Folder ini berisi excerpt, bukan copy penuh view atau CSS.
- Temuan berasal dari inspection code Blade/Tailwind, pengujian device fisik tidak dilakukan.
- Perubahan recommended tidak diimplementasikan di file asli pada folder ini.
