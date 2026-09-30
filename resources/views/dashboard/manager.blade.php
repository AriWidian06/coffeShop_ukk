@extends('layouts.app')

@section('content')
    <h1>Dashboard Manager</h1>
    <p>Ringkasan laporan penjualan hari ini.</p>

    <div style="display:grid; gap:1rem; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-top: 1.5rem;">
        <div style="background:#fff; padding:1rem; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
            <strong>Omset Hari Ini</strong>
            <p>Rp 0</p>
        </div>
        <div style="background:#fff; padding:1rem; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
            <strong>Jumlah Transaksi</strong>
            <p>0</p>
        </div>
        <div style="background:#fff; padding:1rem; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
            <strong>Produk Terlaris</strong>
            <p>-</p>
        </div>
    </div>

    <div style="margin-top:2rem; background:#fff; padding:1rem; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
        <h2>Laporan Penjualan</h2>
        <p>Manager hanya dapat melihat data penjualan dan laporan.</p>
    </div>
@endsection
