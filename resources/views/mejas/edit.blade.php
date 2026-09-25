@extends('layouts.app')
@section('content')<h1>Edit Meja</h1><form class="form" method="post" action="{{ route('mejas.update', $meja) }}">@csrf @method('PUT') @include('mejas.form')<button>Simpan</button></form>@endsection
