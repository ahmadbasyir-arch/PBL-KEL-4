@extends('layouts.app')

@section('title', (Auth::user()->role === 'dosen' ? 'Dashboard Dosen' : 'Dashboard Mahasiswa'))

@section('content')
@if (session('success'))
    <div class="success-message">
        {{ session('success') }}
    </div>
@endif

<div class="section-header">
    <h1>Selamat Datang, {{ ucwords(Auth::user()->name ?? Auth::user()->username ?? 'Pengguna') }}! <small style="font-weight:600; color:#666">({{ ucfirst(Auth::user()->role) }})</small></h1>
</div>

{{-- === Statistik Ringkasan === --}}
<div class="dashboard-cards">
    <div class="card stat-card bg-primary">
        <div class="card-icon">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="card-content">
            <h3>Peminjaman Aktif</h3>
            <p class="card-value">{{ $stats['totalAktif'] }}</p>
        </div>
    </div>

    <div class="card stat-card bg-warning">
        <div class="card-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="card-content">
            <h3>Menunggu Persetujuan</h3>
            <p class="card-value">{{ $stats['totalPending'] }}</p>
        </div>
    </div>

    <div class="card stat-card bg-success">
        <div class="card-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="card-content">
            <h3>Telah Disetujui</h3>
            <p class="card-value">{{ $stats['totalDisetujui'] }}</p>
        </div>
    </div>

    <div class="card stat-card bg-danger">
        <div class="card-icon">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="card-content">
            <h3>Ditolak</h3>
            <p class="card-value">{{ $stats['totalDitolak'] ?? 0 }}</p>
        </div>
    </div>

    <div class="card stat-card bg-info">
        <div class="card-icon">
            <i class="fas fa-history"></i>
        </div>
        <div class="card-content">
            <h3>Riwayat Peminjaman</h3>
            <p class="card-value">{{ $stats['totalRiwayat'] }}</p>
        </div>
    </div>
</div>

{{-- === Tabel Status Peminjaman === --}}
<div class="interactive-table mt-4">
    <div class="section-header">
        <h2>Status Peminjaman Terkini</h2>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ruangan/Unit</th>
                <th>Keperluan</th>
                <th>Tanggal Pinjam</th>
                <th>Status / Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($peminjamanTerkini as $p)
                <tr>
                    <td>#{{ $p->id }}</td>
                    <td>
                        @if (!empty($p->ruangan) && isset($p->ruangan->namaRuangan))
                            <strong>{{ $p->ruangan->namaRuangan }}</strong>
                        @elseif (!empty($p->unit) && isset($p->unit->namaUnit))
                            <strong>{{ $p->unit->namaUnit }}</strong>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $p->keperluan }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggalPinjam)->isoFormat('D MMMM YYYY') }}</td>
                    <td>
                        @php
                            $isDigunakan = in_array($p->status, ['digunakan', 'disetujui']);
                            $isMenyelesaikan = in_array($p->status, ['menyelesaikan', 'menunggu_validasi']);
                        @endphp

                        @if ($p->status == 'pending')
                            <span class="status-badge status-pending">
                                <i class="fas fa-hourglass-half"></i> Menunggu Persetujuan
                            </span>
                        @elseif ($isDigunakan)
                            <form action="{{ route('peminjaman.ajukanSelesai', $p->id) }}" method="POST" style="display:inline;"
                                onsubmit="return confirm('Apakah Anda yakin ingin mengajukan penyelesaian peminjaman ini? Setelah ini akan divalidasi oleh admin.')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i> Ajukan Selesai
                                </button>
                            </form>
                        @elseif ($isMenyelesaikan)
                            <span class="status-badge status-pending">
                                <i class="fas fa-hourglass-half"></i> Menunggu Validasi Admin
                            </span>
                        @elseif ($p->status == 'selesai')
                            <span class="status-badge status-selesai">
                                <i class="fas fa-check-circle"></i> Selesai
                            </span>
                        @elseif ($p->status == 'ditolak')
                            <span class="status-badge status-ditolak">
                                <i class="fas fa-times-circle"></i> Ditolak
                            </span>
                        @else
                            <span class="status-badge status-{{ $p->status }}">
                                {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                            </span>
                        @endif
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

{{-- === Styling Modern === --}}
<style>
    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .stat-card {
        display: flex;
        align-items: center;
        padding: 20px;
        border-radius: 16px;
        color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .stat-card .card-icon {
        background: rgba(255, 255, 255, 0.25);
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 26px;
        margin-right: 15px;
    }

    .stat-card h3 {
        font-size: 1rem;
        margin: 0;
        font-weight: 600;
    }

    .card-value {
        font-size: 1.6rem;
        font-weight: 700;
        margin-top: 4px;
    }

    /* === Kartu Statistik Modern (Warna Putih) === */
    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .stat-card {
        display: flex;
        align-items: center;
        padding: 20px;
        border-radius: 16px;
        background: #ffffff;
        color: #333;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    /* Ikon dengan warna khas masing-masing */
    .stat-card .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 26px;
        margin-right: 15px;
        color: #fff;
    }

    /* Warna ikon */
    .bg-primary .card-icon { background-color: #007bff; }
    .bg-warning .card-icon { background-color: #ffc107; }
    .bg-success .card-icon { background-color: #28a745; }
    .bg-danger .card-icon { background-color: #dc3545; }
    .bg-info .card-icon { background-color: #17a2b8; }

    .stat-card h3 {
        font-size: 1rem;
        margin: 0;
        font-weight: 600;
    }

    .card-value {
        font-size: 1.6rem;
        font-weight: 700;
        margin-top: 4px;
    }


    /* === Badge Status === */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: capitalize;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-disetujui {
        background: #d4edda;
        color: #155724;
    }

    .status-ditolak {
        background: #f8d7da;
        color: #721c24;
    }

    .status-selesai {
        background: #e2e3e5;
        color: #383d41;
    }

    /* === Tombol Aksi === */
    .btn-success {
        background-color: #28a745;
        border: none;
        color: white;
        font-weight: 600;
        border-radius: 8px;
        padding: 5px 10px;
        transition: 0.2s;
    }

    .btn-success:hover {
        background-color: #218838;
        transform: scale(1.05);
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .data-table th {
        background: #f8f9fa;
        padding: 12px;
        text-align: left;
        font-weight: 700;
        border-bottom: 2px solid #e0e0e0;
    }

    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #f1f1f1;
    }

    .data-table tr:hover {
        background: #f9f9f9;
    }
</style>
@endsection