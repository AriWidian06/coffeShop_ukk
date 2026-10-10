@extends('layouts.app')

@section('content')
    <h1>Dashboard Admin</h1>
    <p>Kelola data umum, produk, meja, supplier, dan karyawan.</p>

    <div style="display:grid; gap:1rem; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-top: 1.5rem;">
        <a class="button" href="{{ route('karyawans.index') }}">Karyawan</a>
        <a class="button" href="{{ route('produks.index') }}">Produk</a>
        <a class="button" href="{{ route('mejas.index') }}">Meja</a>
        <a class="button" href="{{ route('suppliers.index') }}">Supplier</a>
        <a class="button" href="{{ route('kategori_produks.index') }}">Kategori Produk</a>

    </div>

    <div style="margin-top:2rem; background:#fff; padding:1rem; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
        <h2>Admin Panel</h2>
        <p>Admin memiliki akses penuh ke pengelolaan sistem.</p>
    </div>
@endsection
