@php
    $mejaId = $meja_id ?? session('meja_id') ?? request()->cookie('qr_meja_id');
    $mejaNomor = $meja_nomor ?? session('meja_nomor') ?? request()->cookie('qr_meja_nomor');
    $homeUrl = $mejaId ? route('welcome', ['meja_id' => $mejaId]) : route('welcome');
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak | Bagi Kopi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-50 font-sans min-h-screen flex items-center justify-center p-4 text-slate-800">
    <div class="max-w-md w-full bg-white p-6 rounded-2xl border border-slate-200 shadow-xl text-center space-y-4">
        <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-2xl font-bold">
            🔒
        </div>
        <div>
            <h1 class="text-xl font-black text-slate-900">Akses Ditolak</h1>
            <p class="text-xs text-slate-500 mt-1">
                Halaman dashboard hanya untuk admin, kasir, dan manager Bagi Kopi.
                Pelanggan yang memesan dari QR meja tidak memiliki akses ke halaman ini.
            </p>
        </div>

        @if($mejaId)
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary-50 border border-primary-200 text-xs font-bold text-primary-700">
                <span class="w-1.5 h-1.5 rounded-full bg-primary-600"></span>
                Meja #{{ $mejaNomor ?? $mejaId }} &mdash; pesanan dari QR
            </div>
        @endif

        <div class="pt-2">
            <a href="{{ $homeUrl }}" class="inline-block w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 rounded-xl text-xs transition-all shadow">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
