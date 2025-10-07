@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h2 class="mb-4 fw-bold text-primary">Dashboard Admin</h2>

    {{-- Statistik singkat --}}
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Total Peminjaman</h5>
                    <h2>{{ $jumlahPeminjaman }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Menunggu</h5>
                    <h2>{{ $menunggu }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Disetujui</h5>
                    <h2>{{ $disetujui }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Ditolak</h5>
                    <h2>{{ $ditolak }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Peminjaman Terbaru --}}
    <div class="card mt-4 shadow-sm border-0">
        <div class="card-header bg-primary text-white fw-bold">
            Peminjaman Terbaru
        </div>
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Mahasiswa</th>
                        <th>Ruangan</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Tanggal Pinjam</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamanTerbaru as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $p->namaMahasiswa }}</td>
                        <td>{{ $p->namaRuangan ?? '-' }}</td>
                        <td>{{ $p->keperluan }}</td>
                        <td>
                            <span class="badge bg-{{ $p->status == 'disetujui' ? 'success' : ($p->status == 'ditolak' ? 'danger' : 'warning') }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td>{{ $p->tanggalPinjam }}</td>
                        <td>
                            @if($p->status == 'pending')
                            <form action="{{ route('admin.peminjaman.update', $p->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button name="status" value="disetujui" class="btn btn-success btn-sm">Setujui</button>
                                <button name="status" value="ditolak" class="btn btn-danger btn-sm">Tolak</button>
                            </form>
                            @else
                            <em>-</em>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada pengajuan peminjaman.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection