@php
    $activeLayout = match (auth('karyawan')->user()->role ?? 'kasir') {
        'kasir' => 'layouts.kasir',
        'manager' => 'layouts.manager',
        default => 'layouts.app',
    };
    $kategoriLabels = ['bahan_baku' => 'Bahan Baku', 'operasional' => 'Operasional', 'gaji' => 'Gaji Karyawan', 'lainnya' => 'Lainnya'];
    $jenisLabels = ['saldo_awal' => 'Saldo Awal', 'penjualan' => 'Penjualan', 'pengeluaran' => 'Pengeluaran', 'setoran' => 'Setoran / Transfer', 'tarik_tunai' => 'Tarik Tunai', 'penyesuaian' => 'Penyesuaian'];
@endphp
@extends($activeLayout)

@section('content')
<div class="min-h-screen bg-slate-50 p-4 sm:p-6">
    <header class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Keuangan / <span class="text-primary-600">Laporan Kas</span></p>
            <h1 class="text-2xl font-bold text-slate-900">Arus Kas &amp; Laba Rugi</h1>
            <p class="text-sm text-slate-500 mt-1">Ringkasan pendapatan, biaya, dan pergerakan saldo untuk periode tertentu.</p>
        </div>
        <a href="{{ route('kas.index') }}" class="self-start px-4 py-2.5 border border-slate-200 bg-white rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Kembali</a>
    </header>

    <form method="GET" action="{{ route('kas.laporan') }}" class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm mb-5 grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
        <div>
            <label class="text-xs font-bold text-slate-600">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ $start->toDateString() }}" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="text-xs font-bold text-slate-600">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $end->toDateString() }}" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <button class="px-4 py-2 bg-slate-800 text-white rounded-xl text-sm font-bold">Tampilkan</button>
    </form>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pendapatan</p>
            <p class="text-lg font-bold text-emerald-600 mt-1">Rp{{ number_format($pendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">HPP</p>
            <p class="text-lg font-bold text-red-600 mt-1">Rp{{ number_format($hpp, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Laba Kotor</p>
            <p class="text-lg font-bold text-slate-900 mt-1">Rp{{ number_format($labaKotor, 0, ',', '.') }}</p>
        </div>
        <div class="bg-slate-900 rounded-2xl p-4 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Laba Bersih</p>
            <p class="text-lg font-bold text-white mt-1">Rp{{ number_format($labaBersih, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100">
                <h2 class="font-bold text-slate-900">Laporan Laba Rugi</h2>
                <p class="text-xs text-slate-400">{{ $start->translatedFormat('d M Y') }} – {{ $end->translatedFormat('d M Y') }}</p>
            </div>
            <div class="p-4 space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-slate-600">Pendapatan penjualan</span><span class="font-bold text-slate-900">Rp{{ number_format($pendapatan, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-slate-600">Harga pokok penjualan (HPP)</span><span class="font-bold text-red-600">-Rp{{ number_format($hpp, 0, ',', '.') }}</span></div>
                <div class="flex justify-between border-t border-slate-100 pt-3"><span class="font-bold text-slate-800">Laba Kotor</span><span class="font-bold text-slate-900">Rp{{ number_format($labaKotor, 0, ',', '.') }}</span></div>

                <div class="border-t border-slate-100 pt-3 space-y-2">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengeluaran Disetujui</p>
                    @forelse ($kategoriLabels as $key => $label)
                        <div class="flex justify-between">
                            <span class="text-slate-600">{{ $label }}</span>
                            <span class="font-semibold text-slate-800">Rp{{ number_format($pengeluaranPerKategori->get($key, 0), 0, ',', '.') }}</span>
                        </div>
                    @empty
                    @endforelse
                    <div class="flex justify-between"><span class="text-slate-600">Total Pengeluaran</span><span class="font-bold text-red-600">-Rp{{ number_format($totalPengeluaran, 0, ',', '.') }}</span></div>
                </div>

                <div class="flex justify-between bg-slate-900 text-white rounded-xl px-3 py-3">
                    <span class="font-bold">Laba Bersih</span>
                    <span class="font-bold">Rp{{ number_format($labaBersih, 0, ',', '.') }}</span>
                </div>
                <p class="text-xs text-slate-400">HPP dihitung dari harga beli produk pada detail transaksi. Pengeluaran yang masih pending tidak memengaruhi laba.</p>
            </div>
        </section>

        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100">
                <h2 class="font-bold text-slate-900">Arus Kas per Akun</h2>
                <p class="text-xs text-slate-400">Saldo akhir mengikuti mutasi hingga akhir periode.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                        <tr>
                            <th class="text-left px-4 py-3">Akun</th>
                            <th class="text-right px-4 py-3">Awal</th>
                            <th class="text-right px-4 py-3">Masuk</th>
                            <th class="text-right px-4 py-3">Keluar</th>
                            <th class="text-right px-4 py-3">Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($accounts as $account)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $account->nama }}</td>
                                <td class="px-4 py-3 text-right text-slate-600">Rp{{ number_format($account->saldo_awal, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-emerald-600">+Rp{{ number_format($account->masuk, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-red-600">-Rp{{ number_format($account->keluar, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold">Rp{{ number_format($account->saldo_akhir, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-slate-100">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Ringkasan Mutasi</p>
                <div class="space-y-1">
                    @forelse ($mutasiPerJenis as $mutasi)
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-600">{{ $jenisLabels[$mutasi->jenis] ?? $mutasi->jenis }} ({{ $mutasi->jumlah }} transaksi)</span>
                            <span class="font-bold text-slate-800">+{{ number_format($mutasi->masuk, 0, ',', '.') }} / -{{ number_format($mutasi->keluar, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">Belum ada mutasi pada periode ini.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </div>

    <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mt-4">
        <div class="px-4 py-3 border-b border-slate-100">
            <h2 class="font-bold text-slate-900">Rincian Pengeluaran Disetujui</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                    <tr>
                        <th class="text-left px-4 py-3">Tanggal</th>
                        <th class="text-left px-4 py-3">Deskripsi</th>
                        <th class="text-left px-4 py-3">Kategori</th>
                        <th class="text-left px-4 py-3">Akun</th>
                        <th class="text-right px-4 py-3">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pengeluarans as $item)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-600">{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $item->deskripsi }}</td>
                            <td class="px-4 py-3">{{ $kategoriLabels[$item->kategori] ?? $item->kategori }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $item->account?->nama }}</td>
                            <td class="px-4 py-3 text-right font-bold">Rp{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada pengeluaran disetujui pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
