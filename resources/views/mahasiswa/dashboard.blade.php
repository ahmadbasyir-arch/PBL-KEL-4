@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
@if (session('success'))
    <div class="success-message">
        {{ session('success') }}
    </div>
@endif

<div class="section-header">
    <h1>Selamat Datang, {{ ucwords(Auth::user()->name ?? Auth::user()->username ?? 'Mahasiswa') }}! 
        <small style="font-weight:600; color:#666">(Mahasiswa)</small>
    </h1>
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
                        @if (!empty($p->ruangan))
                            <strong>{{ $p->ruangan->namaRuangan }}</strong>
                        @elseif (!empty($p->unit))
                            <strong>{{ $p->unit->namaUnit }}</strong>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $p->keperluan }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggalPinjam)->isoFormat('D MMMM YYYY') }}</td>

                    <td>
                        @php
                            $isDigunakan = in_array($p->status, ['digunakan','disetujui','sedang digunakan']);
                            $isMenyelesaikan = in_array($p->status,['menyelesaikan','menunggu_validasi']);
                            $canEdit = in_array($p->status,['pending','disetujui','digunakan','sedang digunakan']);
                        @endphp

                        {{-- STATUS PENDING --}}
                        @if ($p->status == 'pending')
                            <span class="status-badge status-pending">
                                <i class="fas fa-hourglass-half"></i> Menunggu Persetujuan
                            </span>

                            {{-- TOMBOL EDIT --}}
                            <a href="{{ route('peminjaman.edit', $p->id) }}" 
                               class="btn btn-warning btn-sm" style="margin-left:6px;">
                                <i class="fas fa-edit"></i> Edit
                            </a>

                        {{-- STATUS DISETUJUI / DIGUNAKAN --}}
                        @elseif ($isDigunakan)

                            {{-- TOMBOL AJUKAN SELESAI --}}
                            <form action="{{ route('peminjaman.ajukanSelesai', $p->id) }}"
                                  method="POST" style="display:inline;"
                                  onsubmit="return confirm('Ajukan penyelesaian? Setelah ini akan divalidasi admin.')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i> Ajukan Selesai
                                </button>
                            </form>

                            {{-- TOMBOL KEMBALIKAN --}}
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalKembalikan{{ $p->id }}" style="margin-left:6px;">
                                <i class="fas fa-undo"></i> Kembalikan
                            </button>

                            {{-- TOMBOL EDIT --}}
                            @if ($canEdit)
                                <a href="{{ route('peminjaman.edit', $p->id) }}" 
                                   class="btn btn-warning btn-sm" style="margin-left:6px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif

                        {{-- STATUS MENUNGGU VALIDASI --}}
                        @elseif ($isMenyelesaikan)
                            <span class="status-badge status-pending">
                                <i class="fas fa-hourglass-half"></i> Menunggu Validasi Admin
                            </span>

                        {{-- STATUS SELESAI --}}
                        @elseif ($p->status == 'selesai')
                            <span class="status-badge status-selesai">
                                <i class="fas fa-check-circle"></i> Selesai
                            </span>

                        {{-- STATUS DITOLAK --}}
                        @elseif ($p->status == 'ditolak')
                            <span class="status-badge status-ditolak">
                                <i class="fas fa-times-circle"></i> Ditolak
                            </span>
                        @endif
                    </td>
                </tr>

                {{-- MODAL KEMBALIKAN --}}
                <div class="modal fade" id="modalKembalikan{{ $p->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Pengembalian</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <div class="modal-body">
                                Apakah Anda yakin ingin mengembalikan ruangan/unit ini?
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <form action="{{ route('peminjaman.kembalikan', $p->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Ya, Kembalikan</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

            @empty
                <tr>
                    <td colspan="5" style="text-align:center;">Tidak ada peminjaman terbaru.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- === STYLE === --}}
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
        background: #ffffff;
        color: #333;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

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

    .bg-primary .card-icon { background-color: #007bff; }
    .bg-warning .card-icon { background-color: #ffc107; }
    .bg-success .card-icon { background-color: #28a745; }
    .bg-danger .card-icon { background-color: #dc3545; }
    .bg-info .card-icon { background-color: #17a2b8; }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .status-pending { background:#fff3cd; color:#856404; }
    .status-ditolak { background:#f8d7da; color:#721c24; }
    .status-selesai { background:#e2e3e5; color:#383d41; }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .data-table th {
        background: #f8f9fa;
        padding: 12px;
        text-align: left;
        font-weight: 700;
    }

    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #f1f1f1;
        vertical-align: middle;
    }

    .btn-success {
        background:#28a745; border:none; color:white; border-radius:8px; padding:5px 10px;
    }

    .btn-primary {
        background:#007bff; border:none; color:white; border-radius:8px; padding:5px 10px;
    }

    .btn-warning {
        background:#ffc107; border:none; color:black; border-radius:8px; padding:5px 10px;
    }
</style>

@endsection
