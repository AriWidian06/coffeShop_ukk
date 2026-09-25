@extends('layouts.app')
@section('content')<h1>Edit Karyawan</h1><form class="form" method="post" action="{{ route('karyawans.update', $karyawan) }}">@csrf @method('PUT') @include('karyawans.form')<button>Simpan</button></form>@endsection
