@extends('layouts.app')

@section('breadcrumb', 'Operasional Outlet / <span class="text-primary-600">Layanan Mandiri</span>')
@section('page-title', 'Pengaturan Meja & QR Code Self-Order')

@section('content')
<div class="min-h-screen bg-slate-50">
    <!-- Header Section -->
    <div class="bg-white border-b border-slate-200 px-4 sm:px-6 py-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex-1">
                <p class="text-sm text-slate-600 leading-relaxed max-w-3xl">
                    Kelola nomor meja, area penempatan, status aktif pemesanan, dan cetak QR code pelanggan secara terintegrasi dengan POS kasir.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3 shrink-0">
                <button class="btn-navbar-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Unduh Semua QR (ZIP/PDF)
                </button>
                @can('access-admin-panel')
                    <a href="{{ route('mejas.create') }}" class="btn-navbar-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Meja Baru
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $totalMeja = $totalMeja ?? $mejas->total();
                $mejaAktifCount = $mejaAktif ?? $mejas->getCollection()->where('status_aktif', true)->count();
                $mejaNonaktifCount = $mejaNonaktif ?? $mejas->getCollection()->where('status_aktif', false)->count();
                $lantai1Count = $lantai1Count ?? $mejas->getCollection()->where('nomor_meja', '<=', 10)->count();
                $lantai2Count = $lantai2Count ?? $mejas->getCollection()->where('nomor_meja', '>', 10)->count();
                $persentaseAktif = $totalMeja > 0 ? round(($mejaAktifCount / $totalMeja) * 100) : 0;
            @endphp

            <!-- Total Meja -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Total Meja Terdaftar</p>
                        <h3 class="text-3xl font-bold text-slate-900">{{ $totalMeja }}</h3>
                        <p class="text-xs text-primary-600 mt-1 font-semibold">{{ $persentaseAktif }}% Terpasang</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Kapasitas -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-1">Total Kapasitas</p>
                        <h3 class="text-3xl font-bold text-slate-900">{{ $totalKapasitas ?? $mejas->sum('kapasitas') }}</h3>
                        <p class="text-xs text-slate-500 mt-1">Orang</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Siap Pakai -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-emerald-600 font-bold uppercase tracking-wider mb-1">Siap Pakai</p>
                        <h3 class="text-3xl font-bold text-slate-900">{{ $mejaAktifCount }}</h3>
                        <p class="text-xs text-emerald-600 mt-1 font-semibold">Standby</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Nonaktif -->
            <div class="bg-white rounded-2xl p-5 border border-red-200 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-red-600 font-bold uppercase tracking-wider mb-1">Nonaktif / Servis</p>
                        <h3 class="text-3xl font-bold text-red-600">{{ $mejaNonaktifCount }}</h3>
                        <p class="text-xs text-red-600 mt-1 font-semibold">Reserved</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                <!-- Area Filters -->
                @php $activeArea = request('area', 'all'); @endphp
                <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar flex-1">
                    <a href="{{ route('mejas.index') }}" class="filter-pill {{ $activeArea === 'all' ? 'active' : 'inactive' }} whitespace-nowrap">
                        Semua Area <span class="ml-1 bg-black/10 px-1.5 py-0.5 rounded-full text-[10px]">{{ $totalMeja }}</span>
                    </a>
                    <a href="{{ route('mejas.index', ['area' => 'lantai1']) }}" class="filter-pill {{ $activeArea === 'lantai1' ? 'active' : 'inactive' }} whitespace-nowrap">
                        Lantai 1 Indoor <span class="ml-1 bg-black/10 px-1.5 py-0.5 rounded-full text-[10px]">{{ $lantai1Count }}</span>
                    </a>
                    <a href="{{ route('mejas.index', ['area' => 'lantai2']) }}" class="filter-pill {{ $activeArea === 'lantai2' ? 'active' : 'inactive' }} whitespace-nowrap">
                        Lantai 2 Slow Bar <span class="ml-1 bg-black/10 px-1.5 py-0.5 rounded-full text-[10px]">{{ $lantai2Count }}</span>
                    </a>
                </div>
                
                <!-- Right Side Controls -->
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <select class="flex-1 sm:flex-none px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-600">
                        <option>Semua Status Order</option>
                        <option>Sedang Digunakan</option>
                        <option>Standby</option>
                        <option>Reserved</option>
                    </select>
                    <button type="button" class="px-3 py-2 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-200 transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Rotasi Token
                    </button>
                </div>
            </div>
        </div>

        <!-- Meja Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse ($mejas as $meja)
                @php
                    $nomorFormatted = str_pad($meja->nomor_meja, 2, '0', STR_PAD_LEFT);
                    $lantai = $meja->nomor_meja <= 10 ? 'Lt. 1' : 'Lt. 2 Slow Bar';
                    $token = 'TK-' . $nomorFormatted . '-' . rand(1000, 9999);
                    $qrUrl = route('welcome', ['meja_id' => $meja->id]);
                @endphp

                <div class="bg-white rounded-2xl border {{ $meja->status_aktif ? 'border-slate-200' : 'border-red-200' }} p-5 transition-all hover:shadow-md">
                    <!-- Card Header -->
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-base font-bold text-slate-900">Meja #{{ $nomorFormatted }}</span>
                            <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">{{ $lantai }}</span>
                        </div>
                        
                        <!-- Toggle Switch (Admin) -->
                        @can('access-admin-panel')
                            <form method="post" action="{{ route('mejas.update', $meja) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="nomor_meja" value="{{ $meja->nomor_meja }}">
                                <input type="hidden" name="kapasitas" value="{{ $meja->kapasitas }}">
                                <input type="hidden" name="status_aktif" value="{{ $meja->status_aktif ? 0 : 1 }}">
                                <button type="submit" class="toggle-switch {{ $meja->status_aktif ? 'enabled' : 'disabled' }}" title="{{ $meja->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}"></button>
                                <span class="text-xs font-bold {{ $meja->status_aktif ? 'text-primary-600' : 'text-red-600' }}">
                                    {{ $meja->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </form>
                        @endcan
                    </div>

                    <!-- Capacity Info -->
                    <div class="flex items-center gap-2 text-xs text-slate-600 mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Kapasitas: <strong class="text-slate-900">{{ $meja->kapasitas }} Orang</strong></span>
                        <span class="text-slate-400">•</span>
                        <span>Non-Smoking</span>
                    </div>

                    <!-- Status Badge -->
                    <div class="flex items-center justify-between mb-4">
                        @if($meja->status_aktif)
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-xs font-bold text-emerald-700">Meja Kosong (Siap Pakai)</span>
                            </div>
                            <span class="text-xs text-slate-500">Standby</span>
                        @else
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                <span class="text-xs font-bold text-red-700">Pemesanan Dinonaktifkan</span>
                            </div>
                            <span class="text-xs font-semibold text-red-600 bg-red-100 px-2 py-0.5 rounded-md">Kunci QR</span>
                        @endif
                    </div>

                    <!-- QR Code -->
                    <div class="bg-slate-50 rounded-xl p-5 flex flex-col items-center justify-center border border-slate-200 mb-3 {{ !$meja->status_aktif ? 'opacity-60' : '' }}">
                        <div class="bg-white p-3 rounded-lg shadow-sm border border-slate-200">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ $qrUrl }}" 
                                 alt="QR Code Meja {{ $meja->nomor_meja }}" 
                                 class="w-32 h-32 object-contain">
                        </div>
                        <p class="text-[10px] text-slate-500 font-mono mt-3 tracking-wide">TOKEN: #{{ $token }}</p>
                    </div>

                    <!-- URL Link -->
                    <div class="flex items-center gap-2 bg-slate-50 rounded-lg px-3 py-2 mb-3 border border-slate-200">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        <span class="text-xs text-slate-600 font-mono truncate flex-1">{{ $qrUrl }}</span>
                        <button type="button" data-copy-url="{{ $qrUrl }}" onclick="copyMejaUrl(this)" class="text-slate-400 hover:text-primary-600 transition" title="Salin URL">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" class="flex-1 px-3 py-2 bg-primary-50 text-primary-700 rounded-lg text-xs font-bold hover:bg-primary-100 transition flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Unduh QR
                        </button>
                        @can('access-admin-panel')
                            <a href="{{ route('mejas.edit', $meja) }}" class="px-3 py-2 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold hover:bg-slate-200 transition flex items-center justify-center" title="Edit Meja">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </a>
                            <form method="post" action="{{ route('mejas.destroy', $meja) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus meja ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg text-xs font-bold hover:bg-red-100 transition" title="Hapus Meja">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-dashed border-slate-300">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    @if (request('area'))
                        <p class="font-bold text-slate-900 mb-1">Tidak ada meja di area ini</p>
                        <p class="text-sm text-slate-500 mb-4">Coba pilih area lain atau tampilkan semua meja.</p>
                        <a href="{{ route('mejas.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-xl font-semibold text-sm hover:bg-slate-200 transition">
                            Tampilkan Semua Meja
                        </a>
                    @else
                        <p class="font-bold text-slate-900 mb-1">Belum ada meja terdaftar</p>
                        <p class="text-sm text-slate-500 mb-4">Mulai tambahkan meja pertama Anda untuk sistem self-order</p>
                        @can('access-admin-panel')
                            <a href="{{ route('mejas.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition shadow-md shadow-primary-600/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Meja Pertama
                            </a>
                        @endcan
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if (isset($mejas) && $mejas->hasPages())
            <div class="flex items-center justify-center mt-6 overflow-x-auto">
                {{ $mejas->links() }}
            </div>
        @endif

        <!-- Info Box: Standar Cetak -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 flex flex-col lg:flex-row lg:items-center gap-5 mt-6">
            <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-slate-900 text-base mb-1">Standar Cetak Acrylic Stand Karsa Roastery</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Ukuran standar akrilik meja: A6 (105 × 148 mm) dengan resolusi 300 DPI. 
                    Disarankan menggunakan laminasi doff tahan gores dan anti-glare agar kamera smartphone mudah memindai dalam segala kondisi pencahayaan kafe.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-semibold text-sm hover:bg-slate-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Pratinjau Mockup Akrilik
                </button>
                <button type="button" class="btn-navbar-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Unduh Kit Cetak (PDF Vektor)
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function copyMejaUrl(button) {
        const url = button?.dataset?.copyUrl || '';
        if (!url) return;

        const showCopied = () => {
            const originalTitle = button.getAttribute('title') || 'Salin URL';
            button.setAttribute('title', 'URL disalin');
            button.classList.add('text-emerald-600');
            window.setTimeout(() => {
                button.setAttribute('title', originalTitle);
                button.classList.remove('text-emerald-600');
            }, 1600);
        };

        const fallbackCopy = () => {
            const textarea = document.createElement('textarea');
            textarea.value = url;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                showCopied();
            } finally {
                document.body.removeChild(textarea);
            }
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(showCopied).catch(fallbackCopy);
        } else {
            fallbackCopy();
        }
    }
</script>
@endsection