@extends('layouts.app')
@section('content')
    <h1>Tambah Supplier</h1>
    <form class="form" method="post" action="{{ route('suppliers.store') }}">
        @csrf
        @include('suppliers.form')
        <div style="margin-top: 1.5rem;">
            <button type="submit">Simpan Supplier</button>
            <a href="{{ route('suppliers.index') }}" style="margin-left: 1rem; color: #666; text-decoration: none; font-size: 0.9rem;">Batal</a>
        </div}
    </form>
@endsection
