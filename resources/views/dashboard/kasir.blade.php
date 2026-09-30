@extends('layouts.app')

@section('content')
    <h1>Dashboard Kasir</h1>
    <p>Fokus pada transaksi dan pemrosesan pesanan.</p>

    <div style="margin-top: 1.5rem; display:flex; gap:1rem; flex-wrap:wrap;">
        <a class="button" href="{{ route('transaksis.create') }}">POS</a>
        <a class="button" href="{{ route('transaksis.index') }}">Data Transaksi</a>
    </div>

    <div style="margin-top:2rem; background:#fff; padding:1rem; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
        <h2>Area POS</h2>
        <p>Kasir hanya melihat proses transaksi, pembayaran, dan struk.</p>
    </div>
@endsection
