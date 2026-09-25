@extends('layouts.app')
@section('content')<h1>Tambah Produk</h1><form class="form" method="post" action="{{ route('produks.store') }}">@csrf @include('produks.form')<button>Simpan</button></form>@endsection
