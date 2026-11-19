@php
    $activeLayout = match (auth('karyawan')->user()->role ?? 'kasir') {
        'kasir' => 'layouts.kasir',
        'manager' => 'layouts.manager',
        default => 'layouts.app',
    };
@endphp
@extends($activeLayout)

@section('content')
<div class="min-h-screen bg-slate-50 p-4 sm:p-6">
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Kas / <span class="text-primary-600">Mutasi</span></p>
            <h1 class="text-2xl font-bold text-slate-900">Mutasi Kas</h1>
            <p class="text-sm text-slate-500 mt-1">Semua pergerakan saldo tercatat otomatis dan tidak dapat dihapus.</p>
        </div>
        <a href="{{ route('kas.index') }}" class="self-start px-4 py-2.5 border border-slate-200 bg-white rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Kembali</a>
    </header>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Masuk (filter)</p>
            <p class="text-xl font-bold text-emerald-600 mt-1">Rp{{ number_format($totals['debit'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Keluar (filter)</p>
            <p class="text-xl font-bold text-red-600 mt-1">Rp{{ number_format($totals['kredit'], 0, ',', '.') }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('kas.mutasi') }}" class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm mb-5 grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="col-span-2 md:col-span-1">
            <label class="text-xs font-bold text-slate-600">Akun Kas</label>
            <select name="kas_account_id" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 text-sm">
                <option value="">Semua</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}" {{ request('kas_account_id') == $account->id ? 'selected' : '' }}>{{ $account->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-bold text-slate-600">Jenis</label>
            <select name="jenis" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 text-sm">
                <option value="">Semua</option>
                @foreach (['saldo_awal' => 'Saldo Awal', 'penjualan' => 'Penjualan', 'pengeluaran' => 'Pengeluaran', 'setoran' => 'Setoran / Transfer', 'tarik_tunai' => 'Tarik Tunai', 'penyesuaian' => 'Penyesuaian'] as $value => $label)
                    <option value="{{ $value }}" {{ request('jenis') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-bold text-slate-600">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="text-xs font-bold text-slate-600">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div class="col-span-2 md:col-span-1 flex items-end">
            <button class="w-full mt-1 px-3 py-2 bg-slate-800 text-white rounded-xl text-sm font-bold">Terapkan</button>
        </div>
    </form>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-4 py-3">Tanggal</th>
                        <th class="text-left px-4 py-3">Keterangan</th>
                        <th class="text-left px-4 py-3">Akun</th>
                        <th class="text-left px-4 py-3">Jenis</th>
                        <th class="text-right px-4 py-3">Masuk</th>
                        <th class="text-right px-4 py-3">Keluar</th>
                        <th class="text-right px-4 py-3">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($movements as $movement)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-600">{{ $movement->tanggal->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-800">{{ $movement->keterangan }}</p>
                                <p class="text-xs text-slate-400">oleh {{ $movement->karyawan?->nama_karyawan ?? 'Sistem' }}</p>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $movement->account?->nama }}</td>
                            <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">{{ $movement->jenis_label }}</span></td>
                            <td class="px-4 py-3 text-right font-bold text-emerald-600 whitespace-nowrap">{{ $movement->debit > 0 ? 'Rp'.number_format($movement->debit, 0, ',', '.') : '-' }}</td>
                            <td class="px-4 py-3 text-right font-bold text-red-600 whitespace-nowrap">{{ $movement->kredit > 0 ? 'Rp'.number_format($movement->kredit, 0, ',', '.') : '-' }}</td>
                            <td class="px-4 py-3 text-right font-bold whitespace-nowrap">Rp{{ number_format($movement->saldo_sesudah, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-slate-500">Tidak ada mutasi sesuai filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100">{{ $movements->links() }}</div>
    </div>
</div>
@endsection
