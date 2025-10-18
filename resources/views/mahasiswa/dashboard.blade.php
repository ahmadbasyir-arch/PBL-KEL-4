@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <div class="section-header">
        <h1>Selamat Datang, {{ Auth::user()->name }}!</h1>
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
                            {{-- ✅ Status dan tombol aksi --}}
                            @php
                                $isDigunakan = in_array($p->status, ['digunakan', 'disetujui']);
                                $isMenyelesaikan = in_array($p->status, ['menyelesaikan', 'menunggu_validasi']);
                            @endphp

                            @if ($p->status == 'pending')
                                <span class="status-badge status-pending">
                                    <i class="fas fa-hourglass-half"></i> Menunggu Persetujuan
                                </span>

                            @elseif ($isDigunakan)
                                {{-- ✅ Jika status 'digunakan' atau 'disetujui' -> tampilkan tombol Ajukan Selesai --}}
                                <form action="{{ route('peminjaman.ajukanSelesai', $p->id) }}" method="POST" style="display:inline;"
                                    onsubmit="return confirm('Apakah Anda yakin ingin mengajukan penyelesaian peminjaman ini? Setelah ini akan divalidasi oleh admin.')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" style="border-radius: 6px; padding: 5px 10px;">
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

    {{-- ✅ Styling tambahan agar tombol & badge terlihat modern --}}
    <style>
        .btn-success {
            background-color: #28a745;
            border: none;
            color: white;
            font-weight: 600;
            transition: 0.2s ease;
        }
        .btn-success:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-disetujui {
            background-color: #d4edda;
            color: #155724;
        }
        .status-ditolak {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-selesai {
            background-color: #e2e3e5;
            color: #383d41;
        }
    </style>
@endsection