@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')

    <div class="interactive-table">
        <div class="section-header" style="margin-bottom: 20px;">

            <h2>Edit Pengguna</h2>
        </div>

        <form action="{{ route('admin.pengguna.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label class="form-label" style="font-weight: 600; color: #374151;">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required
                               style="padding: 10px; border-radius: 6px; border: 1px solid #d1d5db;">
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label class="form-label" style="font-weight: 600; color: #374151;">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required
                               style="padding: 10px; border-radius: 6px; border: 1px solid #d1d5db;">
                    </div>
                </div>

                <!-- Kolom Kiri -->
                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label class="form-label" style="font-weight: 600; color: #374151;">Role</label>
                        <select name="role" class="form-control" required
                                style="padding: 10px; border-radius: 6px; border: 1px solid #d1d5db;">
                            <option value="mahasiswa" {{ $user->role == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="dosen" {{ $user->role == 'dosen' ? 'selected' : '' }}>Dosen</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label class="form-label" style="font-weight: 600; color: #374151;">Password <small class="text-muted fw-normal">(Opsional)</small></label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah"
                               style="padding: 10px; border-radius: 6px; border: 1px solid #d1d5db;">
                    </div>
                </div>
            </div>

            <div class="form-actions d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.pengguna.index') }}" class="btn btn-secondary px-4 py-2" style="border-radius: 6px;">Batal</a>
                <button type="submit" class="btn btn-primary px-4 py-2" style="background-color: #3b82f6; border: none; border-radius: 6px;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection
