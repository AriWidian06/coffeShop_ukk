@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50">
    
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">
                        Katalog Utama / <span class="text-primary-600">Tambah Produk Baru</span>
                    </p>
                    <h1 class="text-2xl font-bold text-slate-900">Tambah Produk Baru</h1>
                    <p class="text-sm text-slate-500 mt-1">Isi detail produk untuk ditambahkan ke katalog</p>
                </div>
                <a href="{{ route('produks.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-semibold text-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </header>

    <!-- Form Content -->
    <main class="p-6 max-w-4xl mx-auto">
        <form method="post" action="{{ route('produks.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
            @csrf
            @include('produks.form')
            
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200">
                <a href="{{ route('produks.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-semibold text-sm hover:bg-slate-50 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Produk
                </button>
            </div>
        </form>
    </main>
</div>
@endsection