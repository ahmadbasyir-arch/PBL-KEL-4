@extends('layouts.app')

@section('title', 'Data Pengguna')

@section('content')
<div class="container-fluid px-4 py-4">
    <h2 class="fw-bold mb-3">Manajemen Data Pengguna</h2>
    <p class="text-muted mb-4">Semua pengguna yang terdaftar di sistem berdasarkan peran (Mahasiswa & Dosen).</p>

    {{-- ===================== MAHASISWA ===================== --}}
    <div id="mahasiswa" class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="fas fa-user-graduate me-2"></i> Daftar Mahasiswa
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswa as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Belum ada data mahasiswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===================== DOSEN ===================== --}}
    <div id="dosen" class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-success text-white fw-semibold">
            <i class="fas fa-chalkboard-teacher me-2"></i> Daftar Dosen
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dosen as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Belum ada data dosen.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection