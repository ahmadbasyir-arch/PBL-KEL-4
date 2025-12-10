@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
<div class="container">
    <div class="interactive-table">
        <div class="section-header" style="margin-bottom: 20px;">
            <h2>Edit Pengguna</h2>
        </div>

        <form action="{{ route('admin.pengguna.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group mb-3">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="form-group mb-3">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="mahasiswa" {{ $user->role == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ $user->role == 'dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label>Password (Kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('admin.pengguna.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
