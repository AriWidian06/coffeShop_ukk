@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50">
    
    <!-- TOP HEADER -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">
                        Katalog Utama / <span class="text-primary-600">Kategori Produk</span>
                    </p>
                    <h1 class="text-2xl font-bold text-slate-900">Manajemen Kategori</h1>
                    <p class="text-sm text-slate-500 mt-1">Kelola pengelompokan menu dan bahan baku untuk memudahkan navigasi.</p>
                </div>
                <a href="{{ route('kategori-produks.create') }}" class="px-4 py-2.5 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Kategori
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="p-6 max-w-6xl mx-auto space-y-6">
        
        <!-- Stat Card -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Total Kategori Aktif</p>
                <h3 class="text-3xl font-bold text-slate-900">{{ $kategoriProduks->total() }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="category-table">
                    <thead>
                        <tr>
                            <th class="w-16 text-center">No</th>
                            <th>Nama Kategori</th>
                            <th>Jumlah Produk</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kategoriProduks as $index => $kategori)
                            <tr>
                                <td class="text-center text-slate-500 font-medium">{{ $kategoriProduks->firstItem() + $index }}</td>
                                <td>
                                    <div class="font-bold text-slate-900">{{ $kategori->nama_kategori }}</div>
                                </td>
                                <td>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                                        {{ $kategori->produks_count ?? 0 }} Item
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('kategori-produks.edit', $kategori) }}" class="p-2 text-slate-600 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form method="post" action="{{ route('kategori-produks.destroy', $kategori) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Produk di dalamnya tidak akan terhapus, hanya kategori pengelompokannya.')">
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
                                <td colspan="4" class="text-center py-16">
                                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </div>
                                    <p class="font-bold text-slate-900 mb-1">Belum ada kategori</p>
                                    <p class="text-sm text-slate-500">Mulai tambahkan kategori pertama Anda</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($kategoriProduks->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                    <p class="text-sm text-slate-600">
                        Menampilkan <span class="font-bold">{{ $kategoriProduks->firstItem() }}</span> - <span class="font-bold">{{ $kategoriProduks->lastItem() }}</span> dari <span class="font-bold">{{ $kategoriProduks->total() }}</span> data
                    </p>
                    {{ $kategoriProduks->links() }}
                </div>
            @endif
        </div>
    </main>
</div>
@endsection