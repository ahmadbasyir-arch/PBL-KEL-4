<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sarpras TI') - Politeknik Negeri Tanah Laut</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <div class="container">
        
        {{-- ==== SIDEBAR ==== --}}
        @if (!request()->is('lengkapi-profil'))
        <div class="sidebar">
            <div class="sidebar-header"><h2>Sarpras TI</h2></div>

            {{-- ==== DATA USER ==== --}}
@php
    $user = Auth::user();
    $nama = $user->name ?? $user->username ?? 'Pengguna';
    $words = explode(' ', $nama);
    $inisialNama = '';
    foreach (array_slice($words, 0, 2) as $word) {
        $inisialNama .= strtoupper(substr($word, 0, 1));
    }
@endphp


            <div class="sidebar-user">
                <div class="user-avatar">
                    @if (!empty($user->foto_profil))
                        <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="Foto Profil" class="avatar-img">
                    @else
                        <div class="avatar-placeholder">{{ $inisialNama }}</div>
                    @endif
                </div>
                <div class="user-info">
                    <h3>{{ $nama }}</h3>
                    <p>{{ ucfirst($user->role) }}</p>
                </div>
            </div>

            {{-- ==== MENU SIDEBAR SESUAI ROLE ==== --}}
            <ul class="sidebar-menu">
                <li class="{{ Route::is('dashboard') || Route::is('admin.dashboard') || Route::is('dashboard.dosen') ? 'active' : '' }}">
                    <a href="{{
                        Auth::user()->role === 'admin' ? route('admin.dashboard') :
                        (Auth::user()->role === 'mahasiswa' ? route('dashboard') :
                        (Auth::user()->role === 'dosen' ? route('dashboard.dosen') : '#'))
                    }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>

                {{-- === Jika role Mahasiswa === --}}
                @if($user->role === 'mahasiswa')
                    <li class="has-submenu {{ Route::is('peminjaman.create') || request()->is('peminjaman/*') ? 'active open' : '' }}">
                        <a href="#"><i class="fas fa-plus-circle"></i> Ajukan Peminjaman</a>
                        <ul class="submenu" style="{{ Route::is('peminjaman.create') || request()->is('peminjaman/*') ? 'display:block;' : 'display:none;' }}">
                            <li class="{{ request()->is('peminjaman/create*') && request()->query('jenis') == 'ruangan' ? 'active' : '' }}"><a href="{{ route('peminjaman.create', ['jenis' => 'ruangan']) }}"> Ruangan</a></li>
                            <li class="{{ request()->is('peminjaman/create*') && request()->query('jenis') == 'unit' ? 'active' : '' }}"><a href="{{ route('peminjaman.create', ['jenis' => 'unit']) }}"> Unit</a></li>
                        </ul>
                    </li>
                    <li class="{{ request()->is('riwayat*') ? 'active' : '' }}"><a href="#"><i class="fas fa-history"></i> Riwayat</a></li>

                {{-- === Jika role Admin / Staff === --}}
                @elseif(in_array($user->role, ['admin', 'staff']))
                    <li class="{{ request()->is('ruangan*') ? 'active' : '' }}"><a href="#"><i class="fas fa-door-open"></i> Data Ruangan</a></li>
                    <li class="{{ request()->is('users*') ? 'active' : '' }}"><a href="#"><i class="fas fa-users"></i> Data Pengguna</a></li>
                    <li class="{{ request()->is('settings*') ? 'active' : '' }}"><a href="#"><i class="fas fa-cogs"></i> Pengaturan</a></li>

                {{-- === Jika role Dosen === --}}
                @elseif($user->role === 'dosen')
                    <li class="{{ request()->is('dosen/peminjaman*') ? 'active' : '' }}"><a href="#"><i class="fas fa-clipboard-list"></i> Daftar Peminjaman</a></li>
                    <li class="{{ request()->is('riwayat*') ? 'active' : '' }}"><a href="#"><i class="fas fa-history"></i> Riwayat</a></li>
                @endif
            </ul>
        </div>
        @endif

        {{-- ==== MAIN CONTENT ==== --}}
        <div class="main-content">
            {{-- ==== HEADER ==== --}}
            @if (!request()->is('lengkapi-profil'))
            <div class="header header-dark">
                <div class="header-left">
                    <div class="logos">
                        <img src="{{ asset('assets/images/Politeknik Negeri Tanah Laut.jpg') }}" alt="Logo Politala">
                        <img src="{{ asset('assets/images/Teknologi Informasi.jpg') }}" alt="Logo TI">
                    </div>
                    <div class="title-container">
                        <h2>Sarana & Prasarana</h2>
                        <p>Teknologi Informasi</p>
                    </div>
                </div>

                <div class="header-right">
                    <div class="notification-bell icon-link" id="notificationBell">
                        <i class="fas fa-bell"></i>
                    </div>

                    {{-- === FOTO PROFIL DI HEADER === --}}
                    <div class="profile-avatar" id="profileAvatar">
                        @if (!empty($user->foto_profil))
                            <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="Foto Profil" class="avatar-img">
                        @else
                            <div class="avatar-placeholder">{{ $inisialNama }}</div>
                        @endif
                    </div>

                    {{-- Dropdown Profil & Logout --}}
                    <div class="profile-dropdown" id="profileDropdown">
                        <a href="#"><i class="fas fa-user"></i> Lihat Profil</a>
                        <a href="#"><i class="fas fa-history"></i> Aktivitas Saya</a>
                        <div class="divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Keluar
                            </a>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="dashboard-content-area">
                @yield('content')
            </div>
        </div>
    </div>
    
    {{-- ==== SCRIPT ==== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileAvatar = document.getElementById('profileAvatar');
            const profileDropdown = document.getElementById('profileDropdown');

            if (profileAvatar && profileDropdown) {
                profileAvatar.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('show');
                });
            }
            window.addEventListener('click', function(e) {
                if (profileDropdown && profileDropdown.classList.contains('show')) {
                    profileDropdown.classList.remove('show');
                }
            });

            // toggle submenu open/close when clicking parent (for better UX)
            document.querySelectorAll('.sidebar .has-submenu > a').forEach(function(anchor) {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const parent = anchor.closest('.has-submenu');
                    parent.classList.toggle('open');
                    const submenu = parent.querySelector('.submenu');
                    if (submenu) {
                        if (submenu.style.display === 'block') {
                            submenu.style.display = 'none';
                        } else {
                            submenu.style.display = 'block';
                        }
                    }
                });
            });
        });
    </script>

    {{-- ==== CSS TAMBAHAN UNTUK AVATAR & SIDEBAR AKTIF ==== --}}
    <style>
        .sidebar-user {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255, 255, 255, 0.05);
    padding: 10px;
    border-radius: 10px;
    overflow: hidden;
}

