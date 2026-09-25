@extends('layouts.app')
@section('content')<h1>Edit Kategori</h1><form class="form" method="post" action="{{ route('kategori-produks.update', $kategoriProduk) }}">@csrf @method('PUT') @include('kategori_produks.form')<button>Simpan</button></form>@endsection
