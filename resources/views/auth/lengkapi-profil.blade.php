@extends('layouts.app')

@section('content')
<div class="profil-wrapper">
    <div class="profil-card">
        <div class="profil-header">
            <img src="{{ asset('assets/images/Teknologi Informasi.jpg') }}" alt="Logo TI" class="profil-logo">
            <h2>Lengkapi Profil Anda</h2>
            <p>Silakan isi data berikut sebelum melanjutkan ke dashboard.</p>
        </div>

        {{-- ✅ Pesan error validasi --}}
        @if ($errors->any())
            <div class="alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ✅ Pesan error dari session --}}
        @if (session('error'))
            <div class="alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('lengkapi.profil.store') }}" method="POST" class="profil-form" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="nim">NIM/NIP</label>
                <input type="text" id="nim" name="nim" 
                    value="{{ old('nim', $user->nim ?? '') }}" 
                    placeholder="Masukkan NIM atau NIP Anda" required>
            </div>

            <div class="form-group">
                <label for="role">Peran</label>
                <select name="role" id="role" required>
                    <option value="">-- Pilih Peran --</option>
                    <option value="mahasiswa" {{ old('role', $user->role ?? '') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ old('role', $user->role ?? '') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password Baru</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password baru" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru" required>
            </div>

            <button type="submit" class="btn-save">Simpan dan Lanjutkan</button>
        </form>
    </div>
</div>

@if (session('success'))
<div class="alert-success">
    {{ session('success') }}
</div>
@endif

<style scoped>
html, body {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    background: #0056b3;
    font-family: 'Poppins', sans-serif;
}

.profil-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
}

.profil-card {
    background: #fff;
    padding: 40px 45px;
    border-radius: 14px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 480px;
}

.profil-header {
    text-align: center;
    margin-bottom: 25px;
}

.profil-logo {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 10px;
}

.profil-header h2 {
    color: #003366;
    font-weight: 700;
    margin-bottom: 5px;
}

.profil-header p {
    color: #555;
    font-size: 14px;
}

.profil-form .form-group {
    margin-bottom: 18px;
}

.profil-form label {
    font-weight: 600;
    color: #333;
    display: block;
    margin-bottom: 6px;
    font-size: 14px;
}

.profil-form input,
.profil-form select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    background: #fafafa;
    transition: all 0.2s ease;
}

.profil-form input:focus,
.profil-form select:focus {
    border-color: #0056b3;
    background: #fff;
    outline: none;
}

.btn-save {
    width: 100%;
    background: #0056b3;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 12px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: 0.2s;
}

.btn-save:hover {
    background: #004494;
}

.alert-success {
    background: #28a745;
    color: #fff;
    padding: 12px 18px;
    border-radius: 8px;
    text-align: center;
    position: fixed;
    top: 20px;
    right: 20px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    font-weight: 500;
    z-index: 9999;
}

/* ✅ Tambahan untuk pesan error */
.alert-danger {
    background: #dc3545;
    color: #fff;
    padding: 12px 18px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 14px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

@media (max-width: 480px) {
    .profil-card {
        padding: 25px 20px;
    }
    .profil-header h2 {
        font-size: 1.3rem;
    }
}
</style>
@endsection