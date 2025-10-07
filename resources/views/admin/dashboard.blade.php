@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
@if (session('success'))
    <div class="success-message">
        {{ session('success') }}
    </div>
@endif

<div class="section-header">
    <h1>Dashboard Admin</h1>
    <p>Halo, {{ Auth::user()->name }} 👋 — berikut ringkasan kegiatan peminjaman.</p>
</div>

{{-- ==== Statistik ==== --}}
<div class="dashboard-cards">
    <div class="card">
        <div class="card-icon bg-primary"><i class="fas fa-box"></i></div>
        <div class="card-content">
            <h3>Total Peminjaman</h3>
            <p class="card-value">{{ $totalPeminjaman }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-icon bg-warning"><i class="fas fa-clock"></i></div>
        <div class="card-content">
            <h3>Menunggu Persetujuan</h3>
            <p class="card-value">{{ $totalPending }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
        <div class="card-content">
            <h3>Disetujui</h3>
            <p class="card-value">{{ $totalDisetujui }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-icon bg-danger"><i class="fas fa-times-circle"></i></div>
        <div class="card-content">
            <h3>Ditolak</h3>
            <p class="card-value">{{ $totalDitolak }}</p>
        </div>
    </div>
</div>

{{-- ==== Data Peminjaman ==== --}}
<div class="interactive-table mt-4">
    <div class="section-header">
        <h2>Data Peminjaman Terbaru</h2>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Mahasiswa</th>
                <th>Nama Akun</th>
                <th>Dipinjam</th>
                <th>Keperluan</th>
                <th>Status</th>
                <th>Tanggal Pinjam</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($peminjamanTerkini as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->user->name ?? '-' }}</td>
                    <td>{{ $p->user->email ?? '-' }}</td>
                    <td>
                        @if($p->ruangan)
                            {{ $p->ruangan->namaRuangan }}
                        @elseif($p->unit)
                            {{ $p->unit->namaUnit }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $p->keperluan ?? '-' }}</td>
                    <td>
                        <span class="status-badge status-{{ $p->status }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggalPinjam)->isoFormat('D MMM YYYY') }}</td>
                    <td class="action-links">
                        @if ($p->status === 'pending')
                            <form action="{{ route('admin.peminjaman.approve', $p->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-approve">Setujui</button>
                            </form>
                            <form action="{{ route('admin.peminjaman.reject', $p->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-reject">Tolak</button>
                            </form>
                        @else
                            <em>Tidak ada aksi</em>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">Tidak ada data peminjaman terbaru.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ==== Tambahan CSS ==== --}}
<style>
.btn-approve {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 6px 14px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.2s;
}
.btn-approve:hover { background-color: #218838; }

.btn-reject {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 6px 14px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.2s;
}
.btn-reject:hover { background-color: #c82333; }

.status-badge.status-pending {
    background-color: #ffc107;
    color: #000;
    padding: 4px 8px;
    border-radius: 5px;
    font-weight: 600;
}
.status-badge.status-disetujui {
    background-color: #28a745;
    color: #fff;
    padding: 4px 8px;
    border-radius: 5px;
    font-weight: 600;
}
.status-badge.status-ditolak {
    background-color: #dc3545;
    color: #fff;
    padding: 4px 8px;
    border-radius: 5px;
    font-weight: 600;
}
</style>
@endsection