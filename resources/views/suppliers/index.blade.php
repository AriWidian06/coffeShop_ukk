@extends('layouts.app')
@section('content')
    <div class="toolbar">
        <h1>Supplier</h1><a class="button" href="{{ route('suppliers.create') }}">Tambah</a>
    </div>
    <table>
        <tr>
            <th>Nama</th>
            <th>Telepon</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
        @foreach ($suppliers as $supplier)
            <tr>
                <td>{{ $supplier->nama_supplier }}</td>
                <td>{{ $supplier->no_telp }}</td>
                <td>{{ $supplier->alamat }}</td>
                <td><a href="{{ route('suppliers.edit', $supplier) }}">Edit</a>
                    <form class="inline" method="post" action="{{ route('suppliers.destroy', $supplier) }}">@csrf
                        @method('DELETE')<button class="danger">Hapus</button></form>
                </td>
            </tr>
        @endforeach
    </table>{{ $suppliers->links() }}
@endsection
