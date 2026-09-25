@extends('layouts.app')
@section('content')
    <h1>Edit Produk</h1>
    <form class="form" method="post" action="{{ route('produks.update', $produk) }}">@csrf @method('PUT')
        @include('produks.form')<button>Simpan</button></form>
@endsection
