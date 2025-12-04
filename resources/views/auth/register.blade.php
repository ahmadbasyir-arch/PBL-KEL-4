<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Sarpras TI</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; overflow: hidden; }
        
        .login-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
            max-width: none;
            border-radius: 0;
            box-shadow: none;
            margin: 0;
            padding: 0;
            position: relative;
            align-items: center;
            justify-content: flex-end;
            background: #000;
            padding-right: 8%;
            box-sizing: border-box;
        }

        .login-background-img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.5;
            z-index: 1;
        }

        .login-card {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            margin: 20px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .login-logos { display: flex; gap: 15px; margin-bottom: 25px; align-items: center; justify-content: center; }
        .login-logos img { height: 45px; width: auto; }

        .login-form-header { text-align: center; margin-bottom: 30px; }
        .login-form-header h2 { font-size: 1.6rem; font-weight: 700; color: #111827; margin-bottom: 8px; }
        .login-form-header p { color: #6b7280; font-size: 0.9rem; margin: 0; }

        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 0.9rem; }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.2s;
            outline: none;
            box-sizing: border-box;
            background: white;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .password-input-group { position: relative; }
        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.1s, box-shadow 0.2s;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 10px -2px rgba(37, 99, 235, 0.3);
        }

        .login-footer { margin-top: 25px; text-align: center; font-size: 0.9rem; color: #6b7280; }
        .login-footer a { color: #2563eb; text-decoration: none; font-weight: 600; }
        .login-footer a:hover { text-decoration: underline; }

        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border-left: 4px solid #ef4444;
        }

        .welcome-text {
            position: relative;
            z-index: 10;
            color: white;
            margin-right: auto;
            margin-left: 8%;
            max-width: 650px;
            animation: fadeInUp 1s ease-out;
        }

        .welcome-text h1 {
            font-size: 4rem;
            font-weight: 800;
            margin: 0 0 15px 0;
            line-height: 1.1;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
            letter-spacing: -1px;
            background: linear-gradient(to right, #ffffff, #e0e7ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-text h2 {
            font-size: 1.5rem;
            font-weight: 500;
            margin: 0 0 25px 0;
            line-height: 1.6;
            color: #e0e7ff;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
            max-width: 90%;
        }

        .welcome-text h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #fbbf24; /* Amber accent */
            text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
            display: inline-block;
            border-bottom: 2px solid #fbbf24;
            padding-bottom: 5px;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 1024px) {
            .welcome-text { display: none; }
            .login-container { justify-content: center; padding-right: 0; }
        }
    </style>
</head>

<body>
<div class="login-container">
    <img src="{{ asset('assets/images/Gedung Nadiem Makarim 1.jpg') }}" alt="Gedung Kuliah" class="login-background-img">

    <div class="welcome-text">
        <h1>SELAMAT DATANG</h1>
        <h2>Sistem Informasi Peminjaman Sarana dan Prasarana Berbasis Web untuk Prodi TI</h2>
        <h3>POLITEKNIK NEGERI TANAH LAUT</h3>
    </div>

    <div class="login-card">
        <div class="login-logos">
            <img src="{{ asset('assets/images/Politeknik Negeri Tanah Laut.jpg') }}" alt="Logo Politala">
            <img src="{{ asset('assets/images/Teknologi Informasi.jpg') }}" alt="Logo TI">
        </div>
        
        <div class="login-form-header">
            <h2>Formulir Pendaftaran</h2>
            <p>Silakan lengkapi data diri Anda untuk membuat akun baru.</p>
        </div>

        {{-- Error Validasi --}}
        @if ($errors->any())
            <div class="error-message">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- FORM REGISTER --}}
        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="namaLengkap">Nama Lengkap</label>
                <input type="text" id="namaLengkap" name="namaLengkap" required placeholder="Contoh: Budi Santoso">
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Contoh: budi123">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Contoh: budi@example.com">
            </div>

            <div class="form-group">
                <label for="nim">NIM / NIP</label>
                <input type="text" id="nim" name="nim" required placeholder="Masukkan NIM atau NIP">
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

            <div class="form-group">
                <label for="telepon">Nomor WhatsApp</label>
                <input type="text" id="telepon" name="telepon" placeholder="08xxxxxxxxxx" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-input-group">
                    <input type="password" id="password" name="password" required placeholder="Buat password aman">
                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>
            </div>

            <button type="submit" name="register" class="btn-login">Daftar Sekarang</button>
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