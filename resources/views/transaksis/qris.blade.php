@extends('layouts.kasir')

@section('title', 'Pembayaran QRIS')

@section('content')
<div class="min-h-screen bg-slate-50 py-10 px-4 flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden p-8 text-center">
        
        <!-- Header -->
        <div class="mb-6">
            <span class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-full uppercase tracking-wider">
                Scan QRIS Otomatis
            </span>
            <h2 class="text-2xl font-black text-slate-900 mt-2">Struk Transaksi #{{ $transaksi->id }}</h2>
            <p class="text-xs text-slate-500 mt-1">
                Meja: {{ $transaksi->meja->nomor_meja ?? 'Takeaway' }} • Kasir: {{ auth('karyawan')->user()->nama_karyawan ?? 'Kasir' }}
            </p>
        </div>

        <!-- TOTAL HARGA OTOMATIS -->
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 mb-6 shadow-inner">
            <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Total Tagihan Pembayaran</p>
            <div class="text-3xl font-black text-primary-600 mt-1">
                Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
            </div>
        </div>

        <!-- QR Code Generator -->
        <div class="flex justify-center mb-6">
            <div class="p-4 bg-white border-2 border-slate-100 rounded-2xl shadow-sm inline-block">
                 <img src="{{ asset('images/qris.jpg') }}" 
                      alt="QRIS Code" 
                      class="w-52 h-52 mx-auto rounded-lg">
            </div>
        </div>

        <!-- Panduan Kasir -->
        <div class="space-y-2 mb-8 text-left text-xs text-slate-600 bg-amber-50 border border-amber-200 p-4 rounded-xl">
            <p class="font-bold text-amber-800 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Panduan Kasir:
            </p>
            <ul class="list-disc list-inside space-y-1 text-amber-900">
                <li>Minta pelanggan scan QRIS di atas menggunakan aplikasi e-wallet / mobile banking.</li>
                <li>Pastikan nominal yang terproses di HP pelanggan sama persis dengan tagihan.</li>
                <li>Klik tombol <span class="font-bold">Konfirmasi Lunas</span> di bawah setelah pembayaran diterima.</li>
            </ul>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex gap-3">
            <a href="{{ route('transaksis.index') }}" 
               class="flex-1 px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition text-sm text-center flex items-center justify-center">
                Kembali
            </a>
            
            <form action="{{ route('kasir.payment.qris.process', $transaksi->id) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" 
                        class="w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition text-sm shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-1">
                    Konfirmasi Lunas ✓
                </button>
            </form>
        </div>

    </div>
</div>
@endsection