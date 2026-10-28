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

        <!-- Total Harga -->
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 mb-6 shadow-inner">
            <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Total Tagihan Pembayaran</p>
            <div class="text-3xl font-black text-primary-600 mt-1">
                Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
            </div>
        </div>

        <!-- Midtrans Payment Button -->
        <div class="flex justify-center mb-8">
            <button id="pay-button" 
                    class="w-full px-6 py-4 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-2xl transition-all text-lg shadow-lg shadow-primary-600/30 flex items-center justify-center gap-2 active:scale-95">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .845-3 2s1.343 2 3 2 3 .845 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Bayar Sekarang
            </button>
        </div>

        <!-- Panduan Pembayaran -->
        <div class="space-y-2 mb-8 text-left text-xs text-slate-600 bg-blue-50 border border-blue-100 p-4 rounded-xl">
            <p class="font-bold text-blue-800 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Cara Bayar:
            </p>
            <ul class="list-disc list-inside space-y-1 text-blue-900">
                <li>Klik tombol <span class="font-bold">Bayar Sekarang</span>.</li>
                <li>Pilih metode pembayaran (QRIS, E-Wallet, Bank Transfer).</li>
                <li>Lakukan pembayaran sesuai nominal tagihan.</li>
                <li>Sistem akan otomatis memperbarui status menjadi Lunas.</li>
            </ul>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex gap-3">
            <a href="{{ route('transaksis.index') }}" 
               class="flex-1 px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition text-sm text-center flex items-center justify-center">
                Kembali
            </a>
            <button type="button" id="check-button"
                    class="flex-1 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition text-sm text-center flex items-center justify-center">
                Cek Status
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    const orderId = '{{ $orderId }}';
    const checkUrl = '{{ route('kasir.payment.qris.check', $transaksi->id) }}';
    const csrfToken = '{{ csrf_token() }}';

    function checkStatus(redirectOnPaid, attempt = 1) {
        fetch(checkUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ order_id: orderId }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.paid) {
                alert('Pembayaran berhasil dan sudah lunas!');
                window.location.href = "{{ route('transaksis.index') }}";
                return;
            }

            if (redirectOnPaid && attempt < 3) {
                setTimeout(() => checkStatus(redirectOnPaid, attempt + 1), 2000);
                return;
            }

            if (redirectOnPaid) {
                alert('Pembayaran belum terkonfirmasi (status: ' + data.status + '). Silakan cek beberapa saat lagi.');
            }
        })
        .catch(() => {
            if (redirectOnPaid) {
                alert('Gagal mengecek status pembayaran.');
            }
        });
    }

    document.getElementById('pay-button').onclick = function() {
        window.snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                checkStatus(true);
            },
            onPending: function(result) {
                alert('Menunggu Pembayaran...');
                checkStatus(false);
            },
            onError: function(result) {
                alert('Pembayaran Gagal!');
            }
        });
    };

    document.getElementById('check-button').onclick = function() {
        checkStatus(true);
    };
</script>
@endsection
