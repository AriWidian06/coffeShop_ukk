@extends('layouts.app')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Menunggu',
            'ready' => 'Siap',
            'prosesed' => 'Diproses',
            'completed' => 'Selesai',
        ];
    @endphp
    <style>
        main:has(.cashier-dashboard) { max-width: 1320px; }
        .cashier-dashboard { color: #30231b; }
        .cashier-dashboard-head, .cashier-latest-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
        .cashier-dashboard-head { margin-bottom: 1.5rem; }
        .cashier-dashboard-head h1 { margin: 0; font-size: 1.75rem; }
        .cashier-dashboard-head p { margin: .35rem 0 0; color: #59483c; }
        .cashier-dashboard-actions { display: flex; gap: .65rem; }
        .cashier-action { display: inline-flex; min-height: 44px; align-items: center; justify-content: center; padding: .6rem .9rem; border: 1px solid #3b2418; border-radius: 4px; background: #3b2418; color: #fff; font-weight: 700; text-decoration: none; }
        .cashier-action.is-secondary { background: #fffdfa; color: #30231b; }
        .cashier-latest-head { margin-bottom: .75rem; }
        .cashier-latest-head h2 { margin: 0; font-size: 1.2rem; }
        .cashier-latest-head a, .cashier-detail { color: #3b2418; font-weight: 700; }
        .cashier-table-wrap { overflow-x: auto; border: 1px solid #d8cbbf; background: #fffdfa; }
        .cashier-table { width: 100%; min-width: 760px; margin: 0; border-collapse: collapse; }
        .cashier-table th { padding: .8rem .75rem; background: #eee7df; color: #443226; font-size: .78rem; text-align: left; }
        .cashier-table td { padding: .85rem .75rem; border-bottom: 1px solid #e7ded5; vertical-align: top; }
        .cashier-table tbody tr:last-child td { border-bottom: 0; }
        .cashier-muted { display: block; margin-top: .25rem; color: #59483c; font-size: .82rem; }
        .cashier-status { display: inline-flex; min-height: 28px; align-items: center; padding: .25rem .5rem; border: 1px solid #8b7563; border-radius: 4px; color: #443226; font-size: .8rem; font-weight: 700; white-space: nowrap; }
        .cashier-empty { padding: 2.5rem 1rem; border: 1px solid #d8cbbf; background: #fffdfa; text-align: center; }
        .cashier-empty h3 { margin: 0 0 .5rem; }
        .cashier-empty p { margin: 0 0 1rem; color: #59483c; }
        .cashier-dashboard a:focus-visible { outline: 3px solid #8b4a20; outline-offset: 2px; }
    </style>

    <section class="cashier-dashboard">
        <header class="cashier-dashboard-head">
            <div>
                <h1>Dashboard Kasir</h1>
                <p>Pesanan POS dan web kafe untuk ditangani.</p>
            </div>
            <div class="cashier-dashboard-actions">
                <a class="cashier-action" href="{{ route('transaksis.create') }}">POS</a>
                <a class="cashier-action is-secondary" href="{{ route('transaksis.index') }}">Data Transaksi</a>
            </div>
        </header>

        <section aria-labelledby="cashier-latest-title">
            <div class="cashier-latest-head">
                <h2 id="cashier-latest-title">Transaksi terbaru</h2>
                <a href="{{ route('transaksis.index') }}">Lihat semua</a>
            </div>

            @if ($transaksiTerbaru->isEmpty())
                <div class="cashier-empty">
                    <h3>Belum ada transaksi</h3>
                    <p>Pesanan POS dan web kafe akan muncul di sini.</p>
                    <a class="cashier-action" href="{{ route('transaksis.create') }}">Buat pesanan</a>
                </div>
            @else
                <div class="cashier-table-wrap">
                    <table class="cashier-table">
                        <thead>
                            <tr>
                                <th>Transaksi</th>
                                <th>Pesanan</th>
                                <th>Sumber</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksiTerbaru as $transaksi)
                                <tr>
                                    <td>
                                        #{{ $transaksi->id }}
                                        <span class="cashier-muted">{{ $transaksi->waktu_transaksi?->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td>
                                        {{ $transaksi->tipe_pesanan === 'take-away' ? 'Bawa pulang' : 'Makan di tempat' }}
                                        <span class="cashier-muted">
                                            @if ($transaksi->meja)
                                                Meja {{ $transaksi->meja->nomor_meja }}
                                            @else
                                                Tanpa meja
                                            @endif
                                        </span>
                                        <span class="cashier-muted">
                                            @foreach ($transaksi->detail_transaksis->take(2) as $detail)
                                                {{ $detail->produk->nama_produk ?? 'Produk dihapus' }} x {{ $detail->QTY }}@if (!$loop->last), @endif
                                            @endforeach
                                        </span>
                                    </td>
                                    <td>{{ $transaksi->sumber_pesanan === 'web' ? 'Web kafe' : 'POS' }}</td>
                                    <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                    <td><span class="cashier-status">{{ $statusLabels[$transaksi->status_pesanan] ?? $transaksi->status_pesanan }}</span></td>
                                    <td><a class="cashier-detail" href="{{ route('transaksis.show', $transaksi) }}">Detail Transaksi</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </section>
@endsection
