@extends('layouts.app')

@section('title', 'Jadwal Perkuliahan')

@section('content')

{{-- WELCOME BANNER --}}
<div class="welcome-banner">
    <h1>Manajemen Jadwal Perkuliahan</h1>
    <p>Kelola data jadwal perkuliahan, import jadwal, dan lihat daftar jadwal aktif.</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- IMPORT SECTION & TABLE --}}
<div class="interactive-table mt-4">
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Jadwal</h2>
        
    <div class="alert alert-info py-2 mb-0" style="font-size: 0.85rem;">
        <i class="fas fa-info-circle"></i> Gunakan header: <b>mata_kuliah, dosen, kelas, hari, jam_mulai, jam_selesai, ruangan</b>
    </div>
        
    {{-- Import Form Inline --}}
        <form action="{{ route('admin.jadwal.import') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
            @csrf
            <input type="file" name="file" class="form-control form-control-sm" accept=".csv, .xls, .xlsx" required style="max-width: 250px;">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-upload"></i> Import File
            </button>
        </form>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Hari</th>
                <th>Waktu</th>
                <th>Mata Kuliah</th>
                <th>Dosen</th>
                <th>Kelas</th>
                <th>Ruangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwals as $jadwal)
                <tr>
                    <td>
                        <span class="status-badge status-menunggu_validasi">
                            {{ $jadwal->hari }}
                        </span>
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                    </td>
                    <td class="fw-bold">{{ $jadwal->mata_kuliah }}</td>
                    <td>{{ $jadwal->dosen }}</td>
                    <td>
                         <span class="status-badge status-selesai">
                             {{ $jadwal->kelas }}
                         </span>
                    </td>
                    <td>
                        @if($jadwal->ruangan)
                            <span class="status-badge status-disetujui">
                                {{ $jadwal->ruangan->namaRuangan }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted"> Belum ada data jadwal. Silakan import file CSV. </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- CSS Ad-hoc for this page specific alignment if needed --}}
<style>
    .form-control-sm {
        padding: 5px 10px;
        font-size: 0.875rem;
        border-radius: 6px;
        border: 1px solid #d1d5db;
    }
    .gap-2 { gap: 0.5rem; }
    .d-flex { display: flex; }
    .align-items-center { align-items: center; }
    .justify-content-between { justify-content: space-between; }
    .mb-4 { margin-bottom: 1.5rem; }
</style>

@endsection
