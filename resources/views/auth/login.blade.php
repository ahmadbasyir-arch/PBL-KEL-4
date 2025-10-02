<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sarpras TI</title>
    {{-- Menggunakan helper asset() untuk memanggil file CSS dari folder public --}}
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-left">
            <img src="{{ asset('assets/images/Gedung Nadiem Makarim 1.jpg') }}" alt="Gedung" class="login-background-img">
            <div class="welcome-text">
                <h1>SELAMAT DATANG</h1>
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
                <h2>Masuk dan Verifikasi</h2>
                <p>Nikmati kemudahan sistem autentikasi tunggal untuk mengakses semua layanan.</p>
            </div>
            
            <form action="{{ route('login') }}" method="POST" class="login-form">
                @csrf {{-- Token keamanan wajib di Laravel --}}

                {{-- Menampilkan pesan error validasi --}}
                @if ($errors->any())
                    <div class="error-message">
                        @foreach ($errors->all() as $error)
                            <span>{{ $error }}</span>
                        @endforeach
                    </div>
                @endif
                
                {{-- Menampilkan pesan sukses (setelah registrasi) --}}
                @if (session('success'))
                    <div class="success-message">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="form-group">
                    <label for="username">Email/NIM/Username*</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan email/NIM/Username" required value="{{ old('username') }}">
                </div>
                <div class="form-group">
                    <label for="password">Password*</label>
                    <div class="password-input-group">
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn-login">Masuk</button>
            </form>
            <div class="login-footer">
                <p>Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
            </div>
        </div>
    </div>

    <script>
        // Script Anda bisa diletakkan di sini atau di file JS terpisah
    </script>
</body>
</html>