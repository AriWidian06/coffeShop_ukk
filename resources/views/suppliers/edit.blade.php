@extends('layouts.app')
@section('content')
    <h1>Edit Supplier</h1>
    <form class="form" method="post" action="{{ route('suppliers.update', $supplier) }}">@csrf @method('PUT')
        @include('suppliers.form')<button>Simpan</button></form>
@endsection
