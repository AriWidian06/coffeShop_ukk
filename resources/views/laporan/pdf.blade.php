<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Bagi Kopi</title>
    <style>
        @page {
            margin: 90px 28px 60px 28px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.2px;
            color: #2b2118;
            background: #ffffff;
            line-height: 1.35;
        }

        .header {
            position: fixed;
            top: -74px;
            left: 0;
            right: 0;
            height: 70px;
            border-bottom: 2px solid #6f4e37;
            padding-bottom: 6px;
        }

        .header table,
        .footer table,
        .summary-table,
        .two-col,
        .curve-table,
        .closing-table,
        .data-table,
        .mini-table {
            border-collapse: collapse;
            width: 100%;
        }

        .header td,
        .footer td,
        .curve-table td {
            vertical-align: middle;
        }

        .logo {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            border: 1px solid #e7ddd2;
            background: #f7f3ee;
        }

        .brand {
            font-size: 20px;
            font-weight: bold;
            color: #2b2118;
            letter-spacing: 0.3px;
        }

        .tagline {
            margin-top: 2px;
            font-size: 7px;
            color: #8a7766;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .pretitle {
            margin-top: 4px;
            font-size: 7px;
            font-weight: bold;
            color: #a17d5d;
            text-transform: uppercase;
            letter-spacing: 1.1px;
        }

        .doc-title {
            font-size: 16px;
            font-weight: bold;
            color: #6f4e37;
            text-align: right;
            letter-spacing: 0.8px;
        }

        .doc-period {
            margin-top: 3px;
            font-size: 9.5px;
            font-weight: bold;
            color: #3c2f25;
            text-align: right;
        }

        .doc-meta {
            margin-top: 2px;
            font-size: 7px;
            color: #8a7766;
            text-align: right;
        }

        .footer {
            position: fixed;
            bottom: -44px;
            left: 0;
            right: 0;
            border-top: 1px solid #e7ddd2;
            padding-top: 6px;
            font-size: 7px;
            color: #8a7766;
        }

        .footer td {
            vertical-align: middle;
        }

        .card {
            border: 1px solid #e7ddd2;
            background: #ffffff;
            border-radius: 10px;
            padding: 8px 10px;
        }

        .section-title {
            margin: 0 0 6px 0;
            font-size: 10.5px;
            font-weight: bold;
            color: #3c2f25;
        }

        .section-subtitle {
            margin: -3px 0 8px 0;
            font-size: 6.8px;
            color: #8a7766;
        }

        .summary-table {
            border-spacing: 6px 0;
            margin: 0 -6px 12px -6px;
        }

        .summary-table td {
            width: 16.66%;
            background: #f7f3ee;
            border: 1px solid #e7ddd2;
            border-top: 4px solid #6f4e37;
            padding: 8px 9px;
            vertical-align: top;
            border-radius: 9px;
        }

        .stat-label {
            font-size: 6.4px;
            font-weight: bold;
            color: #8a7766;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            margin-top: 4px;
            font-size: 13.5px;
            font-weight: bold;
            color: #2b2118;
        }

        .stat-sub {
            margin-top: 3px;
            font-size: 6.3px;
            color: #8a7766;
        }

        .two-col td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .two-col td:first-child {
            padding-right: 6px;
        }

        .two-col td:last-child {
            padding-left: 6px;
        }

        .panel {
            border: 1px solid #e7ddd2;
            background: #ffffff;
            padding: 9px 10px;
            border-radius: 10px;
        }

        .mini-table th {
            font-size: 6.6px;
            color: #8a7766;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 4px;
            border-bottom: 1px solid #e7ddd2;
        }

        .mini-table td {
            font-size: 7.4px;
            padding: 4px;
            border-bottom: 1px solid #f3ede7;
            vertical-align: top;
        }

        .mini-table tr:last-child td {
            border-bottom: none;
        }

        .progress-bar {
            display: inline-block;
            height: 6px;
            border-radius: 3px;
            background: #e7ddd2;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            display: inline-block;
            height: 100%;
            background: #6f4e37;
        }

        .curve-wrapper {
            margin: 0 -2px 10px -2px;
            border: 1px solid #efe7df;
            border-radius: 10px;
            background: #f7f3ee;
            padding: 6px 4px 3px;
        }

        .curve-table {
            table-layout: fixed;
        }

        .curve-table td {
            width: 4.16%;
            height: 52px;
            vertical-align: bottom;
            padding: 0 1px;
        }

        .curve-bar {
            width: 93%;
            margin: 0 auto;
            background: #b8c7a4;
            border-radius: 3px 3px 0 0;
        }

        .curve-label {
            text-align: center;
            font-size: 5.4px;
            color: #8a7766;
            height: 10px;
            margin-top: 3px;
        }

        .data-table {
            border: 1px solid #e7ddd2;
            border-radius: 10px;
            overflow: hidden;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th {
            background: #6f4e37;
            color: #ffffff;
            font-size: 6.6px;
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
            font-size: 7.1px;
            padding: 5px;
            border-bottom: 1px solid #efe7df;
            border-right: 1px solid #f6f1eb;
            vertical-align: top;
            background: #ffffff;
        }

        .data-table td:last-child {
            border-right: none;
        }

        .data-table tbody tr:nth-child(even) td {
            background: #faf7f3;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 7px;
            font-size: 6.2px;
            font-weight: bold;
        }

        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-prosesed { background: #ede9fe; color: #5b21b6; }
        .badge-ready { background: #dbeafe; color: #1d4ed8; }
        .badge-completed { background: #d1fae5; color: #047857; }
        .badge-paid { background: #d1fae5; color: #047857; }
        .badge-unpaid { background: #fee2e2; color: #b91c1c; }

        .text-right {
            text-align: right;
        }

        .muted {
            color: #8a7766;
        }

        .empty-row td {
            padding: 16px;
            text-align: center;
            color: #8a7766;
            font-size: 8px;
        }

        .closing-box {
            margin-top: 12px;
            border: 1px solid #e7ddd2;
            border-radius: 10px;
            background: #f7f3ee;
            padding: 10px 12px;
            page-break-inside: avoid;
        }

        .closing-box h4 {
            margin: 0 0 8px 0;
            font-size: 9.5px;
            color: #3c2f25;
        }

        .signature-row {
            width: 100%;
            margin-top: 14px;
        }

        .signature-row td {
            width: 50%;
            height: 60px;
            vertical-align: top;
            padding: 4px;
        }

        .signature-line {
            margin-top: 36px;
            border-top: 1px solid #8a7766;
            width: 72%;
            height: 1px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td width="15%" style="padding-right: 8px;">
                    @if($logoData)
                        <img src="data:image/jpeg;base64,{{ $logoData }}" class="logo" alt="Bagi Kopi">
                    @endif
                </td>
                <td width="52%" style="padding-right: 8px;">
                    <div class="brand">Bagi Kopi</div>
                    <div class="tagline">Artisanal Brew &bull; Perkoci Eatery</div>
                    <div class="pretitle">Laporan Penjualan Resmi &bull; Periode WIB</div>
                </td>
                <td width="33%">
                    <div class="doc-title">EXPORT PDF</div>
                    <div class="doc-period">{{ $periodeLabel }}</div>
                    <div class="doc-meta">{{ $start->translatedFormat('d M Y') }} &ndash; {{ $end->translatedFormat('d M Y') }}</div>
                    <div class="doc-meta">Dicetak {{ $nowWib->translatedFormat('d M Y, H:i:s') }} WIB</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <table>
            <tr>
                <td>
                    Dicetak oleh {{ $dicetakOleh }} &bull; Dokumen resmi Bagi Kopi
                </td>
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
                <div class="stat-sub">{{ $totalTransaksi ? round($totalItem / $totalTransaksi, 1) : 0 }} item / struk</div>
            </td>
            <td>
                <div class="stat-label">Rata-rata Omzet / Hari</div>
                <div class="stat-value">Rp {{ number_format($totalTransaksi > 0 ? $totalPendapatan / max(1, (int) floor($start->diffInDays($end)) + 1) : 0, 0, ',', '.') }}</div>
                <div class="stat-sub">{{ (int) floor($start->diffInDays($end)) + 1 }} hari</div>>
            </td>
            <td>
                <div class="stat-label">Status Completed</div>
                <div class="stat-value">{{ number_format($transaksis->where('status_pesanan', 'completed')->count(), 0, ',', '.') }}</div>
                <div class="stat-sub">{{ $totalTransaksi > 0 ? round(($transaksis->where('status_pesanan', 'completed')->count() / $totalTransaksi) * 100) : 0 }}% penjualan tuntas</div>
            </td>
        </tr>
    </table>

    <div class="two-col">
        <table>
            <tr>
                <td>
                    <div class="panel">
                        <div class="section-title">Top 5 Produk Terlaris</div>
                        <div class="section-subtitle">Berdasarkan kuantitas terjual • {{ $periodeLabel }}</div>
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th width="7%">#</th>
                                    <th>Produk</th>
                                    <th width="9%" class="text-right">Qty</th>
                                    <th width="30%" class="text-right">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $index => $product)
                                    <tr>
                                        <td><strong>{{ $index + 1 }}</strong></td>
                                        <td>
                                            <strong>{{ $product->nama_produk }}</strong><br>
                                            <span class="muted">{{ $product->kategori }}</span>
                                        </td>
                                        <td class="text-right">{{ number_format($product->total_qty, 0, ',', '.') }} pcs</td>
                                        <td class="text-right">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="muted" style="text-align:center;padding:10px;">Belum ada data produk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </td>
                <td>
                    <div class="panel">
                        <div class="section-title">Metode Pembayaran</div>
                        <div class="section-subtitle">Distribusi penyelesaian tagihan • {{ $periodeLabel }}</div>
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th>Metode</th>
                                    <th width="11%" class="text-right">Transaksi</th>
                                    <th width="34%" class="text-right">Nominal</th>
                                    <th width="12%" class="text-right">Porsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentStats as $payment)
                                    <tr>
                                        <td><strong>{{ $payment->metode }}</strong></td>
                                        <td class="text-right">{{ number_format($payment->jumlah, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($payment->total, 0, ',', '.') }}</td>
                                        <td class="text-right">
                                            {{ $totalPembayaran > 0 ? round(($payment->total / $totalPembayaran) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="padding-top:0;padding-bottom:6px;">
                                            <div class="progress-bar" style="width:100%;">
                                                <span class="progress-fill" style="width: {{ $totalPembayaran > 0 ? ($payment->total / $totalPembayaran) * 100 : 0 }}%;"></span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="muted" style="text-align:center;padding:10px;">Belum ada pembayaran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Ritme Omzet per Jam (WIB)</div>
    <div class="section-subtitle">Distribusi omzet per jam pada {{ $periodeLabel }}</div>
    <div class="curve-wrapper">
        <table class="curve-table">
            <tr>
                @for ($hour = 0; $hour < 24; $hour++)
                    @php
                        $omzet = $hourlySales[$hour] ?? 0;
                        $height = $omzet > 0 ? max(4, ($omzet / $maxHourlySales) * 36) : 0;
                        $margin = 38 - $height;
                        $peakSale = (float) max($hourlySales);
                        $isPeak = $omzet > 0 && $omzet >= $peakSale;
                    @endphp
                    <td>
                        <div style="margin-top: {{ $margin }}px; height: {{ $height }}px; background: {{ $isPeak ? '#6f4e37' : '#b8c7a4' }}; border-radius: 3px 3px 0 0;"></div>
                        <div class="curve-label">{{ str_pad((string) ($hour), 2, '0', STR_PAD_LEFT) }}</div>
                    </td>
                @endfor
            </tr>
        </table>
    </div>

    <div style="font-size:6.8px;font-weight:bold;color:#6f4e37;text-align:right;margin:-6px 0 10px 0;">
        Peak jam diberi warna coklat pekat.
    </div>

    <div class="section-title">Rincian Transaksi</div>
    <div class="section-subtitle">{{ number_format($totalTransaksi, 0, ',', '.') }} transaksi pada {{ $periodeLabel }}</div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="11%">Waktu (WIB)</th>
                <th width="5%">Tiket</th>
                <th width="10%">Meja / Tipe</th>
                <th width="13%">Kasir / Sumber</th>
                <th width="5%" class="text-right">Item</th>
                <th width="9%" class="text-right">Diskon</th>
                <th width="10%" class="text-right">Total</th>
                <th width="10%">Pembayaran</th>
                <th width="9%">Status</th>
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

    <div class="closing-box">
        <h4>Rangkuman Akhir & Otorisasi</h4>
        <table style="width:100%;border-collapse:collapse;">
            <tr>
                <td style="width:62%;vertical-align:top;">
                    <table style="width:100%;border-collapse:collapse;font-size:7.4px;">
                        <tr>
                            <td style="padding:3px 0;">Total transaksi tuntas</td>
                            <td style="padding:3px 0;font-weight:bold;text-align:right;">{{ $transaksis->where('status_pesanan', 'completed')->count() }} struk</td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0;">Struk belum lunas</td>
                            <td style="padding:3px 0;font-weight:bold;text-align:right;color:#b91c1c;">{{ $transaksis->whereNull('pembayaran')->count() }} struk</td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0;">Omzet puncak per jam</td>
                            <td style="padding:3px 0;font-weight:bold;text-align:right;">Rp {{ number_format(max($hourlySales), 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0;">Rentang periode</td>
                            <td style="padding:3px 0;font-weight:bold;text-align:right;">{{ $periodeLabel }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width:38%;vertical-align:top;">
                    <div style="font-size:7px;color:#8a7766;font-weight:bold;margin-bottom:6px;text-align:center;">Menyetujui (Manager)</div>
                    <div class="signature-line"></div>
                    <div style="font-size:6.5px;color:#8a7766;text-align:center;margin-top:3px;">{{ $dicetakOleh }}</div>
                </td>
            </tr>
        </table>
        <div style="margin-top:8px;font-size:6.8px;color:#8a7766;">
            Catatan: Data laporan diambil langsung dari database dalam rentang WIB yang dipilih. Nilai diskon menampilkan potongan promo pada struk.
        </div>
    </div>
</body>
</html>
