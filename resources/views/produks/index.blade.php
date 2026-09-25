@extends('layouts.app')
@section('content')
    <div class="toolbar">
        <h1>Produk</h1><a class="button" href="{{ route('produks.create') }}">Tambah</a>
    </div>
    <form method="get"><input name="search" placeholder="Cari produk" value="{{ request('search') }}"><select
            name="kategori_id">
            <option value="">Semua kategori</option>
            @foreach ($kategoriProduks as $kategori)
                <option value="{{ $kategori->id }}" @selected(request('kategori_id') == $kategori->id)>{{ $kategori->nama_kategori }}</option>
            @endforeach
        </select>
        <button>Filter</button>
    </form>
    <table>
        <tr>
            <th>Produk</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
        @foreach ($produks as $produk)
            <tr>
                <td>{{ $produk->nama_produk }}</td>
                <td>{{ $produk->kategori_produk->nama_kategori ?? '-' }}</td>
                <td>Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                <td>{{ $produk->stock }}</td>
                <td><a href="{{ route('produks.edit', $produk) }}">Edit</a>
                    <form class="inline" method="post" action="{{ route('produks.destroy', $produk) }}">@csrf
                        @method('DELETE')<button class="danger">Hapus</button></form>
                </td>
            </tr>
        @endforeach
    </table>{{ $produks->withQueryString()->links() }}
@endsection
