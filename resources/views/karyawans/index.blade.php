@extends('layouts.app')
@section('content')
<div class="toolbar"><h1>Karyawan</h1><a class="button" href="{{ route('karyawans.create') }}">Tambah</a></div>
<table><tr><th>Nama</th><th>Jabatan</th><th>Username</th><th>Aksi</th></tr>@foreach($karyawans as $karyawan)<tr><td>{{ $karyawan->nama_karyawan }}</td><td>{{ $karyawan->jabatan }}</td><td>{{ $karyawan->username }}</td><td><a href="{{ route('karyawans.edit', $karyawan) }}">Edit</a> <form class="inline" method="post" action="{{ route('karyawans.destroy', $karyawan) }}">@csrf @method('DELETE')<button class="danger">Hapus</button></form></td></tr>@endforeach</table>
{{ $karyawans->links() }}
@endsection
