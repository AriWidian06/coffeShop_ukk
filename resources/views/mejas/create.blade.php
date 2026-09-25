@extends('layouts.app')
@section('content')
    <h1>Tambah Meja</h1>
    <form class="form" method="post" action="{{ route('mejas.store') }}">@csrf
        @include('mejas.form')<button>Simpan</button></form>
@endsection
