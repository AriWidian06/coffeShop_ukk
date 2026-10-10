@extends('layouts.app')
@section('content')
    @php
        $subtotalItems = (float) $transaksi->detail_transaksis->sum('subtotal');
        $pajakLayanan = max(0, (float) $transaksi->total_harga - $subtotalItems);
        $statusLabels = [
            'pending' => 'Menunggu',
            'ready' => 'Siap',
            'prosesed' => 'Diproses',
            'completed' => 'Selesai',
        ];
        $role = auth('karyawan')->user()->role;
        $canProcess = in_array($role, ['kasir', 'admin'], true);
    @endphp
    <style>
        main:has(.transaction-detail) { max-width: 1280px; }
        .transaction-detail { color: #30231b; }
        .detail-back { display: inline-flex; min-height: 44px; align-items: center; color: #3b2418; font-weight: 700; text-decoration: none; }
        .detail-heading { display: flex; align-items: start; justify-content: space-between; gap: 1rem; margin: .7rem 0 1.1rem; }
        .detail-heading h1 { margin: 0; font-size: 1.8rem; }
        .detail-number { margin: .25rem 0 0; color: #59483c; }
        .detail-status { display: inline-flex; min-height: 32px; align-items: center; padding: .35rem .65rem; border: 1px solid #8b7563; border-radius: 4px; color: #443226; font-size: .85rem; font-weight: 700; white-space: nowrap; }
        .detail-facts { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); margin: 0 0 1.4rem; padding: .85rem 0; border-top: 1px solid #d8cbbf; border-bottom: 1px solid #d8cbbf; }
        .detail-facts div { min-width: 0; padding: .25rem .9rem; border-right: 1px solid #d8cbbf; }
        .detail-facts div:first-child { padding-left: 0; }
        .detail-facts div:last-child { border-right: 0; }
        .detail-facts dt { color: #59483c; font-size: .78rem; }
        .detail-facts dd { margin: .25rem 0 0; font-weight: 700; overflow-wrap: anywhere; }
        .detail-order-note { margin: -0.35rem 0 1.35rem; padding: .8rem 1rem; border: 1px solid #cbd5e1; border-left: 4px solid #2563eb; background: #fff; }
        .detail-order-note strong { display: block; margin-bottom: .25rem; color: #334155; font-size: .82rem; }
        .detail-order-note p { margin: 0; color: #0f172a; white-space: pre-line; }
        .detail-items-heading { display: flex; align-items: baseline; justify-content: space-between; gap: 1rem; }
        .detail-items-heading h2, .detail-action h2 { margin: 0; font-size: 1.1rem; }
        .detail-items-heading span { color: #59483c; font-size: .85rem; }
        .detail-table-wrap { overflow-x: auto; border: 1px solid #d8cbbf; background: #fffdfa; }
        .detail-table { width: 100%; min-width: 560px; margin: 0; border-collapse: collapse; }
        .detail-table th { padding: .75rem; background: #eee7df; color: #443226; font-size: .8rem; text-align: left; }
        .detail-table td { padding: .8rem .75rem; border-bottom: 1px solid #e7ded5; }
        .detail-table tbody tr:last-child td { border-bottom: 0; }
        .detail-qty { width: 7rem; }
        .detail-money { text-align: right !important; white-space: nowrap; }
        .detail-totals { display: grid; gap: .45rem; padding: 1rem .75rem; border-top: 1px solid #d8cbbf; background: #fffdfa; }
        .detail-total-line { display: flex; justify-content: space-between; gap: 1rem; color: #59483c; }
        .detail-total-line.is-grand { margin-top: .35rem; padding-top: .75rem; border-top: 1px solid #d8cbbf; color: #30231b; font-size: 1.05rem; font-weight: 700; }
        .detail-total-line.is-grand strong { font-size: 1.15rem; }
        .detail-actions { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.25rem; margin-top: 1.4rem; }
        .detail-action { padding-top: 1rem; border-top: 2px solid #8b7563; }
        .detail-action p { margin: .45rem 0 .8rem; color: #59483c; font-size: .88rem; }
        .detail-form { max-width: none; padding: 0; background: transparent; }
        .detail-form label { margin-top: .65rem; }
        .detail-form select, .detail-form input { min-height: 44px; border-color: #8b7563; }
        .detail-submit, .detail-receipt { display: inline-flex; min-height: 44px; align-items: center; justify-content: center; margin-top: .8rem; padding: .6rem .9rem; border: 1px solid #3b2418; border-radius: 4px; background: #3b2418; color: #fff; font: inherit; font-weight: 700; text-decoration: none; cursor: pointer; }
        .detail-receipt { background: #fffdfa; color: #30231b; }
        .detail-payment-state { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .75rem 1.5rem; max-width: 650px; padding: .8rem 0; }
        .detail-payment-state span { display: block; color: #59483c; font-size: .8rem; }
        .detail-payment-state strong { display: block; margin-top: .2rem; }
        .badge-customization { display: inline-block; padding: 2px 6px; background: #f5f0e8; border: 1px solid #e7ded5; color: #59483c; font-size: .7rem; border-radius: 4px; font-style: italic; margin-left: 4px; vertical-align: middle; }
        .detail-detail-actions { display: flex; align-items: center; gap: .75rem; margin-top: 1.25rem; }
        .transaction-detail :focus-visible { outline: 3px solid #8b4a20; outline-offset: 2px; }
        @media (max-width: 760px) {
            body:not(.cashier-desktop) .detail-facts { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            body:not(.cashier-desktop) .detail-facts div:nth-child(2) { border-right: 0; }
            body:not(.cashier-desktop) .detail-facts div:nth-child(n+3) { border-top: 1px solid #d8cbbf; }
            body:not(.cashier-desktop) .detail-facts div:nth-child(3) { padding-left: 0; }
            body:not(.cashier-desktop) .detail-actions { grid-template-columns: minmax(0, 1fr); }
        }
        @media (max-width: 460px) {
            body:not(.cashier-desktop) .detail-heading { align-items: flex-start; flex-direction: column; }
            body:not(.cashier-desktop) .detail-facts { grid-template-columns: minmax(0, 1fr); }
            body:not(.cashier-desktop) .detail-facts div { padding: .65rem 0; border-right: 0; border-top: 1px solid #d8cbbf; }
            body:not(.cashier-desktop) .detail-facts div:first-child { border-top: 0; }
            body:not(.cashier-desktop) .detail-total { justify-content: space-between; gap: 1rem; }
        }
    </style>

    <section class="transaction-detail">
        <a class="detail-back" href="{{ route('transaksis.index') }}">Kembali ke Data Transaksi</a>

        <header class="detail-heading">
            <div>
                <h1>Detail Transaksi</h1>
                <p class="detail-number">#{{ $transaksi->id }}</p>
            </div>
            <span class="detail-status">{{ $statusLabels[$transaksi->status_pesanan] ?? $transaksi->status_pesanan }}</span>
        </header>

        <dl class="detail-facts">
            <div>
                <dt>Tipe pesanan</dt>
                <dd>{{ $transaksi->tipe_pesanan === 'take-away' ? 'Bawa pulang' : 'Makan di tempat' }}</dd>
            </div>
            <div>
                <dt>Meja</dt>
                <dd>{{ $transaksi->meja ? 'Meja ' . $transaksi->meja->nomor_meja : 'Tanpa meja' }}</dd>
            </div>
            <div>
                <dt>Sumber / kasir</dt>
                <dd>{{ $transaksi->sumber_pesanan === 'web' ? 'Web kafe' : ($transaksi->karyawan->nama_karyawan ?? '-') }}</dd>
            </div>
            <div>
                <dt>Waktu transaksi</dt>
                <dd>{{ $transaksi->waktu_transaksi?->format('d/m/Y H:i') ?? '-' }}</dd>
            </div>
        </dl>

        @if ($transaksi->catatan)
            <aside class="detail-order-note" aria-label="Catatan pesanan">
                <strong>Catatan pesanan</strong>
                <p>{{ $transaksi->catatan }}</p>
            </aside>
        @endif

        <section aria-labelledby="detail-items-title">
            <div class="detail-items-heading">
                <h2 id="detail-items-title">Item pesanan</h2>
                <span>{{ $transaksi->detail_transaksis->count() }} baris</span>
            </div>
            <div class="detail-table-wrap">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="detail-qty">Jumlah</th>
                            <th class="detail-money">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksi->detail_transaksis as $detail)
                            <tr>
                                <td>
                                    {{ $detail->produk->nama_produk ?? 'Produk dihapus' }}
                                    @if ($detail->customization)
                                        <span class="badge-customization">{{ $detail->customization }}</span>
                                    @endif
                                </td>
                                <td>{{ $detail->QTY }}</td>
                                <td class="detail-money">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">Tidak ada item pada transaksi ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="detail-totals">
                    <div class="detail-total-line">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($subtotalItems, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-total-line">
                        <span>Pajak & layanan</span>
                        <span>Rp {{ number_format($pajakLayanan, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-total-line is-grand">
                        <span>Total transaksi</span>
                        <strong>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </section>

        <div class="detail-actions">
            <section class="detail-action" aria-labelledby="detail-status-title">
                <h2 id="detail-status-title">Status pesanan</h2>
                <p>Saat ini: {{ $statusLabels[$transaksi->status_pesanan] ?? $transaksi->status_pesanan }}</p>
                @if ($canProcess)
                    <form class="detail-form" method="post" action="{{ route('transaksis.status', $transaksi) }}">
                        @csrf
                        @method('PATCH')
                        <label>
                            Ubah status
                            <select name="status_pesanan" required>
                                @foreach ($statusLabels as $value => $label)
                                    <option value="{{ $value }}" @selected($transaksi->status_pesanan === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button class="detail-submit" type="submit">Perbarui status</button>
                    </form>
                @endif
            </section>

            <section class="detail-action" aria-labelledby="detail-payment-title">
                <h2 id="detail-payment-title">Pembayaran</h2>
                @if ($transaksi->pembayaran)
                    <p>Pembayaran sudah diterima.</p>
                    <div class="detail-payment-state">
                        <div>
                            <span>Jumlah</span>
                            <strong>Rp {{ number_format($transaksi->pembayaran->jumlah_bayar, 0, ',', '.') }}</strong>
                        </div>
                        <div>
                            <span>Metode</span>
                            <strong>{{ strtoupper($transaksi->pembayaran->metode_pembayaran) }}</strong>
                        </div>
                        <div>
                            <span>Kembalian</span>
                            <strong>Rp {{ number_format($transaksi->pembayaran->kembalian, 0, ',', '.') }}</strong>
                        </div>
                        <div>
                            <span>Waktu bayar</span>
                            <strong>{{ $transaksi->pembayaran->waktu_pembayaran?->format('d/m/Y H:i') ?? '-' }}</strong>
                        </div>
                    </div>
                @elseif ($canProcess)
                    <p>Belum dibayar. Masukkan jumlah yang diterima untuk menyelesaikan pesanan.</p>
                    <form class="detail-form" method="post" action="{{ route('pembayarans.store') }}">
                        @csrf
                        <input type="hidden" name="transaksi_id" value="{{ $transaksi->id }}">
                        <label>
                            Jumlah bayar
                            <input type="number" step="0.01" name="jumlah_bayar" min="{{ $transaksi->total_harga }}" required>
                        </label>
                        <label>
                            Metode
                            <select name="metode_pembayaran" required>
                                <option value="cash">Tunai</option>
                                <option value="QRIS">QRIS</option>
                            </select>
                        </label>
                        <button class="detail-submit" type="submit">Catat pembayaran</button>
                    </form>
                @else
                    <p>Belum dibayar.</p>
                @endif
            </section>
        </div>

        <div class="detail-detail-actions">
            @if ($canProcess)
                <a class="detail-receipt" href="{{ route('transaksis.receipt', $transaksi) }}">Cetak struk</a>
            @endif
        </div>
    </section>
@endsection
