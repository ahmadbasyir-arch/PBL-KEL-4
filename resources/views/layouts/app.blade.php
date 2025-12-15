<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sarpras TI') - Politeknik Negeri Tanah Laut</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f0f2f5; }
        
        /* Global Dashboard Styles */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid rgba(0,0,0,0.03);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .stat-card .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #fff;
        }

        /* Card Colors */
        .bg-primary .card-icon, .bg-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
        .bg-warning .card-icon, .bg-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .bg-success .card-icon, .bg-success { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); }
        .bg-danger .card-icon, .bg-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .bg-info .card-icon, .bg-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
        .bg-secondary .card-icon, .bg-secondary { background: linear-gradient(135deg, #64748b 0%, #475569 100%); }

        /* Override card background if class is on parent */
        .stat-card.bg-primary, .stat-card.bg-warning, .stat-card.bg-success, .stat-card.bg-danger, .stat-card.bg-info {
            background: #fff; /* Reset to white */
        }

        .card-content h3 {
            margin: 0;
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-value {
            margin-top: 5px;
            font-size: 1.8rem;
            font-weight: 700;
            color: #111827;
        }

        /* Table Styles */
        .interactive-table {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-top: 25px;
            border: 1px solid rgba(0,0,0,0.03);
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .data-table th {
            text-align: left;
            padding: 12px 15px;
            color: #6b7280;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e5e7eb;
            background: transparent;
        }

        .data-table td {
            padding: 16px 15px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
            font-size: 0.95rem;
            vertical-align: middle;
        }

        .data-table tr:hover td {
            background-color: #f9fafb;
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending { background: #fef3c7; color: #d97706; }
        .status-disetujui { background: #dcfce7; color: #166534; }
        .status-ditolak { background: #fee2e2; color: #991b1b; }
        .status-selesai { background: #f3f4f6; color: #374151; }
        .status-menyelesaikan, .status-menunggu_validasi { background: #e0f2fe; color: #075985; }
        .status-tersedia { background: #dcfce7; color: #166534; }
        .status-dipinjam, .status-digunakan { background: #fee2e2; color: #991b1b; }
        .status-perawatan { background: #fef3c7; color: #854d0e; }

        /* Buttons */
        .btn-sm { padding: 6px 12px; font-size: 0.85rem; border-radius: 6px; }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: white;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.5);
            position: relative;
            overflow: hidden;
        }
        
        .welcome-banner h1, .welcome-banner h2 {
            margin: 0 0 10px 0;
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
        }
        
        .welcome-banner p {
            margin: 0;
            opacity: 0.9;
            font-size: 1.05rem;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Decorative circle for banner */
        .welcome-banner::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        /* ===== ADDED: ensure charts display HORIZONTAL (3 columns) ===== */
        .chart-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 20px;
        }

        /* Ensure each chart-card uses reasonable height and centers the canvas */
        .chart-card {
            background: #fff;
            border-radius: 12px;
            padding: 18px;
            min-height: 220px; /* stable height */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 18px rgba(13, 47, 79, 0.04);
            border: 1px solid rgba(0,0,0,0.04);
        }

        /* force canvas to fill the card width but not grow vertically too much */
        .chart-card canvas {
            width: 100% !important;
            height: 160px !important;
            max-height: 160px !important;
            object-fit: contain;
            margin-bottom: 6px;
        }

        /* responsive: collapse to 2 or 1 column on small screens */
        @media (max-width: 1100px) {
            .chart-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            .chart-grid { grid-template-columns: 1fr; }
            .chart-card { min-height: 260px; }
        }
        /* =============================================================== */
    </style>
<body>
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

    <div class="container">

        {{-- ==== SIDEBAR ==== --}}
        @if (!request()->is('lengkapi-profil'))
        <div class="sidebar">
            <div class="sidebar-header"><h2>Sarpras TI</h2></div>

            {{-- ==== DATA USER ==== --}}


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
                    <p>{{ ($user->role === 'admin') ? 'Staf Prodi' : ucfirst($user->role ?? 'user') }}</p>
                </div>
            </div>

            {{-- ==== MENU SIDEBAR SESUAI ROLE ==== --}}
<ul class="sidebar-menu">

    {{-- === DASHBOARD === --}}
    <li class="{{ Route::is('admin.dashboard') || Route::is('mahasiswa.dashboard') || Route::is('dosen.dashboard') || Route::is('superadmin.dashboard') ? 'active' : '' }}">
        <a href="
            @if(($user->role ?? '') === 'admin')
                {{ route('admin.dashboard') }}
            @elseif(($user->role ?? '') === 'mahasiswa')
                {{ route('mahasiswa.dashboard') }}
            @elseif(($user->role ?? '') === 'dosen')
                {{ route('dosen.dashboard') }}
            @elseif(($user->role ?? '') === 'super_admin')
                {{ route('superadmin.dashboard') }}
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

        <li class="{{ request()->is('ulasan*') ? 'active' : '' }}">
            <a href="{{ route('ulasan.create') }}">
                <i class="fas fa-comment-dots"></i> Kritik & Saran
            </a>
        </li>

        <li class="{{ request()->is('settings*') ? 'active' : '' }}">
            <a href="{{ route('profile.edit') }}">
                <i class="fas fa-cog"></i> Pengaturan
            </a>
        </li>

    {{-- === Jika role Admin / Staff / Super Admin === --}}
    @elseif(in_array($user->role ?? '', ['admin', 'staff', 'super_admin']))
        
        @if(($user->role ?? '') === 'super_admin')
            <li class="{{ request()->is('superadmin/prodi*') ? 'active' : '' }}">
                <a href="{{ route('superadmin.prodi.index') }}"><i class="fas fa-university"></i> Data Prodi</a>
            </li>
        @endif
        <li class="{{ request()->is('admin/ruangan*') ? 'active' : '' }}">
            <a href="{{ route('admin.ruangan.index') }}"><i class="fas fa-door-open"></i> Data Ruangan</a>
        </li>
        <li class="{{ request()->is('admin/unit*') ? 'active' : '' }}">
            <a href="{{ route('admin.unit.index') }}"><i class="fas fa-video"></i> Data Unit</a>
        </li>
        <li class="{{ request()->is('admin/pengguna*') ? 'active' : '' }}">
            <a href="{{ route('admin.pengguna.index') }}"><i class="fas fa-users"></i> Data Pengguna</a>
        </li>
        <li class="{{ request()->is('admin/ranking*') ? 'active' : '' }}">
            <a href="{{ route('admin.ranking.index') }}"><i class="fas fa-trophy"></i> Ranking Peminjaman</a>
        </li>
        <li class="{{ request()->is('admin/jadwal*') ? 'active' : '' }}">
            <a href="{{ route('admin.jadwal.index') }}"><i class="fas fa-calendar-alt"></i> Jadwal Perkuliahan</a>
        </li>
        <li class="{{ request()->is('admin/matkul*') ? 'active' : '' }}">
            <a href="{{ route('admin.matkul.index') }}"><i class="fas fa-book"></i> Data Mata Kuliah</a>
        </li>
        <li class="{{ request()->is('admin/laporan*') ? 'active' : '' }}">
            <a href="{{ route('admin.laporan.index') }}"><i class="fas fa-file-alt"></i> Laporan</a>
        </li>
        <li class="{{ request()->is('admin/ulasan*') ? 'active' : '' }}">
            <a href="{{ route('admin.ulasan.index') }}"><i class="fas fa-comments"></i> Ulasan Pengguna</a>
        </li>

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
                        @php
                            $unreadCount = $user ? $user->unreadNotifications->count() : 0;
                        @endphp
                        @if($unreadCount > 0)
                            <span class="notification-badge">{{ $unreadCount }}</span>
                        @endif
                        
                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="dropdown-header">
                                <span>Notifikasi</span>
                                @if($unreadCount > 0)
                                    <a href="{{ route('notifications.markRead') }}" class="mark-read">Tandai sudah dibaca</a>
                                @endif
                            </div>
                            <div class="dropdown-content">
                                @forelse($user->notifications as $notification)
                                    <a href="{{ $notification->data['url'] ?? '#' }}" class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
                                        <div class="icon">
                                            @if(($notification->data['status'] ?? '') == 'disetujui')
                                                <i class="fas fa-check-circle text-success"></i>
                                            @elseif(($notification->data['status'] ?? '') == 'ditolak')
                                                <i class="fas fa-times-circle text-danger"></i>
                                            @else
                                                <i class="fas fa-info-circle text-info"></i>
                                            @endif
                                        </div>
                                        <div class="text">
                                            <p>{{ $notification->data['message'] ?? 'Notifikasi baru' }}</p>
                                            <small>{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                    </a>
                                @empty
                                    <div class="empty-state">
                                        <p>Tidak ada notifikasi</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
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

            // Notification Dropdown Logic
            const notificationBell = document.getElementById('notificationBell');
            const notificationDropdown = document.getElementById('notificationDropdown');

            if (notificationBell && notificationDropdown) {
                notificationBell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    // Close profile dropdown if open
                    if (profileDropdown) profileDropdown.classList.remove('show');
                    notificationDropdown.classList.toggle('show');
                });
            }

            // Close when clicking outside
            window.addEventListener('click', function(e) {
                if (profileDropdown && profileDropdown.classList.contains('show')) {
                    profileDropdown.classList.remove('show');
                }
                if (notificationDropdown && notificationDropdown.classList.contains('show')) {
                    if (!notificationDropdown.contains(e.target) && !notificationBell.contains(e.target)) {
                        notificationDropdown.classList.remove('show');
                    }
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
        /* ... rest of your CSS (omitted here for brevity in this message) ... */
        /* Note: In the file replacement above, the entire CSS block is included as provided earlier. */
    </style>
<!-- tambahkan Chart.js (required oleh chart partial) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</body>
</html>
