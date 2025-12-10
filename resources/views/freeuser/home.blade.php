@extends('layouts.free')

@section('title', 'Dashboard Akun Tamu')

@section('content')

@php
    $ruangan = $ruangan ?? [];
    $proyektor = $proyektor ?? [];
@endphp

<div class="welcome-banner">
    <h2>Selamat Datang, Tamu! 👋</h2>
    <p>Berikut adalah informasi terkini mengenai penggunaan fasilitas ruangan dan proyektor di Jurusan Teknologi Informasi.</p>
</div>

{{-- ==== Statistik ==== --}}
<div class="stats-grid">
    <div class="stat-card blue-card">
        <div class="icon-wrapper">
            <i class="fas fa-door-open"></i>
        </div>
        <div class="stat-info">
            <h3>Ruangan Dipakai</h3>
            <div class="value">{{ $ruanganDipakai }}</div>
        </div>
    </div>

    <div class="stat-card orange-card">
        <div class="icon-wrapper">
            <i class="fas fa-video"></i>
        </div>
        <div class="stat-info">
            <h3>Proyektor Dipakai</h3>
            <div class="value">{{ $unitDipakai }}</div>
        </div>
    </div>
</div>

<div class="filter-section" style="margin-bottom: 20px; display: flex; gap: 10px; align-items: center;">
    <span style="font-weight: 600; color: #374151;">Filter Status:</span>
    <button type="button" class="btn-filter active" data-filter="all">Semua</button>
    <button type="button" class="btn-filter" data-filter="tersedia">Tersedia</button>
    <button type="button" class="btn-filter" data-filter="dipakai">Sedang Digunakan</button>
</div>

<div class="content-grid">
    {{-- ==== Tabel Info Ruangan ==== --}}
    <div class="data-section">
        <div class="section-title">
            <i class="fas fa-door-open" style="color: #10b981;"></i> Info Ruangan Hari Ini
        </div>
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th>Nama Ruangan</th>
                        <th width="25%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($allRuangan as $index => $r)
                        <tr class="item-row" data-status="{{ $r->status }}">
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $r->namaRuangan }}</td>
                            <td>
                                @if($r->status == 'tersedia')
                                    <span class="status-pill" style="background: #d1fae5; color: #047857;">
                                        <i class="fas fa-check-circle"></i> Tersedia
                                    </span>
                                @else
                                    <span class="status-pill active" style="background: #fee2e2; color: #b91c1c;">
                                        <i class="fas fa-circle-play"></i> Sedang Digunakan
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty-state">
                                <i class="fas fa-exclamation-circle"></i>
                                <p>Data ruangan tidak tersedia.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ==== Tabel Info Proyektor ==== --}}
    <div class="data-section">
        <div class="section-title">
            <i class="fas fa-video" style="color: #f59e0b;"></i> Info Proyektor Hari Ini
        </div>
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th>Nama Proyektor</th>
                        <th width="25%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($allUnits as $index => $u)
                        <tr class="item-row" data-status="{{ $u->status }}">
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $u->namaUnit }} 
                                @if($u->kodeUnit) 
                                    <span style="font-weight:normal; color:#666; font-size:0.85em;">({{ $u->kodeUnit }})</span>
                                @endif
                            </td>
                            <td>
                                @if($u->status == 'tersedia')
                                    <span class="status-pill" style="background: #d1fae5; color: #047857;">
                                        <i class="fas fa-check-circle"></i> Tersedia
                                    </span>
                                @else
                                    <span class="status-pill active" style="background: #fee2e2; color: #b91c1c;">
                                        <i class="fas fa-circle-play"></i> Sedang Digunakan
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty-state">
                                <i class="fas fa-exclamation-circle"></i>
                                <p>Data proyektor tidak tersedia.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        color: white;
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.5);
    }
    
    .welcome-banner h2 {
        margin: 0 0 10px 0;
        font-size: 1.8rem;
        font-weight: 700;
    }
    
    .welcome-banner p {
        margin: 0;
        opacity: 0.9;
        font-size: 1.05rem;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid rgba(0,0,0,0.03);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .icon-wrapper {
        width: 70px;
        height: 70px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
    }

    .blue-card .icon-wrapper {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
    }

    .orange-card .icon-wrapper {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 10px rgba(217, 119, 6, 0.3);
    }

    .stat-info h3 {
        margin: 0;
        font-size: 0.95rem;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-info .value {
        font-size: 2.2rem;
        font-weight: 800;
        color: #111827;
        line-height: 1.2;
    }

    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 30px;
    }

    .data-section {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0,0,0,0.03);
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f3f4f6;
    }

    .section-title i {
        color: #4f46e5;
    }

    /* Modern Table */
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .modern-table th {
        text-align: left;
        padding: 12px 15px;
        color: #6b7280;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e5e7eb;
    }

    .modern-table td {
        padding: 16px 15px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        font-size: 0.95rem;
    }

    .modern-table tr:last-child td {
        border-bottom: none;
    }

    .fw-bold {
        font-weight: 600;
        color: #111827;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-pill.active {
        background: #dcfce7;
        color: #166534;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px !important;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 10px;
        color: #e5e7eb;
    }

    .empty-state p {
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .welcome-banner {
            padding: 20px;
        }
        
        .welcome-banner h2 {
            font-size: 1.5rem;
        }
    }

    /* Filter Buttons */
    .btn-filter {
        padding: 8px 16px;
        border: 1px solid #e5e7eb;
        background: white;
        color: #374151;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-filter:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    .btn-filter.active {
        background: #4f46e5;
        color: white;
        border-color: #4f46e5;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.btn-filter');
    const rows = document.querySelectorAll('.item-row');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active class from all buttons
            filterButtons.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            btn.classList.add('active');

            const filterValue = btn.getAttribute('data-filter');

            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                if (filterValue === 'all' || status === filterValue) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endsection