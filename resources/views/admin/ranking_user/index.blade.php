@extends('layouts.app')

@section('title', 'Ranking Kualitas Peminjam')

@section('content')

{{-- WELCOME BANNER (Sesuai Layout Matkul) --}}
<div class="welcome-banner">
    <div class="d-flex align-items-center gap-3 mb-2">
        <i class="fas fa-medal fa-2x"></i>
        <h1 class="m-0">Ranking Kualitas Peminjam</h1>
    </div>
    <p>Daftar Top 100 Peminjam Teladan berdasarkan analisis AI algoritma SAW (Urgensi, Perencanaan, Durasi, Kondisi).</p>
</div>

{{-- INTERACTIVE TABLE CARD (Sesuai style global app.blade.php) --}}
<div class="interactive-table mt-4">
    
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; margin: 0;">Leaderboard</h2>
        
        <div class="d-flex gap-3">
             {{-- Indikator Bobot Ringkas --}}
            <div class="d-flex gap-2 flex-wrap justify-content-end">
                <span class="badge bg-soft-success text-success border border-success px-3 py-2">Urgensi 35%</span>
                <span class="badge bg-soft-primary text-primary border border-primary px-3 py-2">Plan 25%</span>
                <span class="badge bg-soft-warning text-warning border border-warning px-3 py-2">Durasi 15%</span>
                <span class="badge bg-soft-purple text-purple border border-purple px-3 py-2">Kondisi 25%</span>
            </div>
        </div>
    </div>

    {{-- WRAPPER RESPONSIVE --}}
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th width="80" class="text-center">Rank</th>
                    <th width="250">Mahasiswa</th>
                    <th width="120" class="text-center">Total Pinjam</th>
                    <th>Detail Analisis (Grid)</th>
                    <th width="150" class="text-center">Skor Kualitas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rankings as $rank)
                    <tr style="height: 80px;"> {{-- Konsisten height --}}
                        <td class="text-center">
                            @if($rank->rank == 1)
                                <i class="fas fa-crown text-warning fa-2x"></i>
                            @elseif($rank->rank == 2)
                                <i class="fas fa-medal text-secondary fa-2x"></i>
                            @elseif($rank->rank == 3)
                                <i class="fas fa-medal text-danger fa-2x"></i> {{-- Bronze looks reddish/brown --}}
                            @else
                                <span style="font-size: 1.2rem; font-weight: 800; color: #9ca3af;">#{{ $rank->rank }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-circle">
                                    {{ substr($rank->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 1rem;">{{ $rank->user->name }}</div>
                                    <div class="text-muted" style="font-size: 0.85rem;">{{ $rank->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge-custom">
                                {{ $rank->total_pinjam }}x
                            </span>
                        </td>
                        <td>
                            {{-- GRID METRIC TETAP ADA TAPI MENGGUNAKAN STYLE LOKAL CLASS YANG RINGAN --}}
                            <div class="metric-grid">
                                <div class="m-item m-urgency">
                                    <span class="lbl">Urgensi</span>
                                    <span class="val">{{ $rank->raw_metrics['C1'] }}</span>
                                </div>
                                <div class="m-item m-plan">
                                    <span class="lbl">Plan</span>
                                    <span class="val">H-{{ $rank->raw_metrics['C2'] }}</span>
                                </div>
                                <div class="m-item m-durasi">
                                    <span class="lbl">Durasi</span>
                                    <span class="val">{{ $rank->raw_metrics['C3'] }}h</span>
                                </div>
                                <div class="m-item m-kondisi">
                                    <span class="lbl">Kondisi</span>
                                    <span class="val">{{ $rank->raw_metrics['C4'] }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="score-box">
                                {{ number_format($rank->saw_score, 3, ',', '.') }}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 text-light"></i><br>
                            Belum ada data peminjaman selesai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Helper classes mimicking bootstrap/utilities found in admin layout */
    .d-flex { display: flex; }
    .gap-2 { gap: 0.5rem; }
    .gap-3 { gap: 1rem; }
    .align-items-center { align-items: center; }
    .justify-content-between { justify-content: space-between; }
    .justify-content-end { justify-content: flex-end; }
    .flex-wrap { flex-wrap: wrap; }
    .text-center { text-align: center; }
    .m-0 { margin: 0; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .mt-4 { margin-top: 1.5rem; }
    .fw-bold { font-weight: 700; }
    .text-dark { color: #111827; }
    .text-muted { color: #6b7280; }
    .text-warning { color: #f59e0b; }
    .text-secondary { color: #94a3b8; }
    .text-danger { color: #d97706; } /* Custom bronze */
    .text-light { color: #e5e7eb; }

    /* Custom Badges */
    .bg-soft-success { background: #dcfce7; } .text-success { color: #166534; } .border-success { border-color: #bbf7d0 !important; }
    .bg-soft-primary { background: #e0f2fe; } .text-primary { color: #075985; } .border-primary { border-color: #bae6fd !important; }
    .bg-soft-warning { background: #fef3c7; } .text-warning { color: #b45309; } .border-warning { border-color: #fde68a !important; }
    .bg-soft-purple { background: #f3e8ff; } .text-purple { color: #6b21a8; } .border-purple { border-color: #d8b4fe !important; }

    /* Avatar */
    .avatar-circle {
        width: 40px; height: 40px;
        background: #f3f4f6; color: #4b5563;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }

    /* Badge Row */
    .badge-custom {
        display: inline-block;
        padding: 6px 12px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        color: #374151;
    }

    /* Metric Grid (Mini) */
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr); /* Horizontal stretch */
        gap: 8px;
        width: 100%;
        max-width: 500px; /* Don't stretch infinitely */
    }
    @media (max-width: 992px) {
        .metric-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .m-item {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 6px 8px;
        display: flex; flex-direction: column;
        text-align: center;
    }
    .lbl { font-size: 0.65rem; text-transform: uppercase; font-weight: 600; color: #6b7280; }
    .val { font-size: 0.9rem; font-weight: 700; color: #1f2937; }
    
    .m-urgency { background: #f0fdf4; border-color: #bbf7d0; }
    .m-plan { background: #eff6ff; border-color: #bfdbfe; }
    .m-durasi { background: #fff7ed; border-color: #fed7aa; }
    .m-kondisi { background: #faf5ff; border-color: #e9d5ff; }

    /* Score */
    .score-box {
        font-size: 1.4rem;
        font-weight: 800;
        color: #059669;
        /* background: #ecfdf5; padding: 10px; border-radius: 12px; */
    }
</style>

@endsection
