@extends('layouts.app')

@section('content')
    <div class="welcome-banner">
        <h1>Tambah Program Studi</h1>
        <p>Tambahkan program studi baru ke dalam sistem.</p>
    </div>

    <div class="interactive-table">
        <div class="section-header" style="margin-bottom: 20px;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-plus-circle" style="color: #3b82f6;"></i> Form Tambah Prodi
            </h2>
        </div>

        <form action="{{ route('superadmin.prodi.store') }}" method="POST">
            @csrf
            
            <div class="form-group mb-3">
                <label style="font-weight: 600; color: #374151; margin-bottom: 5px; display: block;">Kode Prodi</label>
                <input type="text" name="kode_prodi" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;" 
                    value="{{ old('kode_prodi') }}" required placeholder="Contoh: TI, SI, AK">
                @error('kode_prodi')
                    <div style="color: #ef4444; font-size: 0.875rem; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label style="font-weight: 600; color: #374151; margin-bottom: 5px; display: block;">Nama Prodi</label>
                <input type="text" name="nama_prodi" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;" 
                    value="{{ old('nama_prodi') }}" required placeholder="Contoh: Teknologi Informasi">
                @error('nama_prodi')
                    <div style="color: #ef4444; font-size: 0.875rem; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-4" style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-success shadow-sm" style="background-color: #10b981; border: none; padding: 10px 20px; border-radius: 6px; color: white; font-weight: 600;">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('superadmin.prodi.index') }}" class="btn btn-secondary" style="background-color: #6b7280; border: none; padding: 10px 20px; border-radius: 6px; color: white; font-weight: 600; text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
@endsection
