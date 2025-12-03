<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Tamu') - Sarpras TI</title>
    
    {{-- Menggunakan CSS utama --}}
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    {{-- Google Fonts untuk tampilan lebih modern --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }

        .free-layout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .free-header {
            background: #fff;
            padding: 20px 30px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .free-header-logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .free-header-logo img {
            height: 45px;
            width: auto;
        }

        .free-header-title h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .free-header-title p {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 2px 0 0 0;
        }

        .free-content {
            flex: 1;
        }

        .free-footer {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-size: 0.875rem;
            margin-top: auto;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .free-header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .free-header-logo {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <div class="free-layout-container">
        {{-- Header Khusus Free User --}}
        <header class="free-header">
            <div class="free-header-logo">
                <img src="{{ asset('assets/images/Politeknik Negeri Tanah Laut.jpg') }}" alt="Logo Politala">
                <div class="free-header-title" style="text-align: left;">
                    <h1>Sarana & Prasarana</h1>
                    <p>Teknologi Informasi</p>
                </div>
            </div>
            
            <div class="header-actions" style="display: flex; align-items: center; gap: 15px;">
                <div class="free-user-badge">
                    <span style="background: #e0f2fe; color: #0284c7; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                        <i class="fas fa-user-clock"></i> Akun Tamu
                    </span>
                </div>
                
                <div class="auth-buttons" style="display: flex; gap: 10px;">
                    <a href="{{ route('login') }}" style="text-decoration: none; color: #4b5563; font-weight: 600; font-size: 0.9rem; padding: 8px 16px; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" style="text-decoration: none; background: #2563eb; color: white; font-weight: 600; font-size: 0.9rem; padding: 8px 16px; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                        Daftar
                    </a>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <main class="free-content">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="free-footer">
            &copy; {{ date('Y') }} Jurusan Teknologi Informasi - Politeknik Negeri Tanah Laut
        </footer>
    </div>

</body>
</html>
