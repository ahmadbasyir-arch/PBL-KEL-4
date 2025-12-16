@extends('layouts.app')

@section('title', 'Ulasan Pengguna')

@section('content')

{{-- WELCOME BANNER --}}
<div class="welcome-banner">
    <div class="d-flex align-items-center gap-3 mb-2">
        <i class="fas fa-comments fa-2x"></i>
        <h1 class="m-0">Ulasan Pengguna</h1>
    </div>
    <p>Kritik, saran, dan evaluasi yang dikirimkan oleh mahasiswa dan dosen untuk peningkatan layanan.</p>
</div>

{{-- INTERACTIVE TABLE CARD --}}
<div class="interactive-table mt-4">
    
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; margin: 0;">Daftar Masukan</h2>
        
        {{-- Tombol PDF dihapus, dipindah ke Laporan --}}
    </div>

    {{-- WRAPPER RESPONSIVE --}}
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Nama Pengguna</th>
                    <th>Role</th>
                    <th style="width: 150px; text-align: center;">Rating</th>
                    <th>Komentar</th>
                    <th style="white-space: nowrap;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ulasan as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->user->name ?? 'User' }}</div>
                            <div class="text-muted small">{{ $item->user->email ?? '-' }}</div>
                        </td>
                        <td>
                            @if(($item->user->role ?? '') == 'dosen')
                                <span class="badge bg-soft-primary text-primary">Dosen</span>
                            @else
                                <span class="badge bg-soft-warning text-warning">Mahasiswa</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="text-warning fw-bold">
                                @for($i = 0; $i < $item->rating; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                            </div>
                        </td>
                        <td>
                            <p class="m-0 text-dark" style="line-height: 1.5;">{{ $item->komentar }}</p>
                        </td>
                        <td class="text-muted small" style="white-space: nowrap;">
                            {{ $item->created_at->format('d M Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="far fa-comment-dots fa-3x mb-3 opacity-50"></i>
                            <p>Belum ada ulasan yang masuk.</p>
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
    .text-center { text-align: center; }
    .m-0 { margin: 0; }
    .mt-4 { margin-top: 1.5rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .fw-bold { font-weight: 700; }
    .text-dark { color: #111827; }
    .text-muted { color: #6b7280; }
    .small { font-size: 0.85rem; }
    
    .bg-soft-primary { background: #e0f2fe; } .text-primary { color: #075985; }
    .bg-soft-warning { background: #fef3c7; } .text-warning { color: #d97706; }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>

@endsection
