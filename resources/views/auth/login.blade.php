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

                {{-- ✅ Input email atau NIM --}}
                <div class="form-group">
                    <label for="email_or_nim">Email atau NIM*</label>
                    <input type="text" id="email_or_nim" name="email_or_nim" placeholder="Masukkan email atau NIM" required value="{{ old('email_or_nim') }}">
                </div>

                <div class="form-group">
                    <label for="password">Password*</label>
                    <div class="password-input-group">
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                        <i class="fas fa-eye toggle-password" id="togglePassword" style="cursor: pointer;"></i>
                    </div>
                </div>

                {{-- ✅ Checkbox "Ingat saya" rapi --}}
                <div class="form-group remember-me">
                    <label for="remember" class="checkbox-label">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="btn-login">Masuk</button>

                {{-- ✅ Tombol login Free User (tambahan baru) --}}
                <div class="text-center mt-3">
                    <a href="{{ route('free.login') }}" class="btn-free-login">
                        <i class="fas fa-user"></i>
                        <span>Masuk sebagai Akun Tamu</span>
                    </a>
                </div>

                {{-- ✅ Tombol login Google --}}
                <div class="text-center mt-3">
                    <a href="{{ route('google.login') }}" class="btn-google-login">
                        <img src="https://developers.google.com/identity/images/g-logo.png" width="20" alt="Google Logo">
                        <span>Masuk dengan Google</span>
                    </a>
                </div>
            </form>

            <div class="login-footer">
                <p>Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
            </div>
        </div>
    </div>

    <style>
        .btn-google-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            background-color: #fff;
            border: 1px solid #ccc;
            color: #333;
            padding: 10px 0;
            margin-top: 10px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-google-login:hover {
            background-color: #f5f5f5;
            border-color: #999;
        }

        /* ✅ Tombol Free User (tambahan baru) */
        .btn-free-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            background-color: #007bff;
            border: none;
            color: #fff;
            padding: 10px 0;
            margin-top: 10px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-free-login:hover {
            background-color: #0056b3;
        }

        /* ✅ Styling checkbox agar sejajar & rapi */
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            cursor: pointer;
        }

        .checkbox-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #007bff;
            cursor: pointer;
        }
    </style>

    <script>
        // Fungsi toggle password
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
