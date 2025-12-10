@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi')

@section('content')

{{-- WELCOME BANNER --}}
<div class="welcome-banner">
    <h1>Laporan & Rekapitulasi</h1>
    <p>Pantau data peminjaman aktif, riwayat, dan feedback pengguna.</p>
</div>

<div class="interactive-table mt-4">
    
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Filter Laporan</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.laporan.pdf', request()->all()) }}" target="_blank" class="btn-custom btn-red">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <a href="{{ route('admin.laporan.print', request()->all()) }}" target="_blank" class="btn-custom">
                <i class="fas fa-print"></i> Cetak Laporan
            </a>
        </div>
    </div>

    {{-- CUSTOM FILTER FORM LAYOUT --}}
    <form action="{{ route('admin.laporan.index') }}" method="GET" class="filter-container">
        
        {{-- ITEM 1: TYPE --}}
        <div class="filter-item">
            <label>Jenis Laporan</label>
            <select name="type" class="form-control" onchange="this.form.submit()">
                <option value="peminjaman" {{ $type == 'peminjaman' ? 'selected' : '' }}>Peminjaman Aktif</option>
                <option value="riwayat" {{ $type == 'riwayat' ? 'selected' : '' }}>Riwayat Peminjaman</option>
                <option value="feedback" {{ $type == 'feedback' ? 'selected' : '' }}>Feedback Pengguna</option>
            </select>
        </div>
        
        {{-- ITEM 2: START DATE --}}
        <div class="filter-item">
            <label>Tanggal Mulai</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        
        {{-- ITEM 3: END DATE --}}
        <div class="filter-item">
            <label>Tanggal Selesai</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>

        {{-- ITEM 4: STATUS (Conditional) --}}
        @if($type != 'feedback')
        <div class="filter-item">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                @if($type == 'peminjaman')
                    <option value="menunggu" {{ $status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ $status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="digunakan" {{ $status == 'digunakan' ? 'selected' : '' }}>Digunakan</option>
                @else
                    <option value="selesai" {{ $status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ $status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                @endif
            </select>
        </div>
        @endif

        {{-- ITEM 5: FILTER BUTTON --}}
        <div class="filter-item filter-submit" style="flex: 0 0 50px;">
             {{-- Empty label to align button --}}
             <label>&nbsp;</label>
             <button type="submit" class="btn-custom btn-icon">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </form>

    {{-- TABLE CONTENT --}}
    <div style="overflow-x: auto; margin-top: 20px;">
        @if($type == 'feedback')
            @include('admin.laporan.partials.table_feedback', ['data' => $data])
        @elseif($type == 'riwayat')
            @include('admin.laporan.partials.table_riwayat', ['data' => $data])
        @else
            @include('admin.laporan.partials.table_peminjaman', ['data' => $data])
        @endif
    </div>
</div>

<style>
    /* Custom Layout for Filter Form (Flexbox) */
    .filter-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: flex-end;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
        margin-bottom: 20px;
    }

    .filter-item {
        flex: 1;
        min-width: 200px; /* Ensure inputs don't get squeezed too small */
    }

    .filter-item label {
        display: block;
        margin-bottom: 5px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #6b7280;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        font-size: 0.95rem;
        background-color: #fff;
    }

    /* Button Styling */
    .btn-custom {
        background-color: #3b82f6;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        border: none;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-custom:hover {
        background-color: #2563eb;
        color: white;
    }

    .btn-red {
        background-color: #ef4444; /* Tailwind Red 500 */
    }
    
    .btn-red:hover {
        background-color: #dc2626; /* Tailwind Red 600 */
    }

    .btn-icon {
        width: 100%;
        text-align: center;
        background-color: #1e40af; /* Darker blue for filter button */
    }

    /* Adjust flex for submit button column properly */
    .filter-submit {
        flex: 0 0 auto; /* Don't grow, just fit content */
        min-width: 60px;
    }
</style>

@endsection
