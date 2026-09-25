@extends('layouts.app')
@section('content')
<h1>Masuk ke Perkoci Eatery</h1>
<form class="form" method="post" action="{{ route('login.store') }}">@csrf
    <label>Username<input name="username" value="{{ old('username') }}" required autofocus></label>
    <label>Password<input type="password" name="password" required></label>
    <p><button type="submit">Masuk</button></p>
</form>
@endsection
