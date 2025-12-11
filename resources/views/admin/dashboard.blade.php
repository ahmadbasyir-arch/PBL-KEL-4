@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
@if (session('success'))
    <div class="success-message">
        {{ session('success') }}
    </div>
@endif

<div class="welcome-banner">
    <h1>Dashboard Admin</h1>
    <p>Halo, {{ Auth::user()->namaLengkap ?? Auth::user()->name }} 👋 — berikut ringkasan kegiatan peminjaman.</p>
</div>

{{-- ==== Statistik ==== --}}
<div class="dashboard-cards">
    <div class="card stat-card">
        <div class="card-icon bg-primary"><i class="fas fa-box"></i></div>
        <div class="card-content">
            <h3>Total Peminjaman</h3>
            <p class="card-value" id="stat-total">{{ $totalPeminjaman }}</p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="card-icon bg-warning"><i class="fas fa-clock"></i></div>
        <div class="card-content">
            <h3>Menunggu Persetujuan</h3>
            <p class="card-value" id="stat-pending">{{ $totalPending }}</p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
        <div class="card-content">
            <h3>Disetujui</h3>
            <p class="card-value" id="stat-disetujui">{{ $totalDisetujui }}</p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="card-icon bg-danger"><i class="fas fa-times-circle"></i></div>
        <div class="card-content">
            <h3>Ditolak</h3>
            <p class="card-value" id="stat-ditolak">{{ $totalDitolak }}</p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="card-icon bg-secondary"><i class="fas fa-history"></i></div>
        <div class="card-content">
            <h3>Riwayat Peminjaman</h3>
            <p class="card-value" id="stat-riwayat">{{ $totalRiwayat ?? 0 }}</p>
        </div>
    </div>
</div>

{{-- ==== CHARTS (DITAMBAHKAN DI SINI) ==== --}}
<div class="chart-grid">
    @include('admin._charts')
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
                    <td class="text-center">{{ $peminjamanTerkini->total() - ($peminjamanTerkini->firstItem() + $loop->index) + 1 }}</td>

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
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Setujui</button>
                                </form>

                                <form action="{{ route('admin.peminjaman.reject', $p->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Tolak</button>
                                </form>

                            {{-- Sedang digunakan --}}
                            @elseif (in_array($status, ['disetujui', 'digunakan', 'sedang digunakan']))
                                <span class="status-badge status-disetujui">
                                    <i class="fas fa-play"></i> Sedang Digunakan
                                </span>

                            {{-- Menunggu validasi --}}
                            @elseif (in_array($status, ['menyelesaikan', 'menunggu_validasi']))

                                {{-- 🔥 VALIDASI MENGGUNAKAN GET (MASUK FORM JIKA UNIT) --}}
                                <form action="{{ route('admin.peminjaman.formValidasi', $p->id) }}" method="GET" style="display:inline;">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Validasi
                                    </button>
                                </form>

                                <form action="{{ route('admin.peminjaman.reject', $p->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Tolak Selesai</button>
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
    <div class="d-flex justify-content-center mt-3">
        {{ $peminjamanTerkini->links('pagination::bootstrap-4') }}
    </div>
</div>

@endsection
