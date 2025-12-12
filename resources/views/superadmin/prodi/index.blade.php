@extends('layouts.app')

@section('content')
    <div class="welcome-banner">
        <h1>Data Program Studi</h1>
        <p>Kelola data program studi untuk sistem peminjaman.</p>
    </div>

    <div class="interactive-table">
        <div class="section-header" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 10px; margin: 0;">
                <i class="fas fa-university" style="color: #3b82f6;"></i> Daftar Program Studi
            </h2>
            <a href="{{ route('superadmin.prodi.create') }}" class="btn btn-primary shadow-sm" style="background-color: #3b82f6; border: none; padding: 8px 16px; border-radius: 6px; color: white; font-weight: 600; text-decoration: none; font-size: 0.9rem;">
                <i class="fas fa-plus"></i> Tambah Prodi
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%" style="text-align: center;">No</th>
                    <th width="15%">Kode Prodi</th>
                    <th width="60%">Nama Prodi</th>
                    <th width="20%" style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prodi as $item)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->kode_prodi }}</strong></td>
                        <td>{{ $item->nama_prodi }}</td>
                        <td style="text-align: center;">
                            <div class="action-buttons" style="justify-content: center; display: flex; gap: 5px;">
                                <a href="{{ route('superadmin.prodi.edit', $item->id) }}" class="btn btn-warning btn-sm" style="background-color: #f59e0b; border: none; color: white; padding: 5px 10px; border-radius: 4px;" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('superadmin.prodi.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus prodi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="background-color: #ef4444; border: none; color: white; padding: 5px 10px; border-radius: 4px;" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3" style="text-align: center; padding: 20px; color: #6b7280;">
                            Belum ada data program studi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
