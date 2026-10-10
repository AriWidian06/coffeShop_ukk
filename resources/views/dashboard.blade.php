@extends('layouts.app')
@section('content')
    <h1>Dashboard</h1>
    <p>Selamat datang di sistem pemesanan Perkoci Eatery.</p>
    <p><a class="button" href="{{ route('transaksis.create') }}">Buat transaksi</a></p>
@endsection
