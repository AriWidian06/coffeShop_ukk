<!-- Excerpt dari: resources/views/**/*.blade.php -->
<!-- Fokus: output escaping otomatis dengan Blade. -->

<!-- AMAN: Laravel melakukan escape HTML entities secara default. -->
<h1>{{ $produk->nama_produk }}</h1>
<p>{{ $produk->deskripsi }}</p>
<div data-name="{{ $produk->nama_produk }}" data-options='@json($produk->opsis)'></div>

<!-- Input yang mengandung <script> akan ditampilkan sebagai teks biasa, bukan dieksekusi. -->

<!-- POLA BERBAHAYA (JANGAN DIPAKAI): -->
<!-- {!! $produk->deskripsi !!} -->
<!-- {!! request('q') !!} -->
```