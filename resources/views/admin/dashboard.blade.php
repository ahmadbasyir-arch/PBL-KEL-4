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
    <p>Halo, {{ Auth::user()->namaLengkap ?? Auth::user()->name }} 👋 — berikut ringkasan kegiatan peminjaman.</p>
</div>

{{-- ==== Statistik ==== --}}
<div class="dashboard-cards">
    <div class="card stat-card">
        <div class="card-icon bg-primary"><i class="fas fa-box"></i></div>
        <div class="card-content">
            <h3>Total Peminjaman</h3>
            <p class="card-value">{{ $totalPeminjaman }}</p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="card-icon bg-warning"><i class="fas fa-clock"></i></div>
        <div class="card-content">
            <h3>Menunggu Persetujuan</h3>
            <p class="card-value">{{ $totalPending }}</p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
        <div class="card-content">
            <h3>Disetujui</h3>
            <p class="card-value">{{ $totalDisetujui }}</p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="card-icon bg-danger"><i class="fas fa-times-circle"></i></div>
        <div class="card-content">
            <h3>Ditolak</h3>
            <p class="card-value">{{ $totalDitolak }}</p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="card-icon bg-secondary"><i class="fas fa-history"></i></div>
        <div class="card-content">
            <h3>Riwayat Peminjaman</h3>
            <p class="card-value">{{ $totalRiwayat ?? 0 }}</p>
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
                <th style="width:48px">No</th>
                <th>Nama Peminjam</th>
                <th>Dipinjam</th>
                <th>Keperluan</th>
                <th style="width:140px">Status</th>
                <th style="white-space:nowrap">Tanggal Pinjam</th>
                <th style="width:170px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($peminjamanTerkini as $index => $p)
                @php 
                    $status = strtolower($p->status);
                @endphp

                <tr>
                    <td>{{ $index + 1 }}</td>

                    <td style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $p->mahasiswa->namaLengkap ?? $p->mahasiswa->name ?? '-' }}
                    </td>

                    <td>
                        @if($p->ruangan)
                            {{ $p->ruangan->namaRuangan }}
                        @elseif($p->unit)
                            {{ $p->unit->namaUnit }}
                        @else
                            -
                        @endif
                    </td>

                    <td style="max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $p->keperluan ?? '-' }}
                    </td>

                    {{-- STATUS BADGE --}}
                    <td>
    @php 
        $status = strtolower($p->status);

        $class = match($status) {
            'pending' => 'status-pending',
            'disetujui' => 'status-disetujui',
            'digunakan', 'sedang digunakan' => 'status-digunakan',
            'menyelesaikan' => 'status-menyelesaikan',
            'menunggu_validasi' => 'status-menunggu_validasi',
            'selesai' => 'status-selesai',
            'ditolak' => 'status-ditolak',
            default => 'status-default'
        };
    @endphp

    <span class="status-badge {{ $class }}">
        {{ ucfirst($status) }}
    </span>
</td>


                    <td style="white-space: nowrap;">
                        {{ \Carbon\Carbon::parse($p->tanggalPinjam)->isoFormat('D MMM YYYY') }}
                    </td>

                    {{-- Aksi --}}
                    <td>
                        <div class="action-buttons">

                            {{-- Pending --}}
                            @if ($status === 'pending')
                                <form action="{{ route('admin.peminjaman.approve', $p->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-approve">Setujui</button>
                                </form>

                                <form action="{{ route('admin.peminjaman.reject', $p->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-reject">Tolak</button>
                                </form>

                            {{-- Sedang digunakan --}}
                            @elseif (in_array($status, ['disetujui', 'digunakan', 'sedang digunakan']))
                                <span class="status-badge status-disetujui">
                                    <i class="fas fa-play"></i> Sedang Digunakan
                                </span>

                            {{-- Menunggu validasi --}}
                            @elseif (in_array($status, ['menyelesaikan', 'menunggu_validasi']))
                                <form action="{{ route('admin.peminjaman.validate', $p->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-validate"
                                        onclick="return confirm('Validasi peminjaman ini sebagai selesai?')">Validasi</button>
                                </form>

                                <form action="{{ route('admin.peminjaman.reject', $p->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-reject">Tolak Selesai</button>
                                </form>

                            {{-- Selesai --}}
                            @elseif ($status === 'selesai')
                                <span class="status-badge status-selesai">
                                    <i class="fas fa-check-circle"></i> Selesai
                                </span>

                            {{-- Ditolak --}}
                            @elseif ($status === 'ditolak')
                                <span class="status-badge status-ditolak">
                                    <i class="fas fa-times-circle"></i> Ditolak
                                </span>

                            {{-- Tidak diketahui --}}
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

{{-- === Styling tampilan (presentasi) === --}}
<style>
    /* Statistik cards */
    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px; margin-top: 18px;
    }
    .stat-card { display:flex; align-items:center; gap:14px; padding:18px; background:#fff;
        border-radius:12px; box-shadow:0 6px 18px rgba(18,38,63,.06); transition:0.18s; }
    .stat-card:hover { transform:translateY(-4px); box-shadow:0 10px 26px rgba(18,38,63,.08); }

    .card-icon { width:56px; height:56px; border-radius:50%; display:flex; align-items:center;
        justify-content:center; color:#fff; font-size:20px; }

    .bg-primary{background:linear-gradient(180deg,#007bff,#0062d6);}
    .bg-warning{background:linear-gradient(180deg,#ffc107,#e0a800);}
    .bg-success{background:linear-gradient(180deg,#28a745,#1f7a34);}
    .bg-danger{background:linear-gradient(180deg,#dc3545,#b21f2d);}
    .bg-secondary{background:linear-gradient(180deg,#6c757d,#495057);}

    /* Table */
    .data-table{width:100%; border-collapse:collapse; background:#fff; border-radius:10px;
        overflow:hidden; box-shadow:0 6px 18px rgba(18,38,63,.04); }

    .data-table thead th{background:#f6f7f9; padding:12px 14px; font-weight:700; border-bottom:1px solid #eee;}
    .data-table tbody td{padding:12px 14px; border-bottom:1px solid #f1f1f1;}

    .action-buttons{display:flex; gap:8px; flex-wrap:wrap;}

    .btn-approve,.btn-reject,.btn-validate{
        padding:6px 12px; border-radius:8px; font-weight:700; border:none; cursor:pointer; color:#fff;
    }

    .btn-approve{background:#28a745;} .btn-approve:hover{background:#218838;}
    .btn-reject{background:#dc3545;} .btn-reject:hover{background:#c82333;}
    .btn-validate{background:#17a2b8;} .btn-validate:hover{background:#128a99;}

    /* Status badges */
    .status-badge{
        display:inline-flex; align-items:center; gap:8px;
        padding:6px 10px; border-radius:10px; font-weight:700; font-size:0.9rem;
    }

    .status-pending{background:#fff3cd; color:#856404;}
    .status-disetujui,.status-digunakan{background:#d4edda; color:#155724;}
    .status-ditolak{background:#f8d7da; color:#721c24;}
    .status-selesai{background:#e2e3e5; color:#383d41;}
    .status-menyelesaikan,.status-menunggu_validasi{background:#ffeeba; color:#856404;}
</style>

@endsection
