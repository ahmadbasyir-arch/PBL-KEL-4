@extends('layouts.app')

@section('title', 'Manajemen Data Pengguna (Dosen)')

@section('content')
    <div class="welcome-banner">
        <h1>Manajemen Data Pengguna (DOSEN)</h1>
        <p>Kelola data mahasiswa dan dosen (Edit & Hapus).</p>
    </div>

    {{-- ===================== MAHASISWA ===================== --}}
    <div class="interactive-table" style="margin-bottom: 30px;">
        <div class="section-header" style="margin-bottom: 20px;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-user-graduate" style="color: #3b82f6;"></i> Daftar Mahasiswa
            </h2>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Tanggal Daftar</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mahasiswa as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td style="text-align: center;">
                            <div class="action-buttons" style="justify-content: center; display: flex; gap: 5px;">
                                <a href="{{ route('dosen.pengguna.edit', $user->id) }}" class="btn btn-warning btn-sm" style="background-color: #f59e0b; border: none; color: white; padding: 5px 10px; border-radius: 4px;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('dosen.pengguna.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="background-color: #ef4444; border: none; color: white; padding: 5px 10px; border-radius: 4px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3" style="text-align: center; padding: 20px; color: #6b7280;">Belum ada data mahasiswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ===================== DOSEN ===================== --}}
    <div class="interactive-table">
        <div class="section-header" style="margin-bottom: 20px;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-chalkboard-teacher" style="color: #10b981;"></i> Daftar Dosen
            </h2>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Tanggal Daftar</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dosen as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td style="text-align: center;">
                            <div class="action-buttons" style="justify-content: center; display: flex; gap: 5px;">
                                <a href="{{ route('dosen.pengguna.edit', $user->id) }}" class="btn btn-warning btn-sm" style="background-color: #f59e0b; border: none; color: white; padding: 5px 10px; border-radius: 4px;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('dosen.pengguna.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="background-color: #ef4444; border: none; color: white; padding: 5px 10px; border-radius: 4px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3" style="text-align: center; padding: 20px; color: #6b7280;">Belum ada data dosen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
