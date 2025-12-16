@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi')

@section('content')

{{-- WELCOME BANNER --}}
<div class="welcome-banner">
    <div class="d-flex align-items-center gap-3 mb-2">
        <i class="fas fa-file-invoice fa-2x"></i>
        <h1 class="m-0">Laporan & Rekapitulasi</h1>
    </div>
    <p>Pusat unduh laporan: Data peminjaman, riwayat, ranking approval, dan ulasan pengguna.</p>
</div>

{{-- DOWNLOAD CENTER CARD --}}
<div class="interactive-table mt-4" style="padding: 30px;">
    
    {{-- Modern Header --}}
    <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-4">
        <div class="icon-box-modern">
            <i class="fas fa-print text-white"></i>
        </div>
        <div>
            <h5 class="fw-bold m-0 text-dark" style="font-size: 1.15rem;">Pusat Download Laporan</h5>
            <p class="m-0 text-muted small mt-1">Pilih jenis data dan filter untuk melihat preview data.</p>
        </div>
    </div>

    {{-- DYNAMIC FORM --}}
    <form id="downloadForm" action="{{ route('admin.laporan.pdf') }}" method="GET" target="_blank">
        @csrf
        
        <div class="filter-grid">
            {{-- 1. JENIS LAPORAN --}}
            <div class="form-group custom-field">
                <label class="field-label">
                    <i class="fas fa-layer-group text-primary"></i> Jenis Data
                </label>
                <div class="field-wrapper">
                    <select name="type" id="reportType" class="form-select modern-input auto-reload">
                        <optgroup label="Peminjaman">
                            <option value="peminjaman" {{ $type == 'peminjaman' ? 'selected' : '' }}>Laporan Peminjaman Aktif</option>
                            <option value="riwayat" {{ $type == 'riwayat' ? 'selected' : '' }}>Laporan Riwayat (Selesai)</option>
                        </optgroup>
                        <optgroup label="Ranking & Analisis">
                            <option value="ranking_approval" {{ $type == 'ranking_approval' ? 'selected' : '' }}>Prioritas Approval (SAW)</option>
                            <option value="ranking_user" {{ $type == 'ranking_user' ? 'selected' : '' }}>Ranking Peminjam</option>
                        </optgroup>
                        <optgroup label="Feedback">
                            <option value="ulasan" {{ $type == 'ulasan' ? 'selected' : '' }}>Laporan Ulasan Pengguna</option>
                        </optgroup>
                    </select>
                </div>
            </div>

            {{-- FILTER: TANGGAL --}}
            <div class="form-group custom-field filter-date">
                <label class="field-label">
                    <i class="far fa-calendar-alt text-muted"></i> Mulai
                </label>
                <input type="date" name="start_date" class="form-control modern-input auto-reload" value="{{ request('start_date') }}">
            </div>
            <div class="form-group custom-field filter-date">
                <label class="field-label">
                    <i class="far fa-calendar-alt text-muted"></i> Selesai
                </label>
                <input type="date" name="end_date" class="form-control modern-input auto-reload" value="{{ request('end_date') }}">
            </div>

            {{-- FILTER: STATUS --}}
            <div class="form-group custom-field filter-status" id="filterStatusWrapper">
                <label class="field-label">Status</label>
                <select name="status" class="form-select modern-input auto-reload">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="digunakan" {{ request('status') == 'digunakan' ? 'selected' : '' }}>Digunakan</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            {{-- FILTER: ROLE --}}
            <div class="form-group custom-field filter-role d-none" id="filterRoleWrapper">
                <label class="field-label">Kategori User</label>
                <select name="role" class="form-select modern-input auto-reload">
                    <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="mahasiswa" {{ request('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ request('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                </select>
            </div>

            {{-- FILTER: PERIODE --}}
            <div class="form-group custom-field filter-periode d-none" id="filterPeriodeWrapper">
                <label class="field-label">Periode</label>
                <select name="periode" class="form-select modern-input auto-reload">
                    <option value="harian" {{ request('periode') == 'harian' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="mingguan" {{ request('periode') == 'mingguan' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulanan" {{ request('periode') == 'bulanan' || !request('periode') ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="semester" {{ request('periode') == 'semester' ? 'selected' : '' }}>Semester Ini</option>
                    <option value="tahunan" {{ request('periode') == 'tahunan' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </div>
        </div>

        {{-- BUTTONS (Download Only) --}}
        <div class="mt-4 pt-2">
            <button type="submit" class="btn-download-modern w-100">
                <div class="icon-wrapper">
                    <i class="fas fa-file-download"></i>
                </div>
                <span>Download PDF Laporan</span>
            </button>
        </div>
    </form>
</div>

{{-- LIVE PREVIEW TABLE --}}
<div class="interactive-table mt-4 p-4">
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; margin: 0;">Preview Data</h2>
        <div class="d-flex align-items-center gap-2">
            @if(request('start_date') || request('status') || (request('role') && request('role') !== 'all'))
                    <span class="badge bg-soft-primary text-primary">Terfilter</span>
            @else
                    <span class="badge bg-soft-info text-info"><i class="fas fa-globe"></i> Semua Data</span>
            @endif
        </div>
    </div>
    
    <div style="overflow-x: auto;">
        <div id="previewTableContainer">
            {{-- Content Loaded via Reload --}}
            @if($type == 'riwayat')
                @include('admin.laporan.partials.table_riwayat', ['data' => $data])
            @elseif($type == 'ranking_approval')
                @include('admin.laporan.partials.table_ranking', ['data' => $data])
            @elseif($type == 'ulasan')
                 @include('admin.laporan.partials.table_ulasan', ['data' => $data])
            @elseif($type == 'ranking_user')
                @include('admin.laporan.partials.table_ranking_user', ['data' => $data])
            @else
                @include('admin.laporan.partials.table_peminjaman', ['data' => $data])
            @endif
        </div>
    </div>
</div>

{{-- Style reused from previous steps, no changes needed to CSS logic --}}
<style>
    /* Modern Grid Layout */
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        align-items: end;
    }
    @media (max-width: 992px) {
        .filter-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .filter-grid { grid-template-columns: 1fr; }
    }

    /* Icon Box */
    .icon-box-modern {
        width: 50px; height: 50px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }

    /* Input Styling */
    .field-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #4b5563;
        margin-bottom: 8px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .modern-input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.95rem;
        color: #1f2937;
        background-color: #f9fafb;
        transition: all 0.2s ease;
    }
    .modern-input:focus {
        border-color: #3b82f6;
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* Buttons */
    .btn-download-modern {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border: none;
        padding: 14px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.25);
    }
    .btn-download-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.4);
    }
    .btn-download-modern .icon-wrapper {
        background: rgba(255, 255, 255, 0.2);
        width: 28px; height: 28px;
        border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem;
    }

    /* Utilities */
    .d-none { display: none !important; }
    .bg-soft-info { background: #e0f2fe; } .text-info { color: #0284c7; }
    .d-flex { display: flex; }
    .gap-3 { gap: 1rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .pb-4 { padding-bottom: 1.5rem; }
    .border-bottom { border-bottom: 1px solid #f3f4f6; }

    /* Custom Dropdown Styling */
    select.modern-input optgroup {
        font-weight: 800;
        color: #111827; /* Darker header */
        font-style: normal;
        background-color: #f3f4f6;
        padding: 10px;
    }
    select.modern-input option {
        padding: 10px;
        color: #4b5563;
        background-color: #fff;
        margin-left: 10px; /* Intent */
        font-weight: 500;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportType = document.getElementById('reportType');
        const form = document.getElementById('downloadForm');
        
        // Filter Elements
        const dateFilters = document.querySelectorAll('.filter-date');
        const statusFilter = document.getElementById('filterStatusWrapper');
        const roleFilter = document.getElementById('filterRoleWrapper');
        const periodeFilter = document.getElementById('filterPeriodeWrapper');
        
        const autoReloadInputs = document.querySelectorAll('.auto-reload');

        // Routes
        const routes = {
            'peminjaman': "{{ route('admin.laporan.pdf') }}",
            'riwayat': "{{ route('admin.laporan.pdf') }}",
            'ranking_approval': "{{ route('admin.ranking.export') }}",
            'ranking_user': "{{ route('admin.ranking.user.export') }}",
            'ulasan': "{{ route('admin.ulasan.export') }}"
        };

        // 1. UPDATE FORM VISIBILITY and ACTION
        function updateForm() {
            const type = reportType.value;
            if (routes[type]) form.action = routes[type];

            // Visibility
            dateFilters.forEach(el => el.classList.remove('d-none'));
            statusFilter.classList.add('d-none');
            // ROLE FILTER: Show for ALL types now
            roleFilter.classList.remove('d-none');

            if (type === 'peminjaman' || type === 'riwayat') {
                statusFilter.classList.remove('d-none');
            } else if (type === 'ranking_approval') {
                statusFilter.classList.remove('d-none');
                roleFilter.classList.remove('d-none');
            } else if (type === 'ranking_approval') {
                dateFilters.forEach(el => el.classList.add('d-none'));
                roleFilter.classList.remove('d-none');
            } else if (type === 'ulasan' || type === 'ranking_user') {
                dateFilters.forEach(el => el.classList.add('d-none'));
                periodeFilter.classList.remove('d-none');
            }
        }

        // 2. AUTO RELOAD LOGIC
        function triggerReload() {
            // Build Query Params manually from all modern inputs
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // Reload page with new params to fetch preview
            window.location.href = "{{ route('admin.laporan.index') }}?" + params.toString();
        }

        // Attach listener to ALL inputs with 'auto-reload' class
        autoReloadInputs.forEach(input => {
            input.addEventListener('change', triggerReload);
        });

        // Initial setup
        updateForm();
    });
</script>

@endsection
