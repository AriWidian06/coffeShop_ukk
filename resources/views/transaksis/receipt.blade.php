@extends('layouts.app')
@section('content')
    <h1>
        Struk Transaksi #{{ $transaksi->id }}</h1>
    <p>{{ $transaksi->waktu_transaksi?->format('d/m/Y H:i') }}</p>
    @foreach ($transaksi->detail_transaksis as $detail)
        <p>{{ $detail->produk->nama_produk }} x {{ $detail->QTY }}: Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
        </p>
    @endforeach
    <h2>Total Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</h2><button
        onclick="window.print()">Cetak</button>
@endsection
