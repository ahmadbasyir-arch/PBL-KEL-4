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

        {{-- ==== DETEKSI HALAMAN FREE USER ==== --}}
        @php
            $isFreePage = request()->is('free');
        @endphp
        
        {{-- ==== SIDEBAR ==== --}}
        @if (!request()->is('lengkapi-profil') && !$isFreePage)
        <div class="sidebar">
            <div class="sidebar-header"><h2>Sarpras TI</h2></div>

            {{-- ==== DATA USER ==== --}}
@php
    $authUser = Auth::user();
    $user = null;
    if ($authUser && isset($authUser->id)) {
        $user = \App\Models\User::find($authUser->id) ?? $authUser;
    } else {
        $user = $authUser;
    }

    $nama = trim((string) ($user->name ?? ''));
    if ($nama === '') {
        $nama = $user->username ?? $user->email ?? 'Pengguna';
    }

    $words = explode(' ', $nama);
    $inisialNama = '';
    foreach (array_slice($words, 0, 2) as $word) {
        if ($word !== '') {
            $inisialNama .= strtoupper(substr($word, 0, 1));
        }
    }
    if ($inisialNama === '') {
        $inisialNama = 'U';
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
                    <h3 title="{{ $nama }}">{{ $nama }}</h3>
                    <p>{{ ucfirst($user->role ?? 'user') }}</p>
                </div>
            </div>

            {{-- ==== MENU SIDEBAR SESUAI ROLE ==== --}}
<ul class="sidebar-menu">

    {{-- === DASHBOARD === --}}
    <li class="{{ Route::is('admin.dashboard') || Route::is('mahasiswa.dashboard') || Route::is('dosen.dashboard') ? 'active' : '' }}">
        <a href="
            @if(($user->role ?? '') === 'admin')
                {{ route('admin.dashboard') }}
            @elseif(($user->role ?? '') === 'mahasiswa')
                {{ route('mahasiswa.dashboard') }}
            @elseif(($user->role ?? '') === 'dosen')
                {{ route('dosen.dashboard') }}
            @else
                #
            @endif
        ">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </li>

    {{-- === Jika role Mahasiswa atau Dosen === --}}
    @if(in_array(($user->role ?? ''), ['mahasiswa', 'dosen']))
        <li class="has-submenu {{ Route::is('peminjaman.create') || request()->is('peminjaman/*') ? 'active open' : '' }}">
            <a href="#"><i class="fas fa-plus-circle"></i> Ajukan Peminjaman</a>
            <ul class="submenu" style="{{ Route::is('peminjaman.create') || request()->is('peminjaman/*') ? 'display:block;' : 'display:none;' }}">
                <li class="{{ request()->is('peminjaman/create*') && request()->query('jenis') == 'ruangan' ? 'active' : '' }}">
                    <a href="{{ route('peminjaman.create', ['jenis' => 'ruangan']) }}">Ruangan</a>
                </li>
                <li class="{{ request()->is('peminjaman/create*') && request()->query('jenis') == 'unit' ? 'active' : '' }}">
                    <a href="{{ route('peminjaman.create', ['jenis' => 'unit']) }}">Unit</a>
                </li>
            </ul>
        </li>

        <li class="{{ request()->is('riwayat*') ? 'active' : '' }}">
            <a href="{{ route('riwayat') }}">
                <i class="fas fa-history"></i> Riwayat
            </a>
        </li>

    {{-- === Jika role Admin / Staff === --}}
    @elseif(in_array($user->role ?? '', ['admin', 'staff']))
        <li class="{{ request()->is('admin/ruangan*') ? 'active' : '' }}">
            <a href="{{ route('admin.ruangan.index') }}"><i class="fas fa-door-open"></i> Data Ruangan</a>
        </li>
        <li class="{{ request()->is('admin/unit*') ? 'active' : '' }}">
            <a href="{{ route('admin.unit.index') }}"><i class="fas fa-video"></i> Data Unit</a>
        </li>
        <li class="{{ request()->is('admin/pengguna*') ? 'active' : '' }}">
            <a href="{{ route('admin.pengguna.index') }}"><i class="fas fa-users"></i> Data Pengguna</a>
        </li>
        <li class="{{ request()->is('admin/settings*') ? 'active' : '' }}">
            <a href="#"><i class="fas fa-cogs"></i> Pengaturan</a>
        </li>
    @endif
</ul>
        </div>
        @endif

        {{-- ==== MAIN CONTENT ==== --}}
        <div class="main-content">

            {{-- ==== HEADER ==== --}}
            @if (!request()->is('lengkapi-profil') && !$isFreePage)
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

                    <div class="profile-avatar" id="profileAvatar">
                        @if (!empty($user->foto_profil))
                            <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="Foto Profil" class="avatar-img">
                        @else
                            <div class="avatar-placeholder">{{ $inisialNama }}</div>
                        @endif
                    </div>

                    <div class="profile-dropdown" id="profileDropdown">
                        <a href="#"><i class="fas fa-user"></i> Lihat Profil</a>
                        <a href="{{ route('riwayat') }}"><i class="fas fa-history"></i> Aktivitas Saya</a>
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

            document.querySelectorAll('.sidebar .has-submenu > a').forEach(function(anchor) {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const parent = anchor.closest('.has-submenu');
                    parent.classList.toggle('open');
                    const submenu = parent.querySelector('.submenu');
                    if (submenu) {
                        submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
                    }
                });
            });
        });
    </script>

    {{-- ==== CSS TAMBAHAN (TIDAK DIUBAH) ==== --}}
    <style>
        .sidebar-menu li {
            transition: background 0.3s, padding-left 0.3s;
        }

        .sidebar-menu li a {
            display: block;
            color: #f1f1f1;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
        }

        .sidebar-menu li.active > a,
        .sidebar-menu li a:hover {
            background-color: #1e88e5;
            color: #fff;
            font-weight: 600;
            padding-left: 22px;
        }

        .sidebar-menu .submenu {
            margin-left: 15px;
            border-left: 2px solid #1e88e5;
            padding-left: 10px;
            margin-top: 4px;
        }

        .submenu li a {
            color: #dcdcdc;
            padding: 8px 10px;
            display: block;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .submenu li.active a,
        .submenu li a:hover {
            background-color: #1565c0;
            color: #fff;
            font-weight: 600;
            padding-left: 15px;
        }

        .sidebar-user {
            background-color: #243447;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            text-align: center;
            word-wrap: break-word;
            overflow: hidden;
        }

        .sidebar-user h3 {
            font-size: 14px;
            margin-top: 5px;
            color: #fff;
            white-space: normal;
            word-break: break-word;
            line-height: 1.3;
            max-width: 100%;
        }

        .sidebar-user p {
            color: #9bbbd4;
            font-size: 12px;
            margin: 2px 0 0 0;
        }

        .avatar-placeholder {
            width: 48px;
            height: 48px;
            background-color: #1e88e5;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-weight: bold;
            font-size: 18px;
            margin: 0 auto;
        }

        .submenu li.active a::before {
            content: "• ";
            color: #fff;
            margin-right: 5px;
        }
    </style>
</body>
</html>