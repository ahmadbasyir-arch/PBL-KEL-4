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
        <div class="sidebar">
            <div class="sidebar-header"><h2>Sarpras TI</h2></div>
            <div class="sidebar-user">
                <div class="user-avatar"><i class="fas fa-user-graduate"></i></div>
                <div class="user-info">
                    <h3>{{ Auth::user()->namaLengkap }}</h3>
                    <p>{{ ucfirst(Auth::user()->role) }}</p>
                </div>
            </div>

            {{-- ==== MENU SIDEBAR SESUAI ROLE ==== --}}
            <ul class="sidebar-menu">
                <li class="{{ Route::is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
                </li>

                {{-- === Jika role Mahasiswa === --}}
                @if(Auth::user()->role === 'mahasiswa')
                    <li class="has-submenu {{ Route::is('peminjaman.create') ? 'active open' : '' }}">
                        <a href="#"><i class="fas fa-plus-circle"></i> Ajukan Peminjaman</a>
                        <ul class="submenu" style="display: block;">
                            <li><a href="{{ route('peminjaman.create', ['jenis' => 'ruangan']) }}"> Ruangan</a></li>
                            <li><a href="{{ route('peminjaman.create', ['jenis' => 'unit']) }}"> Unit</a></li>
                        </ul>
                    </li>
                    <li><a href="#"><i class="fas fa-history"></i> Riwayat</a></li>

                {{-- === Jika role Admin / Staff === --}}
                @elseif(in_array(Auth::user()->role, ['admin', 'staff']))
                    <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-chart-line"></i> Kelola Peminjaman</a></li>
                    <li><a href="#"><i class="fas fa-door-open"></i> Data Ruangan</a></li>
                    <li><a href="#"><i class="fas fa-users"></i> Data Pengguna</a></li>
                    <li><a href="#"><i class="fas fa-cogs"></i> Pengaturan</a></li>

                {{-- === Jika role Dosen === --}}
                @elseif(Auth::user()->role === 'dosen')
                    <li><a href="#"><i class="fas fa-clipboard-list"></i> Daftar Peminjaman</a></li>
                    <li><a href="#"><i class="fas fa-history"></i> Riwayat</a></li>
                @endif
            </ul>
        </div>

        {{-- ==== MAIN CONTENT ==== --}}
        <div class="main-content">
            {{-- ==== HEADER ==== --}}
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
                    
                    @php
                        $nama = Auth::user()->namaLengkap;
                        $words = explode(' ', $nama);
                        $inisialNama = '';
                        foreach (array_slice($words, 0, 2) as $word) {
                            $inisialNama .= strtoupper(substr($word, 0, 1));
                        }
                    @endphp
                    <div class="profile-avatar" id="profileAvatar">
                        {{ $inisialNama }}
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
        });
    </script>
</body>
</html>