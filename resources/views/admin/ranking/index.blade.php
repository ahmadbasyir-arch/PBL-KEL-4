@extends('layouts.app')

@section('title', 'Prioritas Persetujuan Peminjaman')

@section('content')
    <div class="content-wrapper" style="padding: 0 30px;">
        
        {{-- Hero Section / Explanation --}}
        <div class="welcome-banner" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); padding: 30px; border-radius: 16px; margin-bottom: 30px; color: white; position: relative; overflow: hidden;">
            <div style="position: relative; z-index: 2;">
                <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 10px;">Sistem Pendukung Keputusan Approval</h1>
                <p style="font-size: 1.1rem; opacity: 0.9; max-width: 800px; line-height: 1.6;">
                    Fitur ini membantu Admin menentukan <strong>prioritas persetujuan</strong> peminjaman pending menggunakan metode <strong>SAW (Simple Additive Weighting)</strong>.
                    Setiap pengajuan dinilai berdasarkan urgensi kegiatan, perencanaan waktu, reputasi peminjam, dan efisiensi durasi.
                </p>
                <div style="margin-top: 20px; display: flex; gap: 15px;">
                    <div style="background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 10px;">
                        <i class="fas fa-exclamation-circle"></i> <strong>C1: Urgensi</strong>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 10px;">
                        <i class="fas fa-calendar-check"></i> <strong>C2: Perencanaan</strong>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 10px;">
                        <i class="fas fa-user-check"></i> <strong>C3: Reputasi</strong>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 10px;">
                        <i class="fas fa-clock"></i> <strong>C4: Durasi</strong>
                    </div>
                </div>
            </div>
            {{-- Decorative Circle --}}
            <div style="position: absolute; top: -50px; right: -50px; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
        </div>

        <div class="section-header" style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f3f4f6; padding-bottom: 15px;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 12px; margin: 0;">
                    <div style="background: #fffbeb; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-sort-amount-up" style="color: #f59e0b; font-size: 1.1rem;"></i>
                    </div>
                    Antrian Prioritas (Pending)
                </h2>
                
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

            <form action="{{ route('admin.ranking.export') }}" method="GET">
                <input type="hidden" name="role" value="{{ request('role', 'all') }}">
                <button type="submit" class="btn btn-primary" style="background-color: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: background 0.2s; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);">
                    <i class="fas fa-file-pdf"></i> Cetak Laporan
                </button>
            </form>
        </div>

        {{-- INFO BOBOT SAW (Dynamic Form) --}}
        <form action="{{ route('admin.ranking.updateWeights') }}" method="POST">
            @csrf
            <div class="card mb-4" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border-radius: 12px; margin-bottom: 25px;">
                <div class="card-body" style="padding: 20px 30px;">
                    
                    <div style="margin-bottom: 20px; border-bottom: 1px solid #e5e7eb; padding-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="background: #eff6ff; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-sliders-h" style="color: #3b82f6; font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h5 style="margin: 0; font-weight: 700; color: #111827; font-size: 1.1rem;">Konfigurasi Bobot Prioritas</h5>
                                <p style="margin: 0; font-size: 0.85rem; color: #6b7280;">Sesuaikan preferensi sistem dalam menentukan prioritas:</p>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary" style="background: #3b82f6; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600;">
                            <i class="fas fa-save me-1"></i> Update Bobot
                        </button>
                    </div>
    
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
                                <h6 style="font-weight: 700; color: #374151; font-size: 0.95rem; margin:0 0 5px 0;">C1: Urgensi Kegiatan</h6>
                                <select name="saw_c1" class="form-select form-select-sm" style="font-weight: 800; color: #3b82f6; border: 1px solid #e5e7eb; margin-bottom: 5px; cursor: pointer;">
                                    @foreach($options as $val => $label)
                                        <option value="{{ $val }}" {{ (string)$bobot['C1'] === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted" style="font-size: 0.8rem; display: block;">Semakin penting kegiatan (ex: Sidang), semakin tinggi prioritas.</small>
                            </div>
                        </div>
    
                        {{-- C2 --}}
                        <div style="flex: 1;">
                            <div style="background: #fdfdfd; border: 1px solid #f3f4f6; padding: 15px; border-radius: 8px; height: 100%; position: relative;">
                                <h6 style="font-weight: 700; color: #374151; font-size: 0.95rem; margin:0 0 5px 0;">C2: Perencanaan</h6>
                                <select name="saw_c2" class="form-select form-select-sm" style="font-weight: 800; color: #3b82f6; border: 1px solid #e5e7eb; margin-bottom: 5px; cursor: pointer;">
                                    @foreach($options as $val => $label)
                                        <option value="{{ $val }}" {{ (string)$bobot['C2'] === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted" style="font-size: 0.8rem; display: block;">Makin jauh hari booking, makin tinggi prioritas.</small>
                            </div>
                        </div>
    
                        {{-- C3 --}}
                        <div style="flex: 1;">
                            <div style="background: #fdfdfd; border: 1px solid #f3f4f6; padding: 15px; border-radius: 8px; height: 100%; position: relative;">
                                <h6 style="font-weight: 700; color: #374151; font-size: 0.95rem; margin:0 0 5px 0;">C3: Reputasi User</h6>
                                <select name="saw_c3" class="form-select form-select-sm" style="font-weight: 800; color: #3b82f6; border: 1px solid #e5e7eb; margin-bottom: 5px; cursor: pointer;">
                                    @foreach($options as $val => $label)
                                        <option value="{{ $val }}" {{ (string)$bobot['C3'] === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted" style="font-size: 0.8rem; display: block;">User yang sering meminjam & tertib lebih diprioritaskan.</small>
                            </div>
                        </div>
    
                        {{-- C4 --}}
                        <div style="flex: 1;">
                            <div style="background: #fdfdfd; border: 1px solid #f3f4f6; padding: 15px; border-radius: 8px; height: 100%; position: relative;">
                                <h6 style="font-weight: 700; color: #374151; font-size: 0.95rem; margin:0 0 5px 0;">C4: Durasi (Cost)</h6>
                                <select name="saw_c4" class="form-select form-select-sm" style="font-weight: 800; color: #ef4444; border: 1px solid #e5e7eb; margin-bottom: 5px; cursor: pointer;">
                                    @foreach($options as $val => $label)
                                        <option value="{{ $val }}" {{ (string)$bobot['C4'] === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted" style="font-size: 0.8rem; display: block;">Meminjam terlalu lama akan menurunkan prioritas.</small>
                            </div>
                        </div>
    
                    </div>
                </div>
            </div>
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px; text-align: center;">Prioritas</th>
                    <th>Peminjam</th>
                    <th>Item & Waktu</th>
                    <th style="text-align: center;">Skor Prioritas</th>
                    <th style="text-align: center;">Analisis Kriteria</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rankings as $rank)
                    <tr>
                        <td style="text-align: center;">
                            <div style="background: {{ $rank->rank <= 3 ? '#ecfccb' : '#f3f4f6' }}; color: {{ $rank->rank <= 3 ? '#4d7c0f' : '#6b7280' }}; font-weight: 800; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 2px solid {{ $rank->rank <= 3 ? '#84cc16' : '#d1d5db' }};">
                                {{ $rank->rank }}
                            </div>
                        </td>
                        <td>
                            <strong>{{ $rank->data->user->name ?? 'User' }}</strong>
                            <div style="font-size: 0.85rem; color: #6b7280;">
                                {{ $rank->data->user->role == 'dosen' ? 'Dosen' : 'Mahasiswa' }}
                            </div>
                            <div style="margin-top: 4px;">
                                <span class="status-badge status-pending">Pending</span>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #111827;">
                                {{ $rank->data->ruangan->namaRuangan ?? ($rank->data->unit->namaUnit ?? 'Item') }}
                            </div>
                            <div style="font-size: 0.85rem; color: #4b5563; margin-top: 2px;">
                                <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($rank->data->tanggalPinjam)->isoFormat('dddd, D MMM Y') }}
                            </div>
                            <div style="font-size: 0.85rem; color: #4b5563;">
                                <i class="far fa-clock me-1"></i> {{ substr($rank->data->jamMulai, 0, 5) }} - {{ substr($rank->data->jamSelesai, 0, 5) }}
                            </div>
                            <div style="font-size: 0.8rem; font-style: italic; color: #6b7280; margin-top: 4px;">
                                "{{ Illuminate\Support\Str::limit($rank->data->keperluan, 30) }}"
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: 800; color: #0ea5e9;">
                                {{ $rank->saw_score }}
                            </div>
                            <div style="font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px;">SAW Score</div>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; max-width: 250px; margin: 0 auto;">
                                <div title="Urgensi: {{ $rank->raw_metrics['C1'] }} / 5" style="border: 1px solid #e5e7eb; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem;">
                                    <span style="color: #6b7280;">Urgensi:</span> <strong>{{ $rank->raw_metrics['C1'] }}</strong>
                                </div>
                                <div title="Booking H-{{ $rank->raw_metrics['C2'] }}" style="border: 1px solid #e5e7eb; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem;">
                                    <span style="color: #6b7280;">Planning:</span> <strong>H-{{ $rank->raw_metrics['C2'] }}</strong>
                                </div>
                                <div title="Reputasi: {{ $rank->raw_metrics['C3'] }} Selesai" style="border: 1px solid #e5e7eb; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem;">
                                    <span style="color: #6b7280;">Reputasi:</span> <strong>{{ $rank->raw_metrics['C3'] }}</strong>
                                </div>
                                <div title="Durasi: {{ $rank->raw_metrics['C4'] }} Jam" style="border: 1px solid #e5e7eb; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem;">
                                    <span style="color: #ef4444;">Durasi:</span> <strong>{{ $rank->raw_metrics['C4'] }} J</strong>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('admin.peminjaman.show', $rank->data->id) }}" class="btn btn-sm btn-primary" style="background: #3b82f6; border: none;">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5" style="text-align: center; padding: 40px; color: #6b7280;">
                            <div style="background: #f3f4f6; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                                <i class="fas fa-check-circle" style="font-size: 2rem; color: #d1d5db;"></i>
                            </div>
                            <h5 style="color: #374151; font-weight: 600;">Tidak ada antrian pending</h5>
                            <p style="font-size: 0.9rem;">Semua permintaan peminjaman telah diproses.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
