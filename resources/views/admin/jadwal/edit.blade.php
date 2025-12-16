@extends('layouts.app')

@section('title', 'Edit Jadwal')

@section('content')
<div class="welcome-banner">
    <h1>Edit Jadwal Perkuliahan</h1>
    <p>Perbarui detail jadwal perkuliahan.</p>
</div>

<div class="interactive-table mt-4" style="max-width: 800px;">
    <div class="section-header mb-4">
        <h2>Form Edit Jadwal</h2>
    </div>

    <form action="{{ route('admin.jadwal.update', $jadwal->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Hari</label>
                <select name="hari" class="form-control" required>
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                        <option value="{{ $hari }}" {{ old('hari', $jadwal->hari) == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Jam Mulai</label>
                <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i')) }}" required>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Jam Selesai</label>
                <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i')) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Mata Kuliah</label>
            <input type="text" name="mata_kuliah" class="form-control" value="{{ old('mata_kuliah', $jadwal->mata_kuliah) }}" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Dosen</label>
                <input type="text" name="dosen" class="form-control" value="{{ old('dosen', $jadwal->dosen) }}" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Kelas</label>
                <input type="text" name="kelas" class="form-control" value="{{ old('kelas', $jadwal->kelas) }}" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Ruangan</label>
            <select name="ruangan_id" class="form-control">
                <option value="">-- Pilih Ruangan (Opsional) --</option>
                @foreach($ruangans as $ruangan)
                    <option value="{{ $ruangan->id }}" {{ old('ruangan_id', $jadwal->ruangan_id) == $ruangan->id ? 'selected' : '' }}>
                        {{ $ruangan->namaRuangan }} ({{ $ruangan->lokasi }})
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Jika kosong, jadwal tidak terkait dengan ruangan fisik.</small>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
