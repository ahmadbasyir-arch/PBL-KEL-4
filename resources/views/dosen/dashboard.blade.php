@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('content')
@if (session('success'))
    <div class="success-message">
        {{ session('success') }}
    </div>
@endif

<div class="welcome-banner">
    <h1>Selamat Datang, {{ ucwords(Auth::user()->name ?? Auth::user()->username ?? 'Dosen') }}! 👋</h1>
    <p>Anda login sebagai Dosen. Berikut adalah ringkasan aktivitas peminjaman Anda.</p>
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
                    <td class="text-center">{{ $peminjamanTerkini->total() - ($peminjamanTerkini->firstItem() + $loop->index) + 1 }}</td>
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
                            $isDigunakan = in_array($p->status, ['digunakan', 'disetujui']);
                            $isMenyelesaikan = in_array($p->status, ['menyelesaikan', 'menunggu_validasi']);
                            $canEdit = in_array($p->status, ['pending']);
                        @endphp

                        {{-- STATUS PENDING --}}
                        @if ($p->status == 'pending')
                            <span class="status-badge status-pending">
                                <i class="fas fa-hourglass-half"></i> Menunggu Persetujuan
                            </span>

                            {{-- 🔥 TOMBOL EDIT --}}
                            <div style="display:inline-block; margin-left:6px;">
                                <a href="{{ route('dosen.peminjaman.edit', $p->id) }}" 
                                class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>


                        {{-- STATUS DISETUJUI / DIGUNAKAN --}}
                        @elseif ($isDigunakan)
                            <form action="{{ route('peminjaman.ajukanSelesai', $p->id) }}"
                                  method="POST" style="display:inline;"
                                  onsubmit="return confirm('Ajukan penyelesaian? Setelah ini akan divalidasi admin.')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i> Ajukan Selesai
                                </button>
                            </form>

                            <form action="{{ route('peminjaman.ajukanSelesai', $p->id) }}"
                                  method="POST" style="display:inline; margin-left:6px;"
                                  onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan ruangan/unit ini?')">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-undo"></i> Kembalikan
                                </button>
                            </form>

                            @if ($canEdit)
                            <div style="display:inline-block; margin-left:6px;">
                                <a href="{{ route('dosen.peminjaman.edit', $p->id) }}" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
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
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;">Tidak ada peminjaman terbaru.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">
        {{ $peminjamanTerkini->links('pagination::bootstrap-4') }}
    </div>
</div>

{{-- === STYLE === --}}


@endsection
