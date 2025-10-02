@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('content')
    <div class="section-header">
        <h1>Dashboard Dosen</h1>
        <p>Selamat Datang, {{ Auth::user()->namaLengkap }}!</p>
    </div>
    <p>Halaman ini sedang dalam pengembangan.</p>
@endsection