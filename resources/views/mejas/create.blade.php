@extends('layouts.app')
@section('content')
    <h1>Tambah Meja</h1>
    <form class="form" method="post" action="{{ route('mejas.store') }}">
        @csrf
        @include('mejas.form')
        <div style="margin-top: 1.5rem;">
            <button type="submit">Simpan Meja</button>
            <a href="{{ route('mejas.index') }}" style="margin-left: 1rem; color: #666; text-decoration: none; font-size: 0.9rem;">Batal</a>
        </div>
    </form>
@endsection
