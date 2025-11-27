@extends('layouts.app')

@section('title', 'Dashboard Akun Tamu')

@section('content')

@php
    // Pastikan variabel tidak null agar tidak error
    $ruangan = $ruangan ?? [];
    $proyektor = $proyektor ?? [];
@endphp

<div class="section-header" style="margin-bottom: 10px;">
    <h1>Dashboard Akun Tamu</h1>
    <p>Halo, Pengguna Tamu 👋 — berikut informasi penggunaan ruangan dan proyektor saat ini.</p>
</div>

{{-- ==== Statistik ==== --}}
<div class="dashboard-cards">

    <div class="card stat-card">
        <div class="card-icon bg-primary"><i class="fas fa-door-open"></i></div>
        <div class="card-content">
            <h3>Ruangan Dipakai</h3>
            <p class="card-value">{{ count($ruangan) }}</p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="card-icon bg-warning"><i class="fas fa-video"></i></div>
        <div class="card-content">
            <h3>Proyektor Dipakai</h3>
            <p class="card-value">{{ count($proyektor) }}</p>
        </div>
    </div>

</div>

{{-- ==== Tabel Ruangan Dipakai ==== --}}
<div class="interactive-table mt-3">
    <div class="section-header">
        <h2>Ruangan yang Sedang Dipakai</h2>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Ruangan</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($ruangan as $index => $r)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $r->namaRuangan ?? $r->nama_ruangan ?? '-' }}</td>
                    <td>
                        <span class="status-badge status-disetujui">
                            <i class="fas fa-play"></i> Dipakai
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Tidak ada ruangan yang sedang digunakan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ==== Tabel Proyektor Dipakai ==== --}}
<div class="interactive-table mt-3">
    <div class="section-header">
        <h2>Proyektor yang Sedang Dipakai</h2>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Proyektor</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($proyektor as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->namaProyektor ?? $p->nama_proyektor ?? '-' }}</td>
                    <td>
                        <span class="status-badge status-disetujui">
                            <i class="fas fa-play"></i> Dipakai
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Tidak ada proyektor yang sedang digunakan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ==== Styling ==== --}}
<style>
    /* Statistik cards */
    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 15px; /* rapikan jarak */
        margin-top: 10px;
    }

    .stat-card {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 6px 18px rgba(18, 38, 63, 0.06);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 26px rgba(18, 38, 63, 0.08);
    }

    .stat-card .card-icon {
        width: 58px;
        height: 58px;
        border-radius: 50%;
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff;
        font-size:22px;
        flex-shrink:0;
    }

    .bg-primary { background: linear-gradient(180deg,#007bff,#0056c7); }
    .bg-warning { background: linear-gradient(180deg,#ffc107,#d39e00); }

    .card-content h3 {
        margin:0;
        font-size:1rem;
        font-weight:600;
    }

    .card-value {
        margin-top:4px;
        font-size:1.5rem;
        font-weight:700;
    }

    /* Table styling */
    .data-table {
        width:100%;
        border-collapse:collapse;
        margin-top:10px;
        background:#fff;
        border-radius:12px;
        overflow:hidden;
        box-shadow:0 6px 18px rgba(18,38,63,0.06);
    }

    .data-table th {
        background:#f6f7f9;
        padding:14px;
        text-align:left;
        font-weight:700;
        border-bottom:1px solid #e5e5e5;
    }

    .data-table td {
        padding:14px;
        border-bottom:1px solid #efefef;
    }

    .data-table tr:hover {
        background:#fafafa;
    }

    .status-badge {
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:6px 12px;
        border-radius:8px;
        font-size:0.9rem;
        font-weight:700;
    }

    .status-disetujui {
        background:#d4edda;
        color:#155724;
    }

    .text-center { text-align:center; }
</style>

@endsection