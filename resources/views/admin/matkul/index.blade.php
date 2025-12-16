@extends('layouts.app')

@section('title', 'Data Mata Kuliah')

@section('content')

{{-- WELCOME BANNER --}}
<div class="welcome-banner">
    <h1>Manajemen Data Mata Kuliah</h1>
    <p>Kelola master data mata kuliah dan import data dari Excel.</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- IMPORT SECTION & TABLE --}}
<div class="interactive-table mt-4">
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Mata Kuliah</h2>
        
        <div class="d-flex align-items-center gap-2">
            <div class="alert alert-info py-2 mb-0" style="font-size: 0.85rem;">
                <i class="fas fa-info-circle"></i> Header Excel: <b>Kode, Mata Kuliah, Semester, Kurikulum</b>
            </div>

            <form action="{{ route('admin.matkul.import') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                @csrf
                <input type="file" name="file" class="form-control form-control-sm" accept=".csv, .xls, .xlsx" required style="max-width: 200px;">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-upload"></i> Import
                </button>
            </form>

            <form action="{{ route('admin.matkul.reset') }}" method="POST" onsubmit="return confirm('Yakin hapus SEMUA data mata kuliah?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> Reset
                </button>
            </form>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Kode</th>
                <th>Nama Mata Kuliah</th>
                <th>Semester</th>
                <th>Kurikulum</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($matkuls as $mk)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-bold">{{ $mk->kode }}</td>
                    <td>{{ $mk->nama_matkul }}</td>
                    <td>Semester {{ $mk->semester }}</td>
                    <td>{{ $mk->kurikulum }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('admin.matkul.edit', $mk->id) }}" class="btn btn-warning btn-sm text-white"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.matkul.destroy', $mk->id) }}" method="POST" onsubmit="return confirm('Hapus matkul ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data mata kuliah.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
    .form-control-sm { padding: 5px 10px; font-size: 0.875rem; border-radius: 6px; border: 1px solid #d1d5db; }
    .gap-2 { gap: 0.5rem; }
</style>

@endsection
