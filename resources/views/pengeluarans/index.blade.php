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
    <header class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Keuangan / <span class="text-primary-600">Pengeluaran</span></p>
            <h1 class="text-2xl font-bold text-slate-900">Pengeluaran</h1>
            <p class="text-sm text-slate-500 mt-1">Pengajuan kasir diverifikasi oleh manager sebelum memotong saldo kas.</p>
        </div>
        @can('input-pengeluaran')
            <a href="{{ route('pengeluarans.create') }}" class="shrink-0 self-start px-4 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-bold hover:bg-primary-700 shadow-sm">+ Ajukan Pengeluaran</a>
        @endcan
    </header>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Menunggu Persetujuan</p>
            <p class="text-xl font-bold text-amber-600 mt-1">Rp{{ number_format($stats['pending'], 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $stats['pending_count'] }} pengajuan</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Disetujui Bulan Ini</p>
            <p class="text-xl font-bold text-emerald-600 mt-1">Rp{{ number_format($stats['approved'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Status Alur</p>
            <p class="text-sm font-bold text-slate-800 mt-2">Kasir mengajukan → Manager menyetujui</p>
            <p class="text-xs text-slate-400 mt-1">Saldo berkurang saat persetujuan.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('pengeluarans.index') }}" class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm mb-5 grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="col-span-2 md:col-span-1">
            <label class="text-xs font-bold text-slate-600">Status</label>
            <select name="status" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 text-sm">
                <option value="">Semua</option>
                @foreach (['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-bold text-slate-600">Kategori</label>
            <select name="kategori" class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 text-sm">
                <option value="">Semua</option>
                @foreach (['bahan_baku' => 'Bahan Baku', 'operasional' => 'Operasional', 'gaji' => 'Gaji', 'lainnya' => 'Lainnya'] as $value => $label)
                    <option value="{{ $value }}" {{ request('kategori') === $value ? 'selected' : '' }}>{{ $label }}</option>
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
        <div class="col-span-2 md:col-span-1 flex items-end gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari deskripsi..." class="mt-1 w-full border border-slate-300 rounded-xl px-3 py-2 text-sm">
            <button class="mt-1 px-3 py-2 bg-slate-800 text-white rounded-xl text-sm font-bold">Cari</button>
        </div>
    </form>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-4 py-3">Tanggal</th>
                        <th class="text-left px-4 py-3">Deskripsi</th>
                        <th class="text-left px-4 py-3">Kategori</th>
                        <th class="text-left px-4 py-3">Kas</th>
                        <th class="text-right px-4 py-3">Jumlah</th>
                        <th class="text-left px-4 py-3">Status</th>
                        <th class="text-right px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pengeluarans as $item)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-600">{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('pengeluarans.show', $item) }}" class="font-semibold text-slate-900 hover:text-primary-600">{{ $item->deskripsi }}</a>
                                <p class="text-xs text-slate-400">oleh {{ $item->karyawan?->nama_karyawan ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $item->kategori_label }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-600">{{ $item->account?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-bold whitespace-nowrap">Rp{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $statusClass = match ($item->status) {
                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-amber-100 text-amber-700',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $statusClass }}">{{ $item->status_label }}</span>
                                @if ($item->approved_by)
                                    <p class="text-xs text-slate-400 mt-1">oleh {{ $item->approver?->nama_karyawan ?? '-' }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap justify-end gap-1.5">
                                    <a href="{{ route('pengeluarans.show', $item) }}" class="px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50">Detail</a>
                                    @can('input-pengeluaran')
                                        @if ($item->status === 'pending')
                                            <a href="{{ route('pengeluarans.edit', $item) }}" class="px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50">Ubah</a>
                                            <form method="POST" action="{{ route('pengeluarans.destroy', $item) }}" onsubmit="return confirm('Batalkan pengajuan ini?')">
                                                @csrf @method('DELETE')
                                                <button class="px-2.5 py-1.5 border border-red-200 rounded-lg text-xs font-bold text-red-600 hover:bg-red-50">Hapus</button>
                                            </form>
                                        @endif
                                    @endcan
                                    @can('approve-pengeluaran')
                                        @if ($item->status === 'pending')
                                            <form method="POST" action="{{ route('pengeluarans.approve', $item) }}">
                                                @csrf
                                                <button class="px-2.5 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700">Setujui</button>
                                            </form>
                                            <a href="{{ route('pengeluarans.show', $item) }}" class="px-2.5 py-1.5 border border-amber-200 rounded-lg text-xs font-bold text-amber-700 hover:bg-amber-50">Tolak</a>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-slate-500">Belum ada pengeluaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100">{{ $pengeluarans->links() }}</div>
    </div>
</div>
@endsection
