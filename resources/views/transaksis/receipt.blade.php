<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk #{{ $transaksi->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #f3f4f6;
            margin: 0;
            padding: 20px;
        }
        .receipt-container {
            max-width: 300px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { font-size: 16px; margin: 0 0 5px 0; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 11px; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 11px; }
        .item-row { margin-bottom: 8px; }
        .item-name { font-weight: bold; }
        .item-detail { display: flex; justify-content: space-between; font-size: 11px; margin-top: 2px; }
        .item-custom { font-size: 10px; font-style: italic; color: #555; margin-top: 2px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 12px; }
        .total-grand { font-weight: bold; font-size: 14px; margin-top: 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 11px; }
        
        .print-btn {
            display: block;
            width: 100%;
            max-width: 300px;
            margin: 20px auto;
            padding: 12px;
            background: #2563EB;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            font-family: sans-serif;
        }
        .print-btn:hover { background: #1D4ED8; }

        @media print {
            body { background: #fff; padding: 0; }
            .receipt-container { box-shadow: none; padding: 0; max-width: 100%; }
            .print-btn { display: none !important; }
        }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()">🖨️ Cetak Struk</button>

    <div class="receipt-container">
        <div class="header">
            <h1>BAGI KOPI</h1>
            <p>Espresso & Space</p>
            <p>Senopati, Jakarta</p>
        </div>

        <div class="divider"></div>

        <div class="info-row">
            <span>No. Transaksi:</span>
            <span>#{{ $transaksi->id }}</span>
        </div>
        <div class="info-row">
            <span>Tanggal:</span>
            <span>{{ $transaksi->waktu_transaksi?->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span>Kasir:</span>
            <span>{{ $transaksi->karyawan->nama_karyawan ?? 'Web Order' }}</span>
        </div>
        <div class="info-row">
            <span>Meja:</span>
            <span>{{ $transaksi->meja ? $transaksi->meja->nomor_meja : 'Take Away' }}</span>
        </div>

        <div class="divider"></div>

        @php
            $subtotalItems = (float) $transaksi->detail_transaksis->sum('subtotal');
            $pajakLayanan = max(0, (float) $transaksi->total_harga - $subtotalItems);
        @endphp

        @foreach ($transaksi->detail_transaksis as $detail)
            <div class="item-row">
                <div class="item-name">{{ $detail->produk->nama_produk ?? 'Produk dihapus' }}</div>
                <div class="item-detail">
                    <span>{{ $detail->QTY ?? $detail->qty }} x Rp {{ number_format($detail->produk->harga_jual ?? 0, 0, ',', '.') }}</span>
                    <span>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                </div>
                @if ($detail->customization)
                    <div class="item-custom">* {{ $detail->customization }}</div>
                @endif
            </div>
        @endforeach

        <div class="divider"></div>

        <div class="total-row">
            <span>Subtotal</span>
            <span>Rp {{ number_format($subtotalItems, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Pajak & Layanan</span>
            <span>Rp {{ number_format($pajakLayanan, 0, ',', '.') }}</span>
        </div>
        
        <div class="divider"></div>

        <div class="total-row total-grand">
            <span>TOTAL</span>
            <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
        </div>

        @if ($transaksi->pembayaran)
            <div class="divider"></div>
            <div class="info-row">
                <span>Bayar ({{ strtoupper($transaksi->pembayaran->metode_pembayaran) }})</span>
                <span>Rp {{ number_format($transaksi->pembayaran->jumlah_bayar, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span>Kembalian</span>
                <span>Rp {{ number_format($transaksi->pembayaran->kembalian, 0, ',', '.') }}</span>
            </div>
        @endif

        <div class="footer">
            <p>Terima kasih telah berkunjung!</p>
            <p>Simpan struk ini sebagai bukti pembayaran yang sah.</p>
        </div>
    </div>

</body>
</html>