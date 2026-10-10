@extends('layouts.app')
@section('content')
    <h1>Tambah Karyawan</h1>
    <form class="form" method="post" action="{{ route('karyawans.store') }}">
        @csrf
        @include('karyawans.form')
        <div style="margin-top: 1.5rem;">
            <button type="submit">Simpan Karyawan</button>
            <a href="{{ route('karyawans.index') }}" style="margin-left: 1rem; color: #666; text-decoration: none; font-size: 0.9rem;">Batal</a>
        </div}
    </form>
@endsection
