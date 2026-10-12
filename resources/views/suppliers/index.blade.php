@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50">
    
    <!-- TOP HEADER -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">
                        Operasional Outlet / <span class="text-primary-600">Manajemen Supplier</span>
                    </p>
                    <h1 class="text-2xl font-bold text-slate-900">Daftar Mitra & Pemasok</h1>
                    <p class="text-sm text-slate-500 mt-1">Kelola data kontak dan alamat pemasok bahan baku kopi serta operasional.</p>
                </div>
                <a href="{{ route('suppliers.create') }}" class="px-4 py-2.5 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Supplier
                </a>
            </div>

            <!-- Stat Card -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between max-w-xs">
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Total Supplier Aktif</p>
                    <h3 class="text-3xl font-bold text-slate-900">{{ $suppliers->total() }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="p-6 max-w-6xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="supplier-table">
                    <thead>
                        <tr>
                            <th class="w-16 text-center">No</th>
                            <th>Nama Supplier</th>
                            <th>Nomor Telepon</th>
                            <th>Alamat</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $index => $supplier)
                            <tr>
                                <td class="text-center text-slate-500 font-medium">{{ $suppliers->firstItem() + $index }}</td>
                                <td>
                                    <div class="font-bold text-slate-900">{{ $supplier->nama_supplier }}</div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2 text-slate-600">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ $supplier->no_telp }}
                                    </div>
                                </td>
                                <td>
                                    <div class="max-w-xs truncate text-slate-600" title="{{ $supplier->alamat }}">
                                        {{ $supplier->alamat }}
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="p-2 text-slate-600 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form method="post" action="{{ route('suppliers.destroy', $supplier) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-600 hover:bg-red-50 hover:text-red-600 rounded-lg transition" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-16">
                                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                    </div>
                                    <p class="font-bold text-slate-900 mb-1">Belum ada data supplier</p>
                                    <p class="text-sm text-slate-500">Mulai tambahkan supplier pertama Anda</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($suppliers->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                    <p class="text-sm text-slate-600">
                        Menampilkan <span class="font-bold">{{ $suppliers->firstItem() }}</span> - <span class="font-bold">{{ $suppliers->lastItem() }}</span> dari <span class="font-bold">{{ $suppliers->total() }}</span> data
                    </p>
                    {{ $suppliers->links() }}
                </div>
            @endif
        </div>
    </main>
</div>
@endsection