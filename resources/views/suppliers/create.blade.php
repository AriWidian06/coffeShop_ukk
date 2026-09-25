@extends('layouts.app')
@section('content')
    <h1>Tambah Supplier</h1>
    <form class="form" method="post" action="{{ route('suppliers.store') }}">@csrf
        @include('suppliers.form')<button>Simpan</button></form>
@endsection
