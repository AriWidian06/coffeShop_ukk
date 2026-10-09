@extends('layouts.app')
@section('content')
    @php
        $role = auth('karyawan')->user()->role;
        $canViewDetails = in_array($role, ['kasir', 'manager', 'admin'], true);
        $statusLabels = [
            'pending' => 'Menunggu',
            'ready' => 'Siap',
            'prosesed' => 'Diproses',
            'completed' => 'Selesai',
        ];
    @endphp
    <style>
        main:has(.transactions-page) { max-width: 1320px; }
        .transactions-page { color: #30231b; }
        .transactions-heading, .transactions-actions, .transactions-filters { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
        .transactions-heading { margin-bottom: 1.5rem; }
        .transactions-heading h1 { margin: 0; font-size: 1.75rem; }
        .transactions-heading p { margin: .35rem 0 0; color: #59483c; }
        .transactions-actions { justify-content: flex-end; }
        .transactions-link, .transactions-submit, .transactions-reset { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: .65rem .9rem; border: 1px solid #3b2418; border-radius: 5px; background: #3b2418; color: #fff; font: inherit; font-weight: 700; text-decoration: none; cursor: pointer; }
        .transactions-reset { border-color: #8b7563; background: #fffdfa; color: #30231b; }
        .transactions-count { padding: .8rem 0; color: #59483c; font-size: .9rem; }
        .transactions-filters { justify-content: flex-start; flex-wrap: wrap; padding: 1rem; border: 1px solid #d8cbbf; background: #fffdfa; }
        .transactions-filters label { display: block; margin: 0; color: #59483c; font-size: .82rem; font-weight: 700; }
        .transactions-filters input, .transactions-filters select { min-height: 44px; min-width: 190px; margin-top: .35rem; border: 1px solid #8b7563; border-radius: 4px; background: #fff; color: #30231b; font: inherit; }
        .transactions-filters input { padding: .6rem .75rem; }
        .transactions-filters select { width: auto; padding: .6rem 2rem .6rem .75rem; }
        .transactions-submit { align-self: end; }
        .transactions-table-wrap { overflow-x: auto; border: 1px solid #d8cbbf; background: #fffdfa; }
        .transactions-table { width: 100%; min-width: 760px; margin: 0; border-collapse: collapse; }
        .transactions-table th { padding: .8rem .75rem; background: #eee7df; color: #443226; font-size: .78rem; text-align: left; }
        .transactions-table td { padding: .85rem .75rem; border-bottom: 1px solid #e7ded5; vertical-align: top; }
        .transactions-table tbody tr:last-child td { border-bottom: 0; }
        .transaction-id { color: #30231b; font-weight: 700; text-decoration: none; }
        .transaction-meta, .transaction-items { display: block; margin-top: .25rem; color: #59483c; font-size: .82rem; }
        .transaction-total { white-space: nowrap; font-weight: 700; }
        .transaction-state { display: inline-flex; min-height: 28px; align-items: center; padding: .25rem .5rem; border: 1px solid #8b7563; border-radius: 4px; color: #443226; font-size: .8rem; font-weight: 700; white-space: nowrap; }
        .transaction-state.is-paid { border-color: #58705a; color: #29472d; }
        .transactions-empty { padding: 3rem 1.25rem; text-align: center; }
        .transactions-empty h2 { margin: 0 0 .5rem; font-size: 1.15rem; }
        .transactions-empty p { margin: 0 0 1rem; color: #59483c; }
        .transactions-pagination { padding: 1rem 0; }
        .transactions-page a:focus-visible, .transactions-page button:focus-visible, .transactions-page input:focus-visible, .transactions-page select:focus-visible { outline: 3px solid #8b4a20; outline-offset: 2px; }
        @media (max-width: 760px) {
            body:not(.cashier-desktop) .transactions-heading { align-items: flex-start; flex-direction: column; }
            body:not(.cashier-desktop) .transactions-actions { width: 100%; justify-content: flex-start; }
            body:not(.cashier-desktop) .transactions-filters { align-items: stretch; flex-direction: column; }
            body:not(.cashier-desktop) .transactions-filters label,
            body:not(.cashier-desktop) .transactions-filters input,
            body:not(.cashier-desktop) .transactions-filters select,
            body:not(.cashier-desktop) .transactions-submit { width: 100%; box-sizing: border-box; }
            body:not(.cashier-desktop) .transactions-filters select { min-width: 0; }
            body:not(.cashier-desktop) .transactions-table-wrap { overflow: visible; border: 0; background: transparent; }
            body:not(.cashier-desktop) .transactions-table { min-width: 0; border-collapse: separate; border-spacing: 0 .7rem; }
            body:not(.cashier-desktop) .transactions-table thead { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; }
            body:not(.cashier-desktop) .transactions-table tbody,
            body:not(.cashier-desktop) .transactions-table tr,
            body:not(.cashier-desktop) .transactions-table td { display: block; width: auto; }
            body:not(.cashier-desktop) .transactions-table tr { padding: .4rem .85rem; border: 1px solid #d8cbbf; background: #fffdfa; }
            body:not(.cashier-desktop) .transactions-table td { display: grid; grid-template-columns: minmax(6.5rem, 34%) minmax(0, 1fr); gap: .75rem; padding: .65rem 0; border-bottom: 1px solid #e7ded5; }
            body:not(.cashier-desktop) .transactions-table td:last-child { border-bottom: 0; }
            body:not(.cashier-desktop) .transactions-table td::before { content: attr(data-label); color: #59483c; font-size: .78rem; font-weight: 700; }
            body:not(.cashier-desktop) .transactions-empty { border: 1px solid #d8cbbf; background: #fffdfa; }
        }
    </style>

    <section class="transactions-page">
        <header class="transactions-heading">
            <div>
                <h1>Data Transaksi</h1>
                <p>Riwayat pesanan dari POS dan web kafe.</p>
            </div>
            @if (in_array($role, ['kasir', 'admin'], true))
                <div class="transactions-actions">
                    <a class="transactions-link" href="{{ route('transaksis.create') }}">Buat pesanan</a>
                </div>
            @endif
        </header>

        <form class="transactions-filters" method="get" action="{{ route('transaksis.index') }}">
            <label>
                Nomor transaksi atau meja
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Contoh: 24" autocomplete="off">
            </label>
            <label>
                Status pesanan
                <select name="status">
                    <option value="">Semua status</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <button class="transactions-submit" type="submit">Tampilkan</button>
            @if (request()->filled('q') || request()->filled('status'))
                <a class="transactions-reset" href="{{ route('transaksis.index') }}">Hapus filter</a>
            @endif
        </form>

        <div class="transactions-count">{{ $transaksis->total() }} transaksi ditemukan</div>

        @if ($transaksis->count())
            <div class="transactions-table-wrap">
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Transaksi</th>
                            <th>Pesanan</th>
                            <th>Sumber / kasir</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            @if ($canViewDetails)
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaksis as $transaksi)
                            <tr>
                                <td data-label="Transaksi">
                                    <span class="transaction-id">#{{ $transaksi->id }}</span>
                                    <span class="transaction-meta">{{ $transaksi->waktu_transaksi?->format('d/m/Y H:i') }}</span>
                                </td>
                                <td data-label="Pesanan">
                                    {{ $transaksi->tipe_pesanan === 'take-away' ? 'Bawa pulang' : 'Makan di tempat' }}
                                    <span class="transaction-meta">
                                        @if ($transaksi->meja)
                                            Meja {{ $transaksi->meja->nomor_meja }}
                                        @else
                                            Tanpa meja
                                        @endif
                                    </span>
                                    <span class="transaction-items">
                                        @foreach ($transaksi->detail_transaksis->take(2) as $detail)
                                            {{ $detail->produk->nama_produk ?? 'Produk dihapus' }} x {{ $detail->QTY }}@if (!$loop->last), @endif
                                        @endforeach
                                        @if ($transaksi->detail_transaksis->count() > 2)
                                            dan {{ $transaksi->detail_transaksis->count() - 2 }} produk lain
                                        @endif
                                    </span>
                                    @if ($transaksi->catatan)
                                        <span class="transaction-meta">Catatan: {{ $transaksi->catatan }}</span>
                                    @endif
                                </td>
                                <td data-label="Sumber / kasir">
                                    @if ($transaksi->sumber_pesanan === 'web')
                                        Web kafe
                                    @else
                                        {{ $transaksi->karyawan->nama_karyawan ?? '-' }}
                                    @endif
                                </td>
                                <td class="transaction-total" data-label="Total">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                <td data-label="Status">
                                    <span class="transaction-state">{{ $statusLabels[$transaksi->status_pesanan] ?? $transaksi->status_pesanan }}</span>
                                </td>
                                <td data-label="Pembayaran">
                                    @if ($transaksi->pembayaran)
                                        <span class="transaction-state is-paid">Lunas</span>
                                    @else
                                        <span class="transaction-state">Belum dibayar</span>
                                    @endif
                                </td>
                                @if ($canViewDetails)
                                    <td data-label="Aksi"><a href="{{ route('transaksis.show', $transaksi) }}">Rincian</a></td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="transactions-pagination">{{ $transaksis->links() }}</div>
        @else
            <div class="transactions-empty">
                @if (request()->filled('q') || request()->filled('status'))
                    <h2>Tidak ada transaksi yang cocok</h2>
                    <p>Coba ubah kata pencarian atau pilih status lain.</p>
                    <a class="transactions-reset" href="{{ route('transaksis.index') }}">Hapus filter</a>
                @else
                    <h2>Belum ada transaksi</h2>
                    <p>Pesanan dari POS dan web kafe akan muncul di sini.</p>
                    @if (in_array($role, ['kasir', 'admin'], true))
                        <a class="transactions-link" href="{{ route('transaksis.create') }}">Buat pesanan pertama</a>
                    @endif
                @endif
            </div>
        @endif
    </section>

    <script>
        document.querySelector('.transactions-filters')?.addEventListener('submit', function () {
            const submit = this.querySelector('button[type="submit"]');
            submit.disabled = true;
            submit.textContent = 'Memuat transaksi...';
        });
    </script>
@endsection
