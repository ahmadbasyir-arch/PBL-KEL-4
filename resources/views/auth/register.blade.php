<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Sarpras TI</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>

<body class="login-body">
<div class="login-container">
    <div class="login-left">
        <img src="{{ asset('assets/images/Gedung Nadiem Makarim 1.jpg') }}" alt="Gedung Kuliah" class="login-background-img">
        <div class="welcome-text">
            <h1>BUAT AKUN BARU</h1>
            <p>Sistem Informasi Peminjaman Sarana dan Prasarana Berbasis Web untuk Prodi TI</p>
            <h2>POLITEKNIK NEGERI TANAH LAUT</h2>
        </div>
    </div>

    <div class="login-right">
        <div class="login-form-header">
            <div class="login-logos">
                <img src="{{ asset('assets/images/Politeknik Negeri Tanah Laut.jpg') }}" alt="Logo Politala" class="logo-politala">
                <img src="{{ asset('assets/images/Teknologi Informasi.jpg') }}" alt="Logo TI" class="logo-ti">
            </div>
            <h2>Formulir Pendaftaran</h2>
        </div>

        {{-- Error Validasi --}}
        @if ($errors->any())
            <div class="error-message">
                @foreach ($errors->all() as $error)
                    <span>{{ $error }}</span><br>
                @endforeach
            </div>
        @endif

        {{-- FORM REGISTER --}}
        <form action="{{ route('register') }}" method="POST" class="login-form">
            @csrf

            <div class="form-group">
                <label for="namaLengkap">Nama Lengkap</label>
                <input type="text" id="namaLengkap" name="namaLengkap" required>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="nim">NIM / NIP</label>
                <input type="text" id="nim" name="nim" required>
            </div>

            <div class="form-group">
                <label for="role">Peran</label>
                <select id="role" name="role" required>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                </select>
            </div>

            {{-- =========== PERBAIKAN DISINI =========== --}}
            <div class="form-group">
                <label for="telepon">Nomor WhatsApp</label>
                <input type="text" id="telepon" name="telepon" placeholder="08xxxxxxxxxx" required>
            </div>
            {{-- ========================================= --}}

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-input-group">
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>
            </div>

            <button type="submit" name="register" class="btn-login">Daftar</button>
        </form>

        <div class="login-footer">
            <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
        </div>
    </div>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
</script>

</body>
</html>