.avatar-img,
.avatar-placeholder {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    flex-shrink: 0;
    object-fit: cover;
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-info {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.user-info h3 {
    color: #fff;
    font-size: 15px;
    margin: 0;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis; /* Nama panjang otomatis dipotong dengan "..." */
    max-width: 140px; /* Biar gak keluar batas */
}

.user-info p {
    color: #cfcfcf;
    font-size: 13px;
    margin: 0;
}

        /* Sidebar basic */
        .sidebar { width: 260px; }
        .sidebar-menu { list-style: none; padding: 0; margin: 20px 0; }
        .sidebar-menu li { margin-bottom: 6px; }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
        }

        /* Active state */
        .sidebar-menu li.active > a {
            background-color: #007bff;
            color: #fff;
            font-weight: 700;
        }
        .sidebar-menu li.active > a i {
            color: #fff;
        }

        .sidebar-menu li a i { width: 20px; text-align: center; color: #6c757d; }

        .sidebar-menu li a:hover {
            background: rgba(0,0,0,0.04);
            color: #000;
        }

        /* Submenu */
        .submenu { list-style: none; padding-left: 10px; margin-top: 8px; }
        .submenu li a { padding: 8px 12px; font-size: 0.95rem; display:block; border-radius:6px; }
        .has-submenu.open > a { background: rgba(0,0,0,0.04); }
        .submenu li.active > a { background-color: rgba(0,123,255,0.1); font-weight:600; }

        /* Small responsive fix */
        @media (max-width: 900px) {
            .sidebar { width: 100%; position: relative; }
            .main-content { margin-left: 0; }
        }
    </style>
</body>
</html>