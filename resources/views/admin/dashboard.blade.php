@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Dashboard Admin</h2>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center shadow">
                <div class="card-body">
                    <h5>Total Peminjaman</h5>
                    <h3>{{ $jumlahPeminjaman }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow">
                <div class="card-body">
                    <h5>Menunggu</h5>
                    <h3>{{ $menunggu }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow">
                <div class="card-body">
                    <h5>Disetujui</h5>
                    <h3>{{ $disetujui }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow">
                <div class="card-body">
                    <h5>Ditolak</h5>
                    <h3>{{ $ditolak }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection