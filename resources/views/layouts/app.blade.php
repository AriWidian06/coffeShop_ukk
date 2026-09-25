<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Perkoci Eatery' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; color: #252525; background: #f6f3ee; }
        nav { background: #3b2418; padding: 1rem; } nav a, nav button { color: #fff; margin-right: 1rem; text-decoration: none; background: none; border: 0; cursor: pointer; font-size: 1rem; }
        main { max-width: 1100px; margin: 2rem auto; padding: 0 1rem; } .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
        .button, button { background: #a85d27; color: #fff; border: 0; padding: .6rem .9rem; border-radius: 4px; text-decoration: none; cursor: pointer; } .danger { background: #9b2c2c; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 1rem; } th, td { padding: .7rem; border-bottom: 1px solid #ddd; text-align: left; }
        form.inline { display: inline; } label { display: block; margin-top: .8rem; font-weight: 600; } input, select, textarea { width: 100%; box-sizing: border-box; padding: .6rem; margin-top: .25rem; border: 1px solid #bbb; border-radius: 4px; } textarea { min-height: 90px; }
        .form { max-width: 650px; background: #fff; padding: 1.25rem; } .alert { padding: .75rem; margin: 1rem 0; background: #e4f3e5; } .errors { background: #fbe3e3; padding: .75rem; }
        .pagination { margin-top: 1rem; } .pagination a, .pagination span { margin-right: .5rem; }
    </style>
</head>
<body>
<nav>
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('transaksis.index') }}">Transaksi</a>
    <a href="{{ route('produks.index') }}">Produk</a>
    <a href="{{ route('mejas.index') }}">Meja</a>
    <a href="{{ route('suppliers.index') }}">Supplier</a>
    <a href="{{ route('karyawans.index') }}">Karyawan</a>
    <form class="inline" method="post" action="{{ route('logout') }}">@csrf <button type="submit">Keluar</button></form>
</nav>
<main>
    @if(session('success')) <div class="alert">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="errors"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
    @yield('content')
</main>
</body>
</html>
