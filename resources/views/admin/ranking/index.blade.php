@extends('layouts.app')

@section('title', 'Prioritas Persetujuan Peminjaman')

@section('content')

{{-- WELCOME BANNER (Sesuai Layout Matkul) --}}
<div class="welcome-banner">
    <div class="d-flex align-items-center gap-3 mb-2">
        <i class="fas fa-sort-amount-up fa-2x"></i>
        <h1 class="m-0">Prioritas Persetujuan Peminjaman</h1>
    </div>
    <p>Sistem Pendukung Keputusan Approval otomatis mengurutkan permintaan peminjaman pending berdasarkan tingkat urgensi, perencanaan, dan reputasi peminjam. Bantu Admin mengambil keputusan lebih cepat & objektif.</p>
</div>

{{-- CONFIGURATION CARD --}}
<div class="interactive-table mt-4 p-4 mb-4">
    <form action="{{ route('admin.ranking.updateWeights') }}" method="POST">
        @csrf
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-soft-primary">
                    <i class="fas fa-sliders-h text-primary"></i>
                </div>
                <div>
                    <h5 class="fw-bold m-0 text-dark">Konfigurasi Bobot SAW</h5>
                    <p class="m-0 text-muted small">Sesuaikan prioritas sistem dalam menilai pengajuan.</p>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                <i class="fas fa-save"></i> Simpan Bobot
            </button>
        </div>

        <div class="config-grid">
            @php
                $options = [];
                for($i=0; $i<=100; $i+=5) {
                    $val = number_format($i/100, 2);
                    $options["$val"] = "$i%";
                }
            @endphp

            {{-- C1 --}}
            <div class="config-item">
                <div class="d-flex justify-content-between mb-2">
                    <label class="fw-bold small text-dark">C1: Urgensi</label>
                    <span class="badge bg-soft-success text-success">Benefit</span>
                </div>
                <select name="saw_c1" class="form-select form-select-sm mb-2">
                    @foreach($options as $val => $label)
                        <option value="{{ $val }}" {{ (string)$bobot['C1'] === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <small class="text-muted d-block" style="line-height:1.2;">Prioritas lebih tinggi untuk sidang/ujian.</small>
            </div>

            {{-- C2 --}}
            <div class="config-item">
                <div class="d-flex justify-content-between mb-2">
                    <label class="fw-bold small text-dark">C2: Perencanaan</label>
                    <span class="badge bg-soft-success text-success">Benefit</span>
                </div>
                <select name="saw_c2" class="form-select form-select-sm mb-2">
                    @foreach($options as $val => $label)
                        <option value="{{ $val }}" {{ (string)$bobot['C2'] === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <small class="text-muted d-block" style="line-height:1.2;">Booking jauh hari diberi nilai plus.</small>
            </div>

            {{-- C3 --}}
            <div class="config-item">
                <div class="d-flex justify-content-between mb-2">
                    <label class="fw-bold small text-dark">C3: Reputasi</label>
                    <span class="badge bg-soft-success text-success">Benefit</span>
                </div>
                <select name="saw_c3" class="form-select form-select-sm mb-2">
                    @foreach($options as $val => $label)
                        <option value="{{ $val }}" {{ (string)$bobot['C3'] === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <small class="text-muted d-block" style="line-height:1.2;">Track record peminjam yang baik.</small>
            </div>

            {{-- C4 --}}
            <div class="config-item">
                <div class="d-flex justify-content-between mb-2">
                    <label class="fw-bold small text-dark">C4: Durasi</label>
                    <span class="badge bg-soft-danger text-danger">Cost</span>
                </div>
                <select name="saw_c4" class="form-select form-select-sm mb-2">
                    @foreach($options as $val => $label)
                        <option value="{{ $val }}" {{ (string)$bobot['C4'] === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <small class="text-muted d-block" style="line-height:1.2;">Durasi yang efisien lebih diutamakan.</small>
            </div>
        </div>
    </form>
</div>

{{-- INTERACTIVE TABLE CARD --}}
<div class="interactive-table mt-4">
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; margin: 0;">Antrian Prioritas (Pending)</h2>
        
        </div>
    </div>

    {{-- WRAPPER RESPONSIVE --}}
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th width="60" class="text-center">#</th>
                    <th>Peminjam</th>
                    <th>Item & Waktu</th>
                    <th class="text-center">SAW Score</th>
                    <th>Detail Analisis Kriteria</th>
                    <th width="100" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rankings as $rank)
                    <tr style="height: 80px;">
                        <td class="text-center">
                            <div class="rank-circle {{ $rank->rank <= 3 ? 'top-rank' : '' }}">
                                {{ $rank->rank }}
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $rank->data->user->name ?? 'User' }}</div>
                            <small class="text-muted d-block">{{ $rank->data->user->role == 'dosen' ? 'Dosen' : 'Mahasiswa' }}</small>
                            <span class="badge bg-soft-warning text-warning mt-1">Pending</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark mb-1">
                                {{ $rank->data->ruangan->namaRuangan ?? ($rank->data->unit->namaUnit ?? 'Item') }}
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                                <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($rank->data->tanggalPinjam)->isoFormat('D MMM Y') }}
                            </div>
                             <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="far fa-clock"></i> {{ substr($rank->data->jamMulai, 0, 5) }} - {{ substr($rank->data->jamSelesai, 0, 5) }}
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="score-main">{{ $rank->saw_score }}</div>
                        </td>
                        <td>
                            <div class="metric-grid-compact">
                                <div class="m-box">
                                    <span class="lbl">Urgensi (C1)</span>
                                    <span class="val">{{ $rank->raw_metrics['C1'] }}</span>
                                </div>
                                <div class="m-box">
                                    <span class="lbl">Plan (C2)</span>
                                    <span class="val">H-{{ $rank->raw_metrics['C2'] }}</span>
                                </div>
                                <div class="m-box">
                                    <span class="lbl">Reputasi (C3)</span>
                                    <span class="val">{{ $rank->raw_metrics['C3'] }}</span>
                                </div>
                                <div class="m-box cost">
                                    <span class="lbl">Durasi (C4)</span>
                                    <span class="val">{{ $rank->raw_metrics['C4'] }}J</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.peminjaman.show', $rank->data->id) }}" class="btn btn-sm btn-primary">
                                Detail <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success opacity-50"></i><br>
                            Tidak ada antrian pending. Semua beres!
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
    .text-end { text-align: right; }
    .m-0 { margin: 0; }
    .mb-1 { margin-bottom: 0.25rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .mt-4 { margin-top: 1.5rem; }
    .mt-1 { margin-top: 0.25rem; }
    .pb-3 { padding-bottom: 1rem; }
    .p-4 { padding: 1.5rem; }
    .fw-bold { font-weight: 700; }
    .text-dark { color: #111827; }
    .text-muted { color: #6b7280; }
    .small { font-size: 0.85rem; }
    .border-bottom { border-bottom: 1px solid #e5e7eb; }
    .ms-1 { margin-left: 0.25rem; }

    /* Custom Badges & Colors */
    .bg-soft-success { background: #dcfce7; } .text-success { color: #166534; }
    .bg-soft-primary { background: #e0f2fe; } .text-primary { color: #075985; }
    .bg-soft-warning { background: #fef3c7; } .text-warning { color: #b45309; }
    .bg-soft-danger  { background: #fee2e2; } .text-danger  { color: #991b1b; }

    /* Config Grid */
    .config-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    @media (max-width: 992px) {
        .config-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .config-grid { grid-template-columns: 1fr; }
    }
    
    .config-item {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 15px;
        transition: all 0.2s;
    }
    .config-item:hover { border-color: #d1d5db; background: #fff; }

    .icon-box {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
    }

    /* Rank Circle */
    .rank-circle {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: #f3f4f6; color: #6b7280;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto;
        border: 2px solid #e5e7eb;
    }
    .rank-circle.top-rank {
        background: #ecfccb; color: #4d7c0f; border-color: #84cc16;
    }

    /* Score */
    .score-main { font-size: 1.25rem; font-weight: 800; color: #0ea5e9; }

    /* Metric Grid Compact */
    .metric-grid-compact {
        display: grid; grid-template-columns: repeat(2, 1fr);
        gap: 6px; width: 100%; max-width: 300px;
    }
    .m-box {
        background: #f9fafb; border: 1px solid #e5e7eb;
        border-radius: 6px; padding: 4px 8px;
        font-size: 0.75rem;
        display: flex; justify-content: space-between; align-items: center;
    }
    .m-box.cost .val { color: #dc2626; }
    .m-box .lbl { color: #6b7280; font-weight: 600; font-size: 0.65rem; text-transform: uppercase; }
    .m-box .val { color: #111827; font-weight: 700; font-size: 0.8rem; }
</style>

@endsection
