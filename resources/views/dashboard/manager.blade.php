@extends('layouts.manager')

@section('content')
@php
    $jumlahHari = (int) $start->diffInDays($end) + 1;
    $rataTransaksiHarian = $jumlahHari > 0 ? round($totalTransaksiPeriode / $jumlahHari, 1) : 0;
    $rataPendapatanHarian = $jumlahHari > 0 ? round($totalPendapatanPeriode / $jumlahHari) : 0;
    $rataItemPerTransaksi = $totalTransaksiPeriode > 0 ? round($totalItemPeriode / $totalTransaksiPeriode, 1) : 0;
    $totalPembayaran = $paymentStats->sum(fn ($row) => (float) $row->total);
    $maxProdukRevenue = max(1, (float) $produkTerlaris->max('total_revenue'));
    $exportParams = ['periode' => $periode, 'tanggal' => $selectedDate->toDateString()];
@endphp

<div class="min-h-screen bg-slate-50">

    <!-- TOP HEADER -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs text-slate-500 font-semibold">Bagi Kopi • Manager Console</span>
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full">Data WIB</span>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900">Laporan Penjualan</h1>
                    <p class="text-sm text-slate-500 mt-1">Periode {{ $periodeLabel }} • {{ $jumlahHari }} hari</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-slate-50 rounded-xl border border-slate-200 text-right">
                        <p class="text-[10px] uppercase tracking-wider font-bold text-slate-400">{{ $nowWib->translatedFormat('l') }}</p>
                        <p class="text-sm font-bold text-slate-800">{{ $nowWib->translatedFormat('d F Y') }}</p>
                        <p class="text-xs font-bold text-primary-600 tabular-nums" id="manager-live-clock">{{ $nowWib->format('H:i:s') }} WIB</p>
                    </div>
                    <a href="{{ route('laporan.export', $exportParams) }}" class="px-4 py-2.5 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="p-6 space-y-6">

        <!-- PERIOD FILTER -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex bg-slate-100 rounded-xl p-1 gap-1">
                @foreach(['harian' => 'Harian', 'mingguan' => 'Mingguan', 'bulanan' => 'Bulanan'] as $mode => $label)
                    <a href="{{ route('dashboard', ['periode' => $mode, 'tanggal' => $selectedDate->toDateString()]) }}"
                       class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $periode === $mode ? 'bg-white text-primary-700 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('dashboard', ['periode' => $periode, 'tanggal' => $prevReference->toDateString()]) }}"
                   title="Periode sebelumnya"
                   class="w-9 h-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>

                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <input type="date" name="tanggal" value="{{ $selectedDate->toDateString() }}" onchange="this.form.submit()"
                           class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <button type="submit" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">Terapkan</button>
                </form>

                <a href="{{ route('dashboard', ['periode' => $periode, 'tanggal' => $nextReference->toDateString()]) }}"
                   title="Periode berikutnya"
                   class="w-9 h-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>

                <a href="{{ route('dashboard', ['periode' => $periode]) }}"
                   class="px-3 py-2 bg-primary-50 hover:bg-primary-100 text-primary-700 rounded-xl text-xs font-bold transition">Hari Ini</a>
            </div>
        </div>

        <!-- SNAPSHOT REAL-TIME HARI INI -->
        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-5 border border-blue-200 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-600 font-semibold mb-1">Snapshot Real-time Hari Ini (WIB)</p>
                    <p class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalPendapatanHariIni, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-500">{{ $totalTransaksiHariIni }} transaksi • {{ number_format($totalItemHariIni, 0, ',', '.') }} item terjual</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-3 py-1.5 bg-white/70 border border-blue-200 text-blue-700 rounded-lg text-xs font-bold">
                    Shift Aktif: {{ $shift }}
                </div>
                <div class="px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">
                    Langsung dari database
                </div>
            </div>
        </div>

        <!-- STATISTICS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Pendapatan -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Total Pendapatan</p>
                        <h3 class="text-3xl font-bold text-slate-900">Rp {{ number_format($totalPendapatanPeriode, 0, ',', '.') }}</h3>
                        <p class="text-xs text-slate-500 mt-1">Rata-rata Rp {{ number_format($rataPendapatanHarian, 0, ',', '.') }} / hari</p>
                    </div>
                    @if($pendapatanGrowth === null)
                        <div class="px-2 py-1 bg-slate-100 text-slate-500 rounded-lg text-xs font-bold whitespace-nowrap">Belum ada pembanding</div>
                    @else
                        <div class="px-2 py-1 rounded-lg text-xs font-bold whitespace-nowrap {{ $pendapatanGrowth >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $pendapatanGrowth >= 0 ? '+' : '' }}{{ $pendapatanGrowth }}%
                        </div>
                    @endif
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Periode sebelumnya</span>
                    <span class="font-bold text-slate-800">Rp {{ number_format($pendapatanSebelumnya, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Jumlah Transaksi -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Jumlah Transaksi</p>
                        <h3 class="text-3xl font-bold text-slate-900">{{ number_format($totalTransaksiPeriode, 0, ',', '.') }}</h3>
                        <p class="text-xs text-slate-500 mt-1">Total tiket pada periode ini</p>
                    </div>
                    <div class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold whitespace-nowrap">{{ number_format($totalItemPeriode, 0, ',', '.') }} item</div>
                </div>
                <div class="flex items-center gap-4 mt-4 pt-4 border-t border-slate-100">
                    <div class="flex-1">
                        <p class="text-xs text-slate-500">Rata-rata Harian</p>
                        <p class="text-sm font-bold text-slate-900">{{ $rataTransaksiHarian }} transaksi/hari</p>
                    </div>
                    <div class="flex-1 text-right">
                        <p class="text-xs text-slate-500">Peak Jam</p>
                        <p class="text-sm font-bold text-primary-600">{{ $peakHourLabel }}</p>
                    </div>
                </div>
            </div>

            <!-- Nilai Rata-rata Transaksi -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Nilai Rata-rata Transaksi (ATV)</p>
                        <h3 class="text-3xl font-bold text-slate-900">Rp {{ number_format($averageTransactionValue, 0, ',', '.') }}</h3>
                        <p class="text-xs text-slate-500 mt-1">per struk</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span class="text-slate-600">Rata-rata Item / Transaksi</span>
                    </div>
                    <span class="text-xs font-bold text-slate-900">{{ $rataItemPerTransaksi }} item</span>
                </div>
            </div>
        </div>

        <!-- CHARTS SECTION -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Kurva Penjualan -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-2 h-2 rounded-full bg-primary-600"></div>
                    <h3 class="font-bold text-slate-900">Kurva Penjualan per Jam</h3>
                </div>
                <p class="text-xs text-slate-500 mb-6">Distribusi omzet per jam WIB • {{ $periodeLabel }}</p>

                @php
                    $maxOmzetJam = max(1, (float) $salesByHour->max());
                @endphp
                <div class="overflow-x-auto pb-2">
                    <div class="min-w-[760px]">
                        <div class="flex items-end gap-2 h-40 px-1">
                            @for ($hour = 0; $hour < 24; $hour++)
                                @php
                                    $row = $salesByHour->get($hour);
                                    $omzet = $row ? (float) $row : 0;
                                    $jumlah = $transaksiPerJam->firstWhere('jam', $hour)?->jumlah ?? 0;
                                    $height = $omzet > 0 ? ($omzet / $maxOmzetJam) * 100 : 0;
                                    $isPeak = $peakHour === $hour;
                                @endphp
                                <div class="flex-1 h-full flex items-end relative group"
                                     title="{{ sprintf('%02d:00', $hour) }} • Rp {{ number_format($omzet, 0, ',', '.') }} • {{ $jumlah }} transaksi">
                                    @if($isPeak)
                                        <span class="absolute -top-5 left-1/2 -translate-x-1/2 text-[9px] font-bold text-primary-600">Peak</span>
                                    @endif
                                    <div class="w-full {{ $isPeak ? 'bg-primary-600' : 'bg-primary-300 group-hover:bg-primary-400' }} rounded-t-md transition-all"
                                         style="height: {{ $height }}%"></div>
                                </div>
                            @endfor
                        </div>
                        <div class="flex gap-2 mt-2 px-1">
                            @for ($hour = 0; $hour < 24; $hour++)
                                <span class="flex-1 text-center text-[9px] {{ $peakHour === $hour ? 'text-primary-700 font-extrabold' : 'text-slate-400 font-semibold' }}">{{ sprintf('%02d', $hour) }}</span>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 mt-4 pt-4 border-t border-slate-200 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded bg-primary-300"></div>
                        <span class="text-slate-600">Omzet per jam</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded bg-primary-600"></div>
                        <span class="text-slate-600">Peak: {{ $peakHourLabel }}</span>
                    </div>
                    <span class="font-bold text-slate-800">Total Rp {{ number_format($totalPendapatanPeriode, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-slate-900">Metode Pembayaran</h3>
                    <span class="text-xs text-slate-500 font-semibold">{{ $periodeLabel }}</span>
                </div>
                <p class="text-xs text-slate-500 mb-6">Distribusi penyelesaian tagihan dari database</p>

                @if($paymentStats->isEmpty())
                    <div class="flex flex-col items-center justify-center text-center py-10">
                        <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-700">Belum ada pembayaran</p>
                        <p class="text-xs text-slate-500 mt-1">Pembayaran pada periode ini akan muncul di sini.</p>
                    </div>
                @else
                    <div class="flex h-2 rounded-full overflow-hidden bg-slate-100 mb-5">
                        @foreach($paymentStats as $index => $payment)
                            @php
                                $persenBar = $totalPembayaran > 0 ? ((float) $payment->total / $totalPembayaran) * 100 : 0;
                                $barColors = ['bg-blue-500', 'bg-emerald-500', 'bg-amber-500', 'bg-purple-500'];
                            @endphp
                            <div class="{{ $barColors[$index % count($barColors)] }}" style="width: {{ $persenBar }}%"></div>
                        @endforeach
                    </div>

                    <div class="space-y-3">
                        @foreach($paymentStats as $index => $payment)
                            @php
                                $metode = strtoupper((string) $payment->metode_pembayaran);
                                $isQris = $metode === 'QRIS';
                                $persen = $totalPembayaran > 0 ? round(((float) $payment->total / $totalPembayaran) * 100, 1) : 0;
                                $iconColors = $isQris ? 'bg-blue-100 text-blue-600' : 'bg-emerald-100 text-emerald-600';
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-lg {{ $iconColors }} flex items-center justify-center shrink-0">
                                        @if($isQris)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v2a2 2 0 002 2h6a2 2 0 002-2v-1" /></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-900">{{ $metode ?: 'LAINNYA' }}</p>
                                        <p class="text-xs text-slate-500 truncate">{{ $payment->jumlah }} transaksi • Rp {{ number_format((float) $payment->total, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-bold text-slate-900">{{ $persen }}%</p>
                                    <p class="text-[10px] font-semibold text-slate-400">dari total</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- PRODUK TERLARIS -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <div>
                        <h3 class="font-bold text-slate-900">Top 5 Produk Terlaris</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Berdasarkan kuantitas terjual • {{ $periodeLabel }}</p>
                    </div>
                </div>
                <a href="{{ route('transaksis.index') }}" class="text-xs text-primary-600 font-semibold hover:text-primary-700">Lihat Transaksi →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase">Produk</th>
                            <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase">Kategori</th>
                            <th class="text-right py-3 px-4 text-xs font-bold text-slate-500 uppercase">Terjual</th>
                            <th class="text-right py-3 px-4 text-xs font-bold text-slate-500 uppercase">Pendapatan</th>
                            <th class="text-right py-3 px-4 text-xs font-bold text-slate-500 uppercase">Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produkTerlaris as $item)
                            @php
                                $revenue = (float) $item->total_revenue;
                                $share = $totalPendapatanPeriode > 0 ? ($revenue / $totalPendapatanPeriode) * 100 : 0;
                                $barWidth = ($revenue / $maxProdukRevenue) * 100;
                            @endphp
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 text-sm">{{ $item->produk->nama_produk ?? 'Produk dihapus' }}</p>
                                            <p class="text-xs text-slate-500">SKU: {{ $item->produk->sku ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-primary-50 text-primary-700 text-xs font-bold">
                                        {{ $item->produk->kategoriProduk->nama_kategori ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <span class="font-bold text-slate-900">{{ number_format($item->total_qty, 0, ',', '.') }} pcs</span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <span class="font-bold text-slate-900">Rp {{ number_format($revenue, 0, ',', '.') }}</span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center justify-end gap-3">
                                        <div class="w-20 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                            <div class="h-full rounded-full bg-emerald-500" style="width: {{ $barWidth }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-emerald-600 w-12 text-right">{{ round($share, 1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-500 text-sm">
                                    Belum ada data penjualan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
    (function () {
        const clock = document.getElementById('manager-live-clock');
        if (!clock) return;

        const renderClock = () => {
            const time = new Date().toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
                timeZone: 'Asia/Jakarta',
            });
            clock.textContent = time + ' WIB';
        };

        renderClock();
        setInterval(renderClock, 1000);
    })();
</script>
@endsection
