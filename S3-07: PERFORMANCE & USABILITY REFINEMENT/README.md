# S3-07: Performance & Usability Refinement

Folder ini berisi potongan SQL/Migration, kode JavaScript, dan dokumentasi refinement performa & usability. File asli proyek tidak diubah.

## A. Optimasi Indeks Basis Data

Contoh dari task menggunakan `reservations`, `rooms`, `check_in`, dan `check_out`. Pada projek Bagi Kopi, schema actual berbeda, sehingga index diadaptasi ke:

- `transaksis`: `karyawan_id`, `meja_id`, `status_pesanan`, `waktu_transaksi`, `sumber_pesanan`
- `detail_transaksis`: `transaksi_id`, `produk_id`
- `produks`: `kategori_produk_id`, `status_aktif`, `stock`
- `produk_opsis`: `produk_id`
- `pembayarans`: `metode_pembayaran`, `status_pembayaran`

Migration referensi:
- `2026_11_08_000000_add_performance_indexes.php`

## B. Anti-Spam Double Click Submit

Form keranjang/checkout, login, produk, pembayaran, dan kasir dapat mengunci tombol submit setelah klik pertama.

Pattern:
- Tambahkan class `js-submit-lock` pada form.
- Tambahkan teks dan spinner:
  - `<span data-btn-text>Simpan</span>`
  - `<span data-btn-spinner class="hidden"><i class="fa-solid fa-spinner fa-spin"></i> Memproses...</span>`
- JavaScript menonaktifkan tombol segera dan menampilkan spinner.

Contoh penerapan pada form:
```blade
<form method="POST" action="{{ route('produks.store') }}" class="js-submit-lock">
    @csrf
    <!-- input -->
    <button type="submit" class="... flex items-center justify-center gap-2">
        <span data-btn-text>Simpan Produk</span>
        <span data-btn-spinner class="hidden">
            <i class="fa-solid fa-spinner fa-spin"></i> Memproses...
        </span>
    </button>
</form>
```

Kasir/report aksi dynamic dapat memakai helper `lockPosSubmitButton(button)` dari `anti-double-click.js`.

## Ringkasan Refinement
| Area | Nilai |
|---|---|
| Query transaction lookup | Dipercepat dengan index gabungan status + waktu. |
| Product listing/filter | Dipercepat dengan index kategori/status/stock. |
| Checkout/payment | Tombol submit tidak dapat dipliklik berkali-kali. |
| Deployment | Pastikan migration dijalankan otomatis pada startup. |

## Catatan
- Index baru tidak duplikat dengan FK index yang mungkin sudah ada; gunakan nama index eksplisit.
- Saat apply ke proyek, jalankan `php artisan migrate`.
- Saat rollback, gunakan `down()` yang drop index.
