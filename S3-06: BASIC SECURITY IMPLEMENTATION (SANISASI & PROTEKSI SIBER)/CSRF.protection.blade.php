<!-- Excerpt dari: resources/views/**/*.blade.php -->
<!-- Fokus: proteksi CSRF pada form mutation. -->

@php
    // Laravel mewajibkan token CSRF untuk form POST/PUT/DELETE.
@endphp

<form method="POST" action="{{ route('login.store') }}">
    @csrf
    <input type="text" name="username" value="{{ old('username') }}" required>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>

<form method="POST" action="{{ route('produks.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="text" name="nama_produk" required>
    <input type="file" name="gambar" accept="image/*">
    <button type="submit">Simpan</button>
</form>

<!-- GET request tidak perlu token bila hanya reading data. -->
<!-- Endpoint publik/eksternal seperti callback payment dikecualikan di bootstrap/app.php dan
     tetap harus divalidasi lewat signature verification. -->
