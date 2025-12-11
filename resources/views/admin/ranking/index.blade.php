@extends('layouts.app')

@section('title', 'Ranking Peminjaman')

@section('content')
    <div class="content-wrapper" style="padding: 0 30px;">
        <div class="welcome-banner">
            <h1>Ranking Peminjaman</h1>
            <p>Peringkat pengguna berdasarkan frekuensi peminjaman terbanyak (Analisis Aktivitas Pengguna).</p>
        </div>

        </div>
        
        <div class="section-header" style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f3f4f6; padding-bottom: 15px;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 12px; margin: 0;">
                <div style="background: #fffbeb; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-trophy" style="color: #f59e0b; font-size: 1.1rem;"></i>
                </div>
                Leaderboard Peminjaman
            </h2>

            <form action="{{ route('admin.ranking.export') }}" method="GET" style="display: flex; gap: 12px; align-items: center; margin: 0;">
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
                    <th style="width: 80px; text-align: center;">Rank</th>
                    <th>Nama Pengguna</th>
                    <th>Role</th>
                    <th style="text-align: center;">Total Peminjaman (Disetujui/Selesai)</th>
                    {{-- <th>Kriteria Lain (Opsional)</th> --}}
                </tr>
            </thead>
            <tbody>
                @forelse ($rankings as $user)
                    <tr>
                        <td style="text-align: center;">
                            @if($user->rank == 1)
                                <span style="font-size: 1.5rem; color: #ffd700;"><i class="fas fa-medal"></i> 1</span>
                            @elseif($user->rank == 2)
                                <span style="font-size: 1.3rem; color: #c0c0c0;"><i class="fas fa-medal"></i> 2</span>
                            @elseif($user->rank == 3)
                                <span style="font-size: 1.1rem; color: #cd7f32;"><i class="fas fa-medal"></i> 3</span>
                            @else
                                <span style="font-weight: bold; color: #6b7280;">#{{ $user->rank }}</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $user->name }}</strong>
                            <div style="font-size: 0.85rem; color: #9ca3af;">{{ $user->email }}</div>
                        </td>
                        <td>
                            @if($user->role == 'dosen')
                                <span class="status-badge" style="background: #e0f2fe; color: #075985;">Dosen</span>
                            @else
                                <span class="status-badge" style="background: #fef3c7; color: #d97706;">Mahasiswa</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span style="font-size: 1.1rem; font-weight: bold; color: #10b981;">
                                {{ $user->total_minjam }}
                            </span>
                            <span style="font-size: 0.8rem; color: #6b7280;">kali</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3" style="text-align: center; padding: 20px; color: #6b7280;">Belum ada data peminjaman yang selesai/disetujui.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
@endsection
