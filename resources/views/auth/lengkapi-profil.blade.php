@extends('layouts.app') {{-- Jika kamu pakai layout utama --}}
@section('content')
<div class="profil-wrapper">
    <div class="profil-card">
        <h2>Lengkapi Profil Anda</h2>
        <p class="subtitle">Silakan isi data berikut sebelum melanjutkan ke dashboard.</p>

        <form action="{{ route('lengkapi.profil.store') }}" method="POST" class="profil-form">

            @csrf

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
            </div>

            <div class="form-group">
                <label for="nim">NIM/NIP</label>
                <input type="text" id="nim" name="nim" value="{{ old('nim', $user->nim ?? '') }}" required>
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
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn-save">Simpan dan Lanjutkan</button>
        </form>
    </div>
</div>

<style>
/* === Background & Wrapper === */
.profil-wrapper {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #004d99, #0073e6);
    padding: 20px;
}

/* === Card === */
.profil-card {
    background: #fff;
    padding: 40px 50px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 500px;
    animation: fadeIn 0.6s ease-in-out;
}

/* === Header === */
.profil-card h2 {
    color: #004d99;
    font-weight: 700;
    text-align: center;
    margin-bottom: 10px;
}

.subtitle {
    text-align: center;
    color: #666;
    margin-bottom: 25px;
}

/* === Form === */
.profil-form .form-group {
    margin-bottom: 20px;
}

.profil-form label {
    font-weight: 600;
    color: #333;
    display: block;
    margin-bottom: 6px;
}

.profil-form input,
.profil-form select {
    width: 100%;
    padding: 10px 12px;
    border: 1.5px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
}

.profil-form input:focus,
.profil-form select:focus {
    border-color: #0073e6;
    box-shadow: 0 0 5px rgba(0,115,230,0.3);
    outline: none;
}

/* === Button === */
.btn-save {
    width: 100%;
    background: #0073e6;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-save:hover {
    background: #005bb5;
}

/* === Animations === */
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(-20px);}
    to {opacity: 1; transform: translateY(0);}
}
</style>
@endsection