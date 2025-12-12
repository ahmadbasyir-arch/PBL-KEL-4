@extends('layouts.app')

@section('title', 'Ulasan Pengguna')

@section('content')
<div class="content-wrapper" style="padding: 0 30px;">
    <div class="welcome-banner">
        <h1>Ulasan Pengguna</h1>
        <p>Kritik, saran, ve evaluasi yang dikirimkan oleh mahasiswa dan dosen.</p>
    </div>

    <div class="section-header" style="margin-bottom: 25px; border-bottom: 1px solid #f3f4f6; padding-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 12px; margin: 0;">
            <div style="background: #e0e7ff; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-comments" style="color: #4f46e5; font-size: 1.1rem;"></i>
            </div>
            Daftar Masukan
        </h2>

        <form action="{{ route('admin.ulasan.export') }}" method="GET" style="display: flex; gap: 12px; align-items: center; margin: 0;">
            
            <div style="position: relative;">
                <select name="periode" class="form-select" style="appearance: none; -webkit-appearance: none; padding: 10px 35px 10px 15px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.9rem; outline: none; cursor: pointer; background-color: #f9fafb; color: #374151; font-weight: 500; transition: border-color 0.2s;">
                    <option value="harian">Hari Ini</option>
                    <option value="mingguan">Minggu Ini</option>
                    <option value="bulanan" selected>Bulan Ini</option>
                    <option value="semester">Semester Ini</option>
                    <option value="tahunan">Tahun Ini</option>
                </select>
                <div style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #6b7280;">
                    <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="background-color: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: background 0.2s; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);">
                <i class="fas fa-file-pdf"></i> Download PDF
            </button>
        </form>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">No</th>
                <th>Nama Pengguna</th>
                <th>Role</th>
                <th style="width: 150px; text-align: center;">Rating</th>
                <th>Komentar</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ulasan as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: 600; color: #111827;">{{ $item->user->name }}</div>
                        <div style="font-size: 0.85rem; color: #6b7280;">{{ $item->user->email }}</div>
                    </td>
                    <td>
                        @if($item->user->role == 'dosen')
                            <span class="status-badge" style="background: #e0f2fe; color: #075985;">Dosen</span>
                        @else
                            <span class="status-badge" style="background: #fef3c7; color: #d97706;">Mahasiswa</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span style="color: #fbbf24; font-weight: bold;">
                            @for($i = 0; $i < $item->rating; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </span>
                    </td>
                    <td>
                        <p style="margin: 0; color: #374151; line-height: 1.5;">{{ $item->komentar }}</p>
                    </td>
                    <td style="white-space: nowrap; color: #6b7280; font-size: 0.9rem;">
                        {{ $item->created_at->format('d M Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5" style="text-align: center; padding: 30px;">
                        <img src="https://img.icons8.com/ios/100/cbd5e0/empty-chat.png" alt="Empty" style="width: 60px; opacity: 0.5; margin-bottom: 10px;">
                        <p>Belum ada ulasan yang masuk.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
