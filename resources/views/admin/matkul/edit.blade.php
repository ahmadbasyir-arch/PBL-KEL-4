@extends('layouts.app')

@section('title', 'Edit Mata Kuliah')

@section('content')
<div class="welcome-banner">
    <h1>Edit Mata Kuliah</h1>
    <p>Perbarui detail master data mata kuliah.</p>
</div>

<div class="interactive-table mt-4" style="max-width: 800px;">
    <div class="section-header mb-4">
        <h2>Form Edit Matkul</h2>
    </div>

    <form action="{{ route('admin.matkul.update', $mk->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Kode Matkul</label>
                <input type="text" name="kode" class="form-control" value="{{ old('kode', $mk->kode) }}" required>
            </div>
            
            <div class="col-md-8 mb-3">
                <label class="form-label fw-bold">Nama Mata Kuliah</label>
                <input type="text" name="nama_matkul" class="form-control" value="{{ old('nama_matkul', $mk->nama_matkul) }}" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Semester</label>
                <select name="semester" class="form-control" required>
                    @for ($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ old('semester', $mk->semester) == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Kurikulum</label>
                <input type="text" name="kurikulum" class="form-control" value="{{ old('kurikulum', $mk->kurikulum) }}" required placeholder="Contoh: 2024">
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('admin.matkul.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
