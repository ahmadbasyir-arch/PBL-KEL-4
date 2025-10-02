@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="section-header">
        <h1>Dashboard Admin</h1>
        <p>Selamat Datang, {{ Auth::user()->namaLengkap }}!</p>
    </div>
    <p>Halaman ini sedang dalam pengembangan.</p>
@endsection