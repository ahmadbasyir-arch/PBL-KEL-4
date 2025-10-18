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
    <p>Halo, {{ Auth::user()->namaLengkap }} 👋 — berikut ringkasan kegiatan peminjaman.</p>
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
                    <td>{{ $p->user->namaLengkap ?? '-' }}</td>
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
                            {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                        </span>
                    </td>
                    <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($p->tanggalPinjam)->isoFormat('D MMM YYYY') }}</td>
                    <td>
                        {{-- ✅ Perbaikan logika aksi agar tidak muncul tanda "-" --}}
                        <div class="action-buttons">
                            @if ($p->status === 'pending')
                                <form action="{{ route('admin.peminjaman.approve', $p->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-approve">Setujui</button>
                                </form>
                                <form action="{{ route('admin.peminjaman.reject', $p->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-reject">Tolak</button>
                                </form>

                            @elseif ($p->status === 'disetujui' || $p->status === 'digunakan')
                                {{-- 🔹 Saat admin sudah menyetujui --}}
                                <span class="status-badge status-disetujui">
                                    <i class="fas fa-play"></i> Sedang Digunakan
                                </span>

                            @elseif ($p->status === 'menyelesaikan' || $p->status === 'menunggu_validasi')
                                {{-- 🔹 Mahasiswa sudah mengajukan selesai --}}
                                <form action="{{ route('admin.peminjaman.validate', $p->id) }}" method="POST" onsubmit="return confirm('Validasi peminjaman ini sebagai selesai?')">
                                    @csrf
                                    <button type="submit" class="btn-validate">Validasi</button>
                                </form>
                                <form action="{{ route('admin.peminjaman.reject', $p->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-reject">Tolak Selesai</button>
                                </form>

                            @elseif ($p->status === 'selesai')
                                <span class="status-badge status-selesai">
                                    <i class="fas fa-check-circle"></i> Selesai
                                </span>

                            @elseif ($p->status === 'ditolak')
                                <span class="status-badge status-ditolak">
                                    <i class="fas fa-times-circle"></i> Ditolak
                                </span>

                            @else
                                <em>-</em>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;">Tidak ada data peminjaman terbaru.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
    .action-buttons {
        display: flex;
        gap: 5px;
        white-space: nowrap;
    }
    .action-buttons form {
        display: inline-block;
    }
    .btn-approve, .btn-reject, .btn-complete, .btn-validate {
        border: none;
        padding: 6px 14px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.2s;
        color: white;
    }
    .btn-approve { background-color: #28a745; } .btn-approve:hover { background-color: #218838; }
    .btn-reject { background-color: #dc3545; } .btn-reject:hover { background-color: #c82333; }
    .btn-complete { background-color: #007bff; } .btn-complete:hover { background-color: #0056b3; }
    .btn-validate { background-color: #17a2b8; } .btn-validate:hover { background-color: #138496; }

    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-disetujui, .status-digunakan { background: #d4edda; color: #155724; }
    .status-ditolak { background: #f8d7da; color: #721c24; }
    .status-selesai { background: #e2e3e5; color: #383d41; }
    .status-menyelesaikan, .status-menunggu_validasi { background: #ffeeba; color: #856404; }
</style>
@endsection