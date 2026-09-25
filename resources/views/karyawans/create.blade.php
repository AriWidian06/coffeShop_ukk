@extends('layouts.app')
@section('content')<h1>Tambah Karyawan</h1><form class="form" method="post" action="{{ route('karyawans.store') }}">@csrf @include('karyawans.form')<button>Simpan</button></form>@endsection
