@extends('layouts.app')
@section('content')
    <h1>Tambah Produk</h1>
    <form class="form" method="post" action="{{ route('produks.store') }}">
        @csrf
        @include('produks.form')
        <div style="margin-top: 1.5rem;">
            <button type="submit">Simpan Produk</button>
            <a href="{{ route('produks.index') }}" style="margin-left: 1rem; color: #666; text-decoration: none; font-size: 0.9rem;">Batal</a>
        </div>
    </form>
@endsection
