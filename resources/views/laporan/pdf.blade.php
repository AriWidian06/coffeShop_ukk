<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Bagi Kopi</title>
    <style>
        @page {
            margin: 96px 28px 54px 28px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5px;
            color: #2b2118;
            background: #ffffff;
        }

        .header {
            position: fixed;
            top: -78px;
            left: 0;
            right: 0;
            height: 68px;
            border-bottom: 2px solid #6f4e37;
            padding-bottom: 8px;
        }

        .header table {
            width: 100%;
            border-collapse: collapse;
        }

        .header td {
            vertical-align: middle;
        }

        .logo {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            border: 1px solid #e7ddd2;
        }

        .brand {
            font-size: 17px;
            font-weight: bold;
            color: #2b2118;
            letter-spacing: 0.2px;
        }

        .tagline {
            margin-top: 2px;
            font-size: 7.5px;
            color: #8a7766;
            letter-spacing: 1.1px;
            text-transform: uppercase;
        }

        .doc-title {
            font-size: 15px;
            font-weight: bold;
            color: #6f4e37;
            letter-spacing: 0.8px;
            text-align: right;
        }

        .doc-period {
            margin-top: 3px;
            font-size: 9px;
            font-weight: bold;
            color: #3c2f25;
            text-align: right;
        }

        .doc-meta {
            margin-top: 2px;
            font-size: 7.5px;
            color: #8a7766;
            text-align: right;
        }

        .footer {
            position: fixed;
            bottom: -38px;
            left: 0;
            right: 0;
            border-top: 1px solid #e7ddd2;
            padding-top: 6px;
            font-size: 7px;
            color: #8a7766;
        }

        .footer table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer td {
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }

        .section-title {
            margin: 0 0 6px 0;
            font-size: 10px;
            font-weight: bold;
            color: #3c2f25;
        }

        .section-subtitle {
            margin: -3px 0 8px 0;
            font-size: 7px;
            color: #8a7766;
        }

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 7px 0;
            margin: 0 -7px 12px -7px;
        }

        .summary-table td {
            width: 25%;
            background: #f7f3ee;
            border: 1px solid #e7ddd2;
            border-top: 3px solid #6f4e37;
            padding: 9px 10px;
            vertical-align: top;
        }

        .stat-label {
            font-size: 6.8px;
            font-weight: bold;
            color: #8a7766;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .stat-value {
            margin-top: 4px;
            font-size: 14px;
            font-weight: bold;
            color: #2b2118;
        }

        .stat-sub {
            margin-top: 3px;
            font-size: 7px;
            color: #8a7766;
        }

        .two-col {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .two-col > tbody > tr > td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .two-col > tbody > tr > td:first-child {
            padding-right: 6px;
        }

        .two-col > tbody > tr > td:last-child {
            padding-left: 6px;
        }

        .panel {
            border: 1px solid #e7ddd2;
            background: #ffffff;
            padding: 9px 10px;
        }

        .mini-table {
            width: 100%;
            border-collapse: collapse;
        }

        .mini-table th {
            font-size: 6.8px;
            color: #8a7766;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: left;
            border-bottom: 1px solid #e7ddd2;
            padding: 3px 4px;
        }

        .mini-table td {
            font-size: 7.5px;
            padding: 4px;
            border-bottom: 1px solid #f1ebe4;
            vertical-align: top;
        }

        .mini-table tr:last-child td {
            border-bottom: none;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e7ddd2;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th {
            background: #6f4e37;
            color: #ffffff;
            font-size: 6.8px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 6px 5px;
            text-align: left;
            border-right: 1px solid #84634c;
        }

        .data-table th:last-child {
            border-right: none;
        }

        .data-table td {
            font-size: 7.3px;
            padding: 5px;
            border-bottom: 1px solid #eee5db;
            border-right: 1px solid #f4eee8;
            vertical-align: top;
        }

        .data-table td:last-child {
            border-right: none;
        }

        .data-table tbody tr:nth-child(even) td {
            background: #fbf8f5;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 8px;
            font-size: 6.5px;
            font-weight: bold;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-prosesed {
            background: #ede9fe;
            color: #5b21b6;
        }

        .badge-ready {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-completed {
            background: #d1fae5;
            color: #047857;
        }

        .badge-paid {
            background: #d1fae5;
            color: #047857;
        }

        .badge-unpaid {
            background: #fee2e2;
            color: #b91c1c;
        }

        .muted {
            color: #8a7766;
        }

        .empty-row td {
            padding: 14px;
            text-align: center;
            color: #8a7766;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td width="55%">
                    <table>
                        <tr>
                            @if($logoData)
                                <td width="58" style="padding-right: 9px;">
                                    <img src="data:image/jpeg;base64,{{ $logoData }}" class="logo" alt="Bagi Kopi">
                                </td>
                            @endif
                            <td>
                                <div class="brand">Bagi Kopi</div>
                                <div class="tagline">Artisanal Brew &bull; Perkoci Eatery</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="45%">
                    <div class="doc-title">LAPORAN PENJUALAN</div>
                    <div class="doc-period">{{ $periodeLabel }}</div>
                    <div class="doc-meta">{{ $start->translatedFormat('d M Y') }} &ndash; {{ $end->translatedFormat('d M Y') }} WIB</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <table>
            <tr>
                <td>Dicetak oleh {{ $dicetakOleh }} &bull; {{ $nowWib->translatedFormat('d F Y, H:i:s') }} WIB</td>
                <td class="text-right">Bagi Kopi &bull; Laporan Penjualan</td>
            </tr>
        </table>
    </div>

    <table class="summary-table">
        <tr>
            <td>
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                <div class="stat-sub">Omzet periode terpilih</div>
            </td>
            <td>
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-value">{{ number_format($totalTransaksi, 0, ',', '.') }}</div>
                <div class="stat-sub">{{ number_format($totalItem, 0, ',', '.') }} item terjual</div>
            </td>
            <td>
                <div class="stat-label">Rata-rata / Struk</div>
                <div class="stat-value">Rp {{ number_format($averageTransactionValue, 0, ',', '.') }}</div>
                <div class="stat-sub">Average transaction value</div>
            </td>
            <td>
                <div class="stat-label">Total Diskon Promo</div>
                <div class="stat-value">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</div>
                <div class="stat-sub">Potongan harga pelanggan</div>
            </td>
        </tr>
    </table>

    <table class="two-col">
        <tr>
            <td>
                <div class="panel">
                    <div class="section-title">Top 5 Produk Terlaris</div>
                    <div class="section-subtitle">Berdasarkan kuantitas terjual pada periode ini</div>
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th width="8%">#</th>
                                <th>Produk</th>
                                <th width="15%" class="text-right">Qty</th>
                                <th width="28%" class="text-right">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $index => $product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $product->nama_produk }}</strong><br>
                                        <span class="muted">{{ $product->kategori }}</span>
                                    </td>
                                    <td class="text-right">{{ number_format($product->total_qty, 0, ',', '.') }} pcs</td>
                                    <td class="text-right">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="muted" style="text-align: center; padding: 10px;">Belum ada data produk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </td>
            <td>
                <div class="panel">
                    <div class="section-title">Metode Pembayaran</div>
                    <div class="section-subtitle">Distribusi pembayaran pada periode ini</div>
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th>Metode</th>
                                <th width="18%" class="text-right">Transaksi</th>
                                <th width="30%" class="text-right">Nominal</th>
                                <th width="16%" class="text-right">Porsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paymentStats as $payment)
                                <tr>
                                    <td><strong>{{ $payment->metode }}</strong></td>
                                    <td class="text-right">{{ number_format($payment->jumlah, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($payment->total, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ $totalPendapatan > 0 ? round(($payment->total / $totalPendapatan) * 100, 1) : 0 }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="muted" style="text-align: center; padding: 10px;">Belum ada pembayaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Rincian Transaksi</div>
    <div class="section-subtitle">{{ number_format($totalTransaksi, 0, ',', '.') }} transaksi pada {{ $periodeLabel }}</div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">Waktu (WIB)</th>
                <th width="6%">Tiket</th>
                <th width="10%">Meja / Tipe</th>
                <th width="13%">Kasir / Sumber</th>
                <th width="5%" class="text-right">Item</th>
                <th width="9%" class="text-right">Diskon</th>
                <th width="11%" class="text-right">Total</th>
                <th width="11%">Pembayaran</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $index => $transaksi)
                @php
                    $waktuWib = $transaksi->waktu_transaksi?->copy()->timezone('Asia/Jakarta');
                    $itemCount = (int) $transaksi->detail_transaksis->sum('QTY');
                    $statusClass = match ($transaksi->status_pesanan) {
                        'prosesed' => 'badge-prosesed',
                        'ready' => 'badge-ready',
                        'completed' => 'badge-completed',
                        default => 'badge-pending',
                    };
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $waktuWib ? $waktuWib->format('d/m/Y H:i') : '-' }}</td>
                    <td>#{{ $transaksi->id }}</td>
                    <td>
                        {{ $transaksi->meja ? 'Meja ' . $transaksi->meja->nomor_meja : 'Tanpa Meja' }}<br>
                        <span class="muted">{{ $transaksi->tipe_pesanan === 'take-away' ? 'Take Away' : 'Dine-in' }}</span>
                    </td>
                    <td>{{ $transaksi->karyawan?->nama_karyawan ?? ($transaksi->sumber_pesanan === 'web' ? 'Web Order' : 'Kasir') }}</td>
                    <td class="text-right">{{ $itemCount }}</td>
                    <td class="text-right">{{ $transaksi->diskon > 0 ? '-Rp ' . number_format((float) $transaksi->diskon, 0, ',', '.') : '-' }}</td>
                    <td class="text-right"><strong>Rp {{ number_format((float) $transaksi->total_harga, 0, ',', '.') }}</strong></td>
                    <td>
                        @if($transaksi->pembayaran)
                            <span class="badge badge-paid">Lunas</span><br>
                            <span class="muted">{{ $transaksi->pembayaran->metode_pembayaran }}</span>
                        @else
                            <span class="badge badge-unpaid">Belum Lunas</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $statusClass }}">{{ $statusLabels[$transaksi->status_pesanan] ?? ucfirst($transaksi->status_pesanan) }}</span>
                    </td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="10">Belum ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
