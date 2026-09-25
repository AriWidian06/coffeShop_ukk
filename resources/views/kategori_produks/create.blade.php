@extends('layouts.app')
@section('content')
    <h1>Tambah Kategori</h1>
    <form class="form" method="post" action="{{ route('kategori-produks.store') }}">@csrf
        @include('kategori_produks.form')<button>Simpan</button></form>
@endsection
