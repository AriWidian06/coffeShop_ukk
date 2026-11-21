<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Bagi Kopi</title>
    <style>
        @page {
            margin: 88px 28px 58px 28px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8px;
            color: #0F172A;
            background: #ffffff;
            line-height: 1.35;
        }

        .header {
            position: fixed;
            top: -72px;
            left: 0;
            right: 0;
            height: 68px;
            border-bottom: 1px solid #E2E8F0;
            padding-bottom: 8px;
        }

        .header table,
        .footer table,
        .hero-table,
        .hero-stats,
        .two-col,
        .mini-table,
        .curve-table,
        .data-table,
        .closing-table {
            border-collapse: collapse;
            width: 100%;
        }

        .header td,
        .footer td,
        .hero-table td,
        .hero-stats td,
        .curve-table td {
            vertical-align: middle;
        }

        .logo-wrap {
            width: 48px;
        }

        .logo {
            width: 44px;
            height: 44px;
            border-radius: 11px;
            border: 1px solid #DBEAFE;
            background: #EFF6FF;
        }

        .brand {
            font-size: 17px;
            font-weight: bold;
            color: #0F172A;
            letter-spacing: 0.2px;
        }

        .tagline {
            margin-top: 2px;
            font-size: 5.8px;
            color: #64748B;
            letter-spacing: 1.15px;
            text-transform: uppercase;
        }

        .pretitle {
            margin-top: 4px;
            font-size: 6px;
            font-weight: bold;
            color: #2563EB;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-title {
            font-size: 13px;
            font-weight: bold;
            color: #1D4ED8;
            text-align: right;
            letter-spacing: 0.7px;
        }

        .doc-period {
            margin-top: 3px;
            font-size: 9px;
            font-weight: bold;
            color: #0F172A;
            text-align: right;
        }

        .doc-meta {
            margin-top: 2px;
            font-size: 6.2px;
            color: #64748B;
            text-align: right;
        }

        .footer {
            position: fixed;
            bottom: -42px;
            left: 0;
            right: 0;
            border-top: 1px solid #E2E8F0;
            padding-top: 5px;
            font-size: 6.3px;
            color: #64748B;
        }

        .footer .footer-brand {
            font-weight: bold;
            color: #1D4ED8;
        }

        .hero {
            background: #2563EB;
            border-radius: 14px;
            padding: 13px 15px 10px 15px;
            color: #ffffff;
            margin-bottom: 11px;
        }

        .hero-eyebrow {
            font-size: 6.2px;
            font-weight: bold;
            color: #DBEAFE;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .hero-value {
            margin-top: 2px;
            font-size: 25px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.2px;
            line-height: 1.1;
        }

        .hero-currency {
            font-size: 12px;
            font-weight: bold;
            color: #BFDBFE;
            letter-spacing: 0.4px;
        }

        .hero-sub {
            margin-top: 3px;
            font-size: 6.4px;
            color: #DBEAFE;
        }

        .hero-side {
            border-left: 1px solid #3B82F6;
            padding-left: 14px;
            text-align: right;
        }

        .hero-badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 8px;
            background: #1D4ED8;
            color: #DBEAFE;
            font-size: 5.4px;
            font-weight: bold;
            letter-spacing: 0.9px;
            text-transform: uppercase;
        }

        .hero-period {
            margin-top: 4px;
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
        }

        .hero-range {
            margin-top: 1px;
            font-size: 6.4px;
            color: #DBEAFE;
        }

        .hero-printed {
            margin-top: 2px;
            font-size: 5.8px;
            color: #BFDBFE;
        }

        .hero-stats {
            margin-top: 9px;
            border-top: 1px solid #3B82F6;
        }

        .hero-stats td {
            width: 25%;
            padding: 7px 8px 0 8px;
            border-left: 1px solid #3B82F6;
            vertical-align: top;
        }

        .hero-stats td:first-child {
            border-left: none;
            padding-left: 0;
        }

        .hero-stats .stat-label {
            font-size: 5.4px;
            font-weight: bold;
            color: #BFDBFE;
            text-transform: uppercase;
            letter-spacing: 0.55px;
        }

        .hero-stats .stat-value {
            margin-top: 2px;
            font-size: 9.2px;
            font-weight: bold;
            color: #ffffff;
        }

        .hero-stats .stat-sub {
            margin-top: 1px;
            font-size: 5.4px;
            color: #DBEAFE;
        }

        .two-col td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .two-col td:first-child {
            padding-right: 5px;
        }

        .two-col td:last-child {
            padding-left: 5px;
        }

        .card {
            border: 1px solid #E2E8F0;
            background: #ffffff;
            border-radius: 12px;
            padding: 9px 11px 8px 11px;
            page-break-inside: avoid;
        }

        .card-kicker {
            font-size: 5.3px;
            font-weight: bold;
            color: #2563EB;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card-title {
            margin-top: 1px;
            font-size: 10px;
            font-weight: bold;
            color: #0F172A;
        }

        .card-sub {
            margin-top: 1px;
            margin-bottom: 4px;
            font-size: 6.2px;
            color: #64748B;
        }

        .mini-table th {
            font-size: 5.6px;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.45px;
            padding: 4px 4px;
            border-bottom: 1px solid #E2E8F0;
            background: #F8FAFC;
        }

        .mini-table td {
            font-size: 6.9px;
            padding: 4px 4px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
        }

        .mini-table tr:last-child td {
            border-bottom: none;
        }

        .rank {
            display: inline-block;
            width: 14px;
            height: 14px;
            line-height: 14px;
            border-radius: 4px;
            background: #EFF6FF;
            color: #2563EB;
            font-size: 6.4px;
            font-weight: bold;
            text-align: center;
        }

        .product-name {
            font-size: 7px;
            font-weight: bold;
            color: #1E293B;
            line-height: 1.2;
        }

        .product-cat {
            font-size: 5.4px;
            color: #94A3B8;
            line-height: 1.2;
        }

        .money {
            font-weight: bold;
            color: #0F172A;
        }

        .progress-bar {
            display: block;
            height: 4px;
            border-radius: 2px;
            background: #E2E8F0;
            overflow: hidden;
            position: relative;
            width: 100%;
        }

        .progress-fill {
            display: block;
            height: 100%;
            background: #2563EB;
        }

        .progress-fill.warn {
            background: #F59E0B;
        }

        .pay-row td {
            border-bottom: none;
            padding-bottom: 1px;
        }

        .pay-bar td {
            border-bottom: 1px solid #F1F5F9;
            padding: 0 4px 6px 4px;
        }

        .pay-row:last-child td {
            border-bottom: none;
        }

        .porsi {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 7px;
            background: #EFF6FF;
            color: #1D4ED8;
            font-size: 5.8px;
            font-weight: bold;
        }

        .chart-card {
            margin-top: 10px;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            background: #ffffff;
            padding: 9px 11px 7px 11px;
            page-break-inside: avoid;
        }

        .chart-head td {
            vertical-align: bottom;
        }

        .legend {
            text-align: right;
            font-size: 5.6px;
            color: #64748B;
        }

        .legend-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 3px;
            margin: 0 2px 0 7px;
        }

        .chart-wrap {
            margin-top: 5px;
            border-top: 1px solid #F1F5F9;
            padding-top: 3px;
        }

        .curve-table {
            table-layout: fixed;
        }

        .curve-table td {
            width: 4.16%;
            height: 53px;
            vertical-align: bottom;
            padding: 0 1px;
        }

        .peak-label {
            height: 8px;
            font-size: 4.9px;
            font-weight: bold;
            color: #1D4ED8;
            text-align: center;
            white-space: nowrap;
        }

        .curve-bar {
            width: 92%;
            margin: 0 auto;
            border-radius: 3px 3px 0 0;
        }

        .curve-label {
            text-align: center;
            font-size: 4.8px;
            color: #94A3B8;
            height: 8px;
            margin-top: 2px;
        }

        .chart-note {
            margin-top: 2px;
            font-size: 5.8px;
            color: #64748B;
            text-align: right;
        }

        .insight-card {
            margin-top: 10px;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            background: #F8FAFC;
            padding: 7px 11px 8px 11px;
            page-break-inside: avoid;
        }

        .insight-kicker {
            font-size: 5.3px;
            font-weight: bold;
            color: #2563EB;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .insight-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        .insight-table td {
            width: 25%;
            vertical-align: top;
            padding: 1px 10px;
            border-left: 1px solid #E2E8F0;
        }

        .insight-table td:first-child {
            border-left: none;
            padding-left: 0;
        }

        .insight-label {
            font-size: 5.5px;
            font-weight: bold;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .insight-value {
            margin-top: 2px;
            font-size: 8.6px;
            font-weight: bold;
            color: #0F172A;
        }

        .insight-sub {
            margin-top: 1px;
            font-size: 5.5px;
            color: #94A3B8;
        }

        .detail-head {
            margin-top: 0;
            page-break-before: always;
        }

        .detail-head table {
            border-collapse: collapse;
            width: 100%;
        }

        .detail-head td {
            vertical-align: bottom;
        }

        .section-kicker {
            font-size: 5.6px;
            font-weight: bold;
            color: #2563EB;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section-title {
            margin-top: 1px;
            font-size: 12px;
            font-weight: bold;
            color: #0F172A;
        }

        .section-subtitle {
            margin-top: 1px;
            margin-bottom: 6px;
            font-size: 6.4px;
            color: #64748B;
        }

        .count-pill {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 9px;
            background: #EFF6FF;
            border: 1px solid #DBEAFE;
            color: #1D4ED8;
            font-size: 6.2px;
            font-weight: bold;
            text-align: right;
        }

        .data-table {
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            overflow: hidden;
            table-layout: fixed;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th {
            background: #EFF6FF;
            color: #1D4ED8;
            font-size: 5.8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 6px 5px;
            text-align: left;
            border-bottom: 1px solid #BFDBFE;
        }

        .data-table td {
            font-size: 6.8px;
            padding: 4px 5px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            background: #ffffff;
        }

        .data-table tbody tr:nth-child(even) td {
            background: #F8FAFC;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .ticket {
            font-weight: bold;
            color: #1D4ED8;
        }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 7px;
            font-size: 5.6px;
            font-weight: bold;
            line-height: 1.3;
        }

        .badge-pending { background: #FEF3C7; color: #92400E; }
        .badge-prosesed { background: #EDE9FE; color: #5B21B6; }
        .badge-ready { background: #DBEAFE; color: #1D4ED8; }
        .badge-completed { background: #D1FAE5; color: #047857; }
        .badge-paid { background: #D1FAE5; color: #047857; }
        .badge-unpaid { background: #FEE2E2; color: #B91C1C; }

        .text-right {
            text-align: right;
        }

        .muted {
            color: #64748B;
        }

        .soft {
            color: #94A3B8;
        }

        .empty-row td {
            padding: 18px;
            text-align: center;
            color: #64748B;
            font-size: 7.5px;
        }

        .closing-box {
            margin-top: 11px;
            border: 1px solid #BFDBFE;
            border-left: 3px solid #2563EB;
            border-radius: 10px;
            background: #EFF6FF;
            padding: 9px 11px;
            page-break-inside: avoid;
        }

        .closing-title {
            font-size: 9px;
            font-weight: bold;
            color: #1E3A8A;
        }

        .closing-sub {
            margin-top: 1px;
            font-size: 6px;
            color: #64748B;
        }

        .closing-table {
            margin-top: 5px;
        }

        .closing-table td {
            vertical-align: top;
        }

        .closing-label {
            font-size: 6.4px;
            color: #475569;
            padding: 2px 0;
        }

        .closing-value {
            font-size: 6.4px;
            font-weight: bold;
            color: #0F172A;
            padding: 2px 0;
            text-align: right;
        }

        .signature-label {
            font-size: 5.8px;
            font-weight: bold;
            color: #64748B;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .signature-line {
            margin: 28px auto 3px auto;
            border-top: 1px solid #94A3B8;
            width: 78%;
            height: 1px;
        }

        .signature-name {
            font-size: 5.9px;
            color: #475569;
            text-align: center;
        }

        .closing-note {
            margin-top: 6px;
            border-top: 1px solid #DBEAFE;
            padding-top: 5px;
            font-size: 5.8px;
            color: #64748B;
        }
    </style>
</head>
<body>
    @php
        $activeDays = (int) floor($start->diffInDays($end)) + 1;
        $averagePerDay = $totalTransaksi > 0 ? $totalPendapatan / max(1, $activeDays) : 0;
        $peakSale = (float) max($hourlySales);
        $peakHour = (int) array_search($peakSale, $hourlySales, true);
        $topProduct = $topProducts->first();
        $dominantPayment = $paymentStats->first();
    @endphp

    <div class="header">
        <table>
            <tr>
                <td class="logo-wrap" style="padding-right: 8px;">
                    @if($logoData)
                        <img src="data:image/jpeg;base64,{{ $logoData }}" class="logo" alt="Bagi Kopi">
                    @endif
                </td>
                <td style="padding-right: 8px;">
                    <div class="brand">Bagi Kopi</div>
                    <div class="tagline">Artisanal Brew &bull; Perkoci Eatery</div>
                    <div class="pretitle">Laporan Penjualan Resmi &bull; Waktu Indonesia Barat</div>
                </td>
                <td width="34%">
                    <div class="doc-title">LAPORAN PENJUALAN</div>
                    <div class="doc-period">{{ $periodeLabel }}</div>
                    <div class="doc-meta">{{ $start->translatedFormat('d M Y') }} &ndash; {{ $end->translatedFormat('d M Y') }}</div>
                    <div class="doc-meta">Dicetak {{ $nowWib->translatedFormat('d M Y, H:i') }} WIB</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <table>
            <tr>
                <td>
                    <span class="footer-brand">Bagi Kopi</span> &bull; Laporan Penjualan
                    &bull; Dicetak oleh {{ $dicetakOleh }} &bull; Dokumen dihasilkan otomatis oleh sistem
                </td>
            </tr>
        </table>
    </div>

    <div class="hero">
        <table class="hero-table">
            <tr>
                <td width="58%" style="vertical-align: top;">
                    <div class="hero-eyebrow">Total Pendapatan</div>
                    <div class="hero-value"><span class="hero-currency">Rp</span> {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                    <div class="hero-sub">Akumulasi {{ $activeDays }} hari operasional &bull; omzet bersih setelah diskon</div>
                </td>
                <td width="42%" class="hero-side" style="vertical-align: top;">
                    <div class="hero-badge">Laporan Resmi</div>
                    <div class="hero-period">{{ $periodeLabel }}</div>
                    <div class="hero-range">{{ $start->translatedFormat('d M Y') }} &ndash; {{ $end->translatedFormat('d M Y') }}</div>
                    <div class="hero-printed">Dicetak {{ $nowWib->translatedFormat('d M Y, H:i:s') }} WIB</div>
                </td>
            </tr>
        </table>
        <table class="hero-stats">
            <tr>
                <td>
                    <div class="stat-label">Total Transaksi</div>
                    <div class="stat-value">{{ number_format($totalTransaksi, 0, ',', '.') }}</div>
                    <div class="stat-sub">{{ $completedCount }} selesai &bull; {{ $unpaidCount }} belum lunas</div>
                </td>
                <td>
                    <div class="stat-label">Rata-rata / Struk</div>
                    <div class="stat-value">Rp {{ number_format($averageTransactionValue, 0, ',', '.') }}</div>
                    <div class="stat-sub">Average transaction value</div>
                </td>
                <td>
                    <div class="stat-label">Item Terjual</div>
                    <div class="stat-value">{{ number_format($totalItem, 0, ',', '.') }}</div>
                    <div class="stat-sub">{{ $totalTransaksi ? round($totalItem / $totalTransaksi, 1) : 0 }} item per struk</div>
                </td>
                <td>
                    <div class="stat-label">Diskon Promo</div>
                    <div class="stat-value">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</div>
                    <div class="stat-sub">{{ $promoCount }} struk menggunakan promo</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="two-col">
        <table>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-kicker">Kinerja Menu</div>
                        <div class="card-title">Top 5 Produk Terlaris</div>
                        <div class="card-sub">Berdasarkan kuantitas terjual &bull; {{ $periodeLabel }}</div>
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th width="9%">#</th>
                                    <th>Produk</th>
                                    <th width="13%" class="text-right">Qty</th>
                                    <th width="34%" class="text-right">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $index => $product)
                                    <tr>
                                        <td><span class="rank">{{ $index + 1 }}</span></td>
                                        <td>
                                            <div class="product-name">{{ $product->nama_produk }}</div>
                                            <div class="product-cat">{{ $product->kategori }}</div>
                                        </td>
                                        <td class="text-right"><strong>{{ number_format($product->total_qty, 0, ',', '.') }}</strong> <span class="soft">pcs</span></td>
                                        <td class="text-right money">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="muted" style="text-align:center;padding:12px;">Belum ada data produk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-kicker">Penyelesaian Tagihan</div>
                        <div class="card-title">Metode Pembayaran</div>
                        <div class="card-sub">Distribusi transaksi &bull; {{ $periodeLabel }}</div>
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th>Metode</th>
                                    <th width="12%" class="text-right">Struk</th>
                                    <th width="35%" class="text-right">Nominal</th>
                                    <th width="14%" class="text-right">Porsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentStats as $payment)
                                    @php
                                        $share = $totalPembayaran > 0 ? ($payment->total / $totalPembayaran) * 100 : 0;
                                        $isUnpaid = $payment->metode === 'Belum Lunas';
                                    @endphp
                                    <tr class="pay-row">
                                        <td><strong>{{ ucfirst($payment->metode) }}</strong></td>
                                        <td class="text-right">{{ number_format($payment->jumlah, 0, ',', '.') }}</td>
                                        <td class="text-right money">Rp {{ number_format($payment->total, 0, ',', '.') }}</td>
                                        <td class="text-right"><span class="porsi">{{ round($share, 1) }}%</span></td>
                                    </tr>
                                    <tr class="pay-bar">
                                        <td colspan="4">
                                            <span class="progress-bar">
                                                <span class="progress-fill {{ $isUnpaid ? 'warn' : '' }}" style="width: {{ $share }}%;"></span>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="muted" style="text-align:center;padding:12px;">Belum ada pembayaran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="chart-card">
        <table class="chart-head">
            <tr>
                <td>
                    <div class="card-kicker">Pola Kunjungan</div>
                    <div class="card-title">Ritme Omzet per Jam (WIB)</div>
                    <div class="card-sub">Distribusi omzet per jam pada {{ $periodeLabel }}</div>
                </td>
                <td width="38%" class="legend">
                    <span class="legend-dot" style="background:#BFDBFE;"></span> Omzet reguler
                    <span class="legend-dot" style="background:#2563EB;"></span> Jam tersibuk
                </td>
            </tr>
        </table>
        <div class="chart-wrap">
            <table class="curve-table">
                <tr>
                    @for ($hour = 0; $hour < 24; $hour++)
                        @php
                            $omzet = $hourlySales[$hour] ?? 0;
                            $height = $omzet > 0 ? max(4, ($omzet / $maxHourlySales) * 33) : 0;
                            $isPeak = $omzet > 0 && $hour === $peakHour;
                        @endphp
                        <td>
                            <div class="peak-label">@if($isPeak) Rp {{ round($omzet / 1000) }}k @endif</div>
                            <div class="curve-bar" style="height: {{ $height }}px; background: {{ $isPeak ? '#2563EB' : '#BFDBFE' }};"></div>
                            <div class="curve-label">{{ str_pad((string) ($hour), 2, '0', STR_PAD_LEFT) }}</div>
                        </td>
                    @endfor
                </tr>
            </table>
        </div>
        <div class="chart-note">
            @if($peakSale > 0)
                Puncak omzet terjadi pada pukul {{ str_pad((string) $peakHour, 2, '0', STR_PAD_LEFT) }}.00 WIB sebesar Rp {{ number_format($peakSale, 0, ',', '.') }}.
            @else
                Belum ada omzet yang tercatat pada periode ini.
            @endif
        </div>
    </div>

    <div class="insight-card">
        <div class="insight-kicker">Sorotan Periode</div>
        <table class="insight-table">
            <tr>
                <td>
                    <div class="insight-label">Produk Terlaris</div>
                    <div class="insight-value">{{ $topProduct->nama_produk ?? 'Belum ada data' }}</div>
                    <div class="insight-sub">{{ $topProduct ? number_format($topProduct->total_qty, 0, ',', '.') . ' pcs terjual' : 'Tidak ada penjualan' }}</div>
                </td>
                <td>
                    <div class="insight-label">Metode Dominan</div>
                    <div class="insight-value">{{ ucfirst($dominantPayment->metode ?? 'Belum ada data') }}</div>
                    <div class="insight-sub">{{ $dominantPayment && $totalPembayaran > 0 ? round(($dominantPayment->total / $totalPembayaran) * 100, 1) . '% dari nominal' : 'Belum ada pembayaran' }}</div>
                </td>
                <td>
                    <div class="insight-label">Jam Tersibuk</div>
                    <div class="insight-value">{{ $peakSale > 0 ? str_pad((string) $peakHour, 2, '0', STR_PAD_LEFT) . '.00 WIB' : '-' }}</div>
                    <div class="insight-sub">{{ $peakSale > 0 ? 'Rp ' . number_format($peakSale, 0, ',', '.') : 'Belum ada omzet' }}</div>
                </td>
                <td>
                    <div class="insight-label">Rata-rata per Hari</div>
                    <div class="insight-value">Rp {{ number_format($averagePerDay, 0, ',', '.') }}</div>
                    <div class="insight-sub">{{ $activeDays }} hari operasional</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="detail-head" style="page-break-inside: avoid;">
        <table>
            <tr>
                <td>
                    <div class="section-kicker">Buku Kas &amp; Laporan Keuangan</div>
                    <div class="section-title">Arus Kas &amp; Laba Rugi</div>
                    <div class="section-subtitle">Pengeluaran yang telah disetujui &bull; nilai dalam Rupiah</div>
                </td>
            </tr>
        </table>
        <table class="closing-table">
            <tr>
                <td width="49%" style="padding-right: 10px;">
                    <table class="data-table">
                        <thead><tr><th>Laba Rugi</th><th class="text-right">Nilai</th></tr></thead>
                        <tbody>
                            <tr><td>Pendapatan penjualan</td><td class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td></tr>
                            <tr><td>Harga pokok penjualan (HPP)</td><td class="text-right">-Rp {{ number_format($hpp, 0, ',', '.') }}</td></tr>
                            <tr><td><strong>Laba Kotor</strong></td><td class="text-right"><strong>Rp {{ number_format($labaKotor, 0, ',', '.') }}</strong></td></tr>
                            @foreach (['bahan_baku' => 'Bahan Baku', 'operasional' => 'Operasional', 'gaji' => 'Gaji Karyawan', 'lainnya' => 'Lainnya'] as $key => $label)
                                <tr><td>Pengeluaran {{ $label }}</td><td class="text-right">Rp {{ number_format($pengeluaranPerKategori->get($key, 0), 0, ',', '.') }}</td></tr>
                            @endforeach
                            <tr><td>Total Pengeluaran</td><td class="text-right">-Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td></tr>
                            <tr><td><strong>Laba Bersih</strong></td><td class="text-right"><strong>Rp {{ number_format($labaBersih, 0, ',', '.') }}</strong></td></tr>
                        </tbody>
                    </table>
                </td>
                <td width="2%"></td>
                <td width="49%" style="padding-left: 10px;">
                    <table class="data-table">
                        <thead><tr><th>Akun Kas</th><th class="text-right">Awal</th><th class="text-right">Masuk</th><th class="text-right">Keluar</th><th class="text-right">Akhir</th></tr></thead>
                        <tbody>
                            @forelse ($arusKas as $akun)
                                <tr>
                                    <td>{{ $akun->nama }}</td>
                                    <td class="text-right">{{ number_format($akun->saldo_awal, 0, ',', '.') }}</td>
                                    <td class="text-right">+{{ number_format($akun->masuk, 0, ',', '.') }}</td>
                                    <td class="text-right">-{{ number_format($akun->keluar, 0, ',', '.') }}</td>
                                    <td class="text-right"><strong>{{ number_format($akun->saldo_akhir, 0, ',', '.') }}</strong></td>
                                </tr>
                            @empty
                                <tr><td colspan="5">Belum ada akun kas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="closing-box">
        <div class="closing-title">Rangkuman Akhir &amp; Otorisasi</div>
        <div class="closing-sub">Konsolidasi periode {{ $periodeLabel }} &bull; seluruh nilai dalam Rupiah</div>
        <table class="closing-table">
            <tr>
                <td style="width: 60%; padding-right: 14px;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td class="closing-label">Total pendapatan bersih</td>
                            <td class="closing-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="closing-label">Transaksi tuntas</td>
                            <td class="closing-value">{{ number_format($completedCount, 0, ',', '.') }} dari {{ number_format($totalTransaksi, 0, ',', '.') }} struk</td>
                        </tr>
                        <tr>
                            <td class="closing-label">Struk belum lunas</td>
                            <td class="closing-value" style="color:#B91C1C;">{{ number_format($unpaidCount, 0, ',', '.') }} struk</td>
                        </tr>
                        <tr>
                            <td class="closing-label">Rata-rata omzet per hari</td>
                            <td class="closing-value">Rp {{ number_format($averagePerDay, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="closing-label">Omzet puncak per jam</td>
                            <td class="closing-value">Rp {{ number_format($peakSale, 0, ',', '.') }} <span class="soft">({{ str_pad((string) $peakHour, 2, '0', STR_PAD_LEFT) }}.00 WIB)</span></td>
                        </tr>
                    </table>
                </td>
                <td style="width: 40%;">
                    <div class="signature-label">Menyetujui, Manager</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $dicetakOleh }}</div>
                </td>
            </tr>
        </table>
        <div class="closing-note">
            Catatan: Data diambil langsung dari database Bagi Kopi pada rentang WIB yang dipilih. Nilai pendapatan merupakan omzet bersih setelah potongan promo. Rincian seluruh transaksi pada periode ini dilampirkan pada halaman berikutnya. Laporan ini sah tanpa tanda tangan basah.
        </div>
    </div>
</body>
</html>
