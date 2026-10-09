@extends('layouts.app')
@section('content')
    <h1>Tambah Kategori</h1>
    <form class="form" method="post" action="{{ route('kategori-produks.store') }}">
        @csrf
        @include('kategori_produks.form')
        <div style="margin-top: 1.5rem;">
            <button type="submit">Simpan Kategori</button>
            <a href="{{ route('kategori_produks.index') }}" style="margin-left: 1rem; color: #666; text-decoration: none; font-size: 0.9rem;">Batal</a>
        </div}
    </form>
@endsection
