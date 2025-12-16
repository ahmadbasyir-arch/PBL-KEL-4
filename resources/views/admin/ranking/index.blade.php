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
            <div style="display: flex; gap: 12px; align-items: center;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 12px; margin: 0;">
                    <div style="background: #fffbeb; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-trophy" style="color: #f59e0b; font-size: 1.1rem;"></i>
                    </div>
                    Leaderboard
                </h2>
                
                {{-- Filter Role Buttons --}}
                <div class="filter-group" style="display: flex; background: #f3f4f6; padding: 4px; border-radius: 8px; margin-left: 20px;">
                    <a href="{{ route('admin.ranking.index', ['role' => 'all']) }}" 
                       style="text-decoration: none; padding: 5px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; color: {{ request('role') == 'all' || !request('role') ? '#fff' : '#4b5563' }}; background: {{ request('role') == 'all' || !request('role') ? '#3b82f6' : 'transparent' }};">
                       Semua
                    </a>
                    <a href="{{ route('admin.ranking.index', ['role' => 'mahasiswa']) }}" 
                       style="text-decoration: none; padding: 5px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; color: {{ request('role') == 'mahasiswa' ? '#fff' : '#4b5563' }}; background: {{ request('role') == 'mahasiswa' ? '#3b82f6' : 'transparent' }};">
                       Mahasiswa
                    </a>
                    <a href="{{ route('admin.ranking.index', ['role' => 'dosen']) }}" 
                       style="text-decoration: none; padding: 5px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; color: {{ request('role') == 'dosen' ? '#fff' : '#4b5563' }}; background: {{ request('role') == 'dosen' ? '#3b82f6' : 'transparent' }};">
                       Dosen
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.ranking.export') }}" method="GET" style="display: flex; gap: 12px; align-items: center; margin: 0;">
                <input type="hidden" name="role" value="{{ request('role', 'all') }}">
                
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

        {{-- INFO BOBOT SAW (Dynamic Form) --}}
        <form action="{{ route('admin.ranking.updateWeights') }}" method="POST">
            @csrf
            <div class="card mb-4" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border-radius: 12px; margin-bottom: 25px;">
                <div class="card-body" style="padding: 20px 30px;">
                    
                    {{-- Header Section --}}
                    <div style="margin-bottom: 20px; border-bottom: 1px solid #e5e7eb; padding-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="background: #eff6ff; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-calculator" style="color: #3b82f6; font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h5 style="margin: 0; font-weight: 700; color: #111827; font-size: 1.1rem;">Metode SAW (Simple Additive Weighting)</h5>
                                <p style="margin: 0; font-size: 0.85rem; color: #6b7280;">Sesuaikan bobot kriteria di bawah ini (Pastikan total 100%):</p>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary" style="background: #3b82f6; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600;">
                            <i class="fas fa-save me-1"></i> Simpan Bobot
                        </button>
                    </div>
    
                    {{-- Cards Grid - Flex Layout to Fill Width --}}
                    <div style="display: flex; gap: 15px; width: 100%;">
                        
                        @php
                            $options = [];
                            for($i=0; $i<=100; $i+=5) {
                                $val = $i/100;
                                $options["$val"] = "$i%";
                            }
                        @endphp
    
                        {{-- C1 --}}
                        <div style="flex: 1;">
                            <div style="background: #fdfdfd; border: 1px solid #f3f4f6; padding: 15px; border-radius: 8px; height: 100%; position: relative;">
                                <div style="margin-bottom: 8px; padding-right: 60px;">
                                    <h6 style="font-weight: 700; color: #374151; font-size: 0.95rem; margin:0; line-height: 1.2;">C1: Kepentingan</h6>
                                </div>
                                <span class="badge badge-success" style="position: absolute; top: 15px; right: 15px; background:#dcfce7; color:#166534; font-size: 0.75rem;">Benefit</span>
                                
                                <select name="saw_c1" class="form-select form-select-sm" style="font-weight: 800; color: #3b82f6; border: 1px solid #e5e7eb; margin-bottom: 5px; cursor: pointer;">
                                    @foreach($options as $val => $label)
                                        <option value="{{ $val }}" {{ (string)$bobot['C1'] === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted" style="font-size: 0.8rem; line-height: 1.3; display: block;">Tingkat urgensi kegiatan (Sidang vs Rapat).</small>
                            </div>
                        </div>
    
                        {{-- C2 --}}
                        <div style="flex: 1;">
                            <div style="background: #fdfdfd; border: 1px solid #f3f4f6; padding: 15px; border-radius: 8px; height: 100%; position: relative;">
                                <div style="margin-bottom: 8px; padding-right: 60px;">
                                    <h6 style="font-weight: 700; color: #374151; font-size: 0.95rem; margin:0; line-height: 1.2;">C2: Perencanaan</h6>
                                </div>
                                <span class="badge badge-success" style="position: absolute; top: 15px; right: 15px; background:#dcfce7; color:#166534; font-size: 0.75rem;">Benefit</span>
                                
                                <select name="saw_c2" class="form-select form-select-sm" style="font-weight: 800; color: #3b82f6; border: 1px solid #e5e7eb; margin-bottom: 5px; cursor: pointer;">
                                    @foreach($options as $val => $label)
                                        <option value="{{ $val }}" {{ (string)$bobot['C2'] === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted" style="font-size: 0.8rem; line-height: 1.3; display: block;">Jarak waktu pengajuan dari tanggal pakai.</small>
                            </div>
                        </div>
    
                        {{-- C3 --}}
                        <div style="flex: 1;">
                            <div style="background: #fdfdfd; border: 1px solid #f3f4f6; padding: 15px; border-radius: 8px; height: 100%; position: relative;">
                                <div style="margin-bottom: 8px; padding-right: 50px;">
                                    <h6 style="font-weight: 700; color: #374151; font-size: 0.95rem; margin:0; line-height: 1.2;">C3: Durasi</h6>
                                </div>
                                <span class="badge badge-warning" style="position: absolute; top: 15px; right: 15px; background:#fee2e2; color:#991b1b; font-size: 0.75rem;">Cost</span>
                                
                                <select name="saw_c3" class="form-select form-select-sm" style="font-weight: 800; color: #ef4444; border: 1px solid #e5e7eb; margin-bottom: 5px; cursor: pointer;">
                                    @foreach($options as $val => $label)
                                        <option value="{{ $val }}" {{ (string)$bobot['C3'] === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted" style="font-size: 0.8rem; line-height: 1.3; display: block;">Efisiensi waktu (makin singkat makin baik).</small>
                            </div>
                        </div>
    
                        {{-- C4 --}}
                        <div style="flex: 1;">
                            <div style="background: #fdfdfd; border: 1px solid #f3f4f6; padding: 15px; border-radius: 8px; height: 100%; position: relative;">
                                <div style="margin-bottom: 8px; padding-right: 60px;">
                                    <h6 style="font-weight: 700; color: #374151; font-size: 0.95rem; margin:0; line-height: 1.2;">C4: Kondisi</h6>
                                </div>
                                <span class="badge badge-success" style="position: absolute; top: 15px; right: 15px; background:#dcfce7; color:#166534; font-size: 0.75rem;">Benefit</span>
                                
                                <select name="saw_c4" class="form-select form-select-sm" style="font-weight: 800; color: #3b82f6; border: 1px solid #e5e7eb; margin-bottom: 5px; cursor: pointer;">
                                    @foreach($options as $val => $label)
                                        <option value="{{ $val }}" {{ (string)$bobot['C4'] === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted" style="font-size: 0.8rem; line-height: 1.3; display: block;">Kepatuhan aturan & kondisi barang.</small>
                            </div>
                        </div>
    
                    </div>
                </div>
            </div>
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">Rank</th>
                    <th>Nama Pengguna</th>
                    <th>Role</th>
                    <th style="text-align: center;">Total Pinjam</th>
                    <th style="text-align: center;">Skor SAW (V)</th>
                    <th style="text-align: right;">Detail Nilai (R)</th>
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
                            <strong>{{ $user->user->name }}</strong>
                            <div style="font-size: 0.85rem; color: #9ca3af;">{{ $user->user->email }}</div>
                        </td>
                        <td>
                            @if($user->user->role == 'dosen')
                                <span class="status-badge" style="background: #e0f2fe; color: #075985;">Dosen</span>
                            @else
                                <span class="status-badge" style="background: #fef3c7; color: #d97706;">Mahasiswa</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            {{ $user->total_minjam }}
                        </td>
                        <td style="text-align: center;">
                            <span style="font-size: 1.2rem; font-weight: bold; color: #10b981;">
                                {{ $user->saw_score }}
                            </span>
                        </td>
                        <td style="text-align: right; font-size: 0.85rem; color: #6b7280;">
                            <div>R1: {{ number_format($user->detail['C1'] * 100, 0) }}</div>
                            <div>R2: {{ number_format($user->detail['C2'] * 100, 0) }}</div>
                            <div>R3: {{ number_format($user->detail['C3'] * 100, 0) }}</div>
                            <div>R4: {{ number_format($user->detail['C4'] * 100, 0) }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3" style="text-align: center; padding: 20px; color: #6b7280;">Belum ada data peminjaman yang dapat dianalisis.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
@endsection
