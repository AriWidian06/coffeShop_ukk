@extends('layouts.app')
@section('content')
    <div class="toolbar">
        <h1>Transaksi</h1><a class="button" href="{{ route('transaksis.create') }}">Buat transaksi</a>
    </div>
    <table>
        <tr>
            <th>ID</th>
            <th>Waktu</th>
            <th>Meja</th>
            <th>Total</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        @foreach ($transaksis as $transaksi)
            <tr>
                <td>#{{ $transaksi->id }}</td>
                <td>{{ $transaksi->waktu_transaksi?->format('d/m/Y H:i') }}</td>
                <td>{{ $transaksi->meja->nomor_meja ?? '-' }}</td>
                <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                <td>{{ $transaksi->status_pesanan }}</td>
                <td><a href="{{ route('transaksis.show', $transaksi) }}">Lihat</a></td>
            </tr>
        @endforeach
    </table>{{ $transaksis->links() }}
@endsection
