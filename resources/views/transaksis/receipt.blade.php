@extends('layouts.app')
@section('content')
    @php
        $subtotalItems = (float) $transaksi->detail_transaksis->sum('subtotal');
        $pajakLayanan = max(0, (float) $transaksi->total_harga - $subtotalItems);
    @endphp
    <style>
        .badge-customization { display: inline-block; padding: 2px 6px; background: #f5f0e8; border: 1px solid #e7ded5; color: #59483c; font-size: .7rem; border-radius: 4px; font-style: italic; margin-left: 4px; vertical-align: middle; }
    </style>
    <h1>
        Struk Transaksi #{{ $transaksi->id }}</h1>
    <p>{{ $transaksi->waktu_transaksi?->format('d/m/Y H:i') }}</p>
    @if ($transaksi->catatan)
        <p><strong>Catatan pesanan:</strong> {{ $transaksi->catatan }}</p>
    @endif
    <p>Subtotal: Rp {{ number_format($subtotalItems, 0, ',', '.') }}</p>
    <p>Pajak & layanan: Rp {{ number_format($pajakLayanan, 0, ',', '.') }}</p>
    @foreach ($transaksi->detail_transaksis as $detail)
        <p>
            {{ $detail->produk->nama_produk ?? 'Produk dihapus' }} x {{ $detail->QTY }}: Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
            @if ($detail->customization)
                <br><span class="badge-customization">{{ $detail->customization }}</span>
            @endif
        </p>
    @endforeach
    <h2>Total Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</h2><button
        onclick="window.print()">Cetak</button>
@endsection
