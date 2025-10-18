@extends('layouts.app')

@section('title', 'Data Peminjaman')

@section('content')
    <div class="section-header">
        <h1>Data Peminjaman Terbaru</h1>
    </div>

    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="error-message">{{ session('error') }}</div>
    @endif

    <div class="interactive-table">
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
                @forelse ($peminjaman as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $p->user->name ?? '-' }}</td>
                        <td>
                            @if ($p->ruangan)
                                {{ $p->ruangan->namaRuangan }}
                            @elseif ($p->unit)
                                {{ $p->unit->namaUnit }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $p->keperluan }}</td>
                        <td>
                            @php
                                $label = ucfirst(str_replace('_', ' ', $p->status));
                                $class = match ($p->status) {
                                    'pending' => 'status-pending',
                                    'disetujui', 'digunakan' => 'status-disetujui',
                                    'menyelesaikan', 'menunggu_validasi' => 'status-warning',
                                    'selesai' => 'status-selesai',
                                    'ditolak' => 'status-ditolak',
                                    default => 'status-default',
                                };
                            @endphp
                            <span class="status-badge {{ $class }}">{{ $label }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggalPinjam)->isoFormat('D MMM YYYY') }}</td>
                        <td>
                            {{-- 🔹 Tombol aksi sesuai status --}}
                            @if ($p->status === 'pending')
                                <form action="{{ route('admin.peminjaman.approve', $p->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn btn-success btn-sm"><i class="fas fa-check"></i> Setuju</button>
                                </form>
                                <form action="{{ route('admin.peminjaman.reject', $p->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Tolak</button>
                                </form>

                            @elseif ($p->status === 'disetujui' || $p->status === 'digunakan')
                                <span class="status-badge status-disetujui">
                                    <i class="fas fa-play"></i> Sedang Digunakan
                                </span>

                            @elseif ($p->status === 'menyelesaikan' || $p->status === 'menunggu_validasi')
                                <form action="{{ route('admin.peminjaman.validate', $p->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn btn-success btn-sm"><i class="fas fa-check"></i> Selesaikan</button>
                                </form>
                                <form action="{{ route('admin.peminjaman.reject', $p->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Tolak Selesai</button>
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
                                <span>-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;">Belum ada data peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ✅ Styling tambahan --}}
    <style>
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 600;
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
        .status-warning {
            background: #ffeeba;
            color: #856404;
        }
        .btn {
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 600;
            margin: 2px;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
            border: none;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
        }
    </style>
@endsection