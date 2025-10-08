@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <div class="section-header">
        <h1>Selamat Datang, {{ Auth::user()->namaLengkap }}!</h1>
    </div>

    <div class="dashboard-cards">
        <div class="card">
            <div class="card-icon bg-primary">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="card-content">
                <h3>Peminjaman Aktif</h3>
                <p class="card-value">{{ $stats['totalAktif'] }}</p>
            </div>
        </div>
        <div class="card">
            <div class="card-icon bg-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="card-content">
                <h3>Menunggu Persetujuan</h3>
                <p class="card-value">{{ $stats['totalPending'] }}</p>
            </div>
        </div>
        <div class="card">
            <div class="card-icon bg-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-content">
                <h3>Telah Disetujui</h3>
                <p class="card-value">{{ $stats['totalDisetujui'] }}</p>
            </div>
        </div>
        <div class="card">
            <div class="card-icon bg-info">
                <i class="fas fa-history"></i>
            </div>
            <div class="card-content">
                <h3>Riwayat Peminjaman</h3>
                <p class="card-value">{{ $stats['totalRiwayat'] }}</p>
            </div>
        </div>
    </div>

    <div class="interactive-table mt-4">
        <div class="section-header"><h2>Status Peminjaman Terkini</h2></div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ruangan/Unit</th>
                    <th>Keperluan</th>
                    <th>Tanggal Pinjam</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($peminjamanTerkini as $p)
                    <tr>
                        <td>#{{ $p->id }}</td>
                        <td>
                            @if ($p->ruangan)
                                <strong>{{ $p->ruangan->namaRuangan }}</strong>
                            @elseif ($p->unit)
                                <strong>{{ $p->unit->namaUnit }}</strong>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $p->keperluan }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggalPinjam)->isoFormat('D MMMM YYYY') }}</td>
                        <td>
                            <span class="status-badge status-{{ $p->status }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">Tidak ada data peminjaman terkini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection