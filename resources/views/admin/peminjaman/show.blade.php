@extends('layouts.app')

@section('title', 'Detail Permintaan Peminjaman')

@section('content')
<div class="content-wrapper">
    
    {{-- Welcome Banner --}}
    <div class="welcome-banner">
        <h1>Detail Peminjaman</h1>
        <p>Review detail permintaan sebelum memberikan keputusan.</p>
    </div>

    {{-- Main Content Container --}}
    <div class="interactive-table mt-4" style="border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 30px;">
        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            
            {{-- Left Column: User & Status --}}
            <div style="flex: 1; min-width: 300px; border-right: 1px solid #f3f4f6; padding-right: 30px;">
                <div style="margin-bottom: 25px;">
                    @php 
                        $status = strtolower($peminjaman->status);
                        $class = match($status) {
                            'pending' => 'status-pending',
                            'disetujui' => 'status-disetujui',
                            'digunakan', 'sedang digunakan' => 'status-digunakan',
                            'menyelesaikan' => 'status-menyelesaikan',
                            'menunggu_validasi' => 'status-menunggu_validasi',
                            'selesai' => 'status-selesai',
                            'ditolak' => 'status-ditolak',
                            default => 'status-default'
                        };
                    @endphp
                    <span class="status-badge {{ $class }}" style="font-size: 1rem; padding: 8px 16px;">
                        {{ ucfirst($peminjaman->status) }}
                    </span>
                </div>

                <div style="margin-bottom: 30px;">
                    <h6 style="color: #6b7280; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 15px;">Peminjam</h6>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 60px; height: 60px; background: #eff6ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 1.5rem; font-weight: 700;">
                            {{ substr($peminjaman->mahasiswa->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <h5 style="margin: 0; font-size: 1.2rem; color: #111827; font-weight: 600;">{{ $peminjaman->mahasiswa->name ?? '-' }}</h5>
                            <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 0.95rem;">{{ $peminjaman->mahasiswa->email ?? '-' }}</p>
                            @if(isset($peminjaman->mahasiswa->prodi))
                                <p style="margin: 2px 0 0 0; color: #9ca3af; font-size: 0.85rem;">{{ $peminjaman->mahasiswa->prodi->namaProdi ?? '' }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <h6 style="color: #6b7280; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Keperluan</h6>
                    <p style="margin-top: 8px; color: #374151; line-height: 1.6; font-size: 1rem;">
                        {{ $peminjaman->keperluan }}
                    </p>
                </div>
            </div>

            {{-- Right Column: Item & Timing --}}
            <div style="flex: 1; min-width: 300px;">
                <div style="margin-bottom: 25px;">
                    <h6 style="color: #6b7280; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Item Yang Diminta</h6>
                    <div style="background: #f9fafb; padding: 20px; border-radius: 12px; margin-top: 10px; border: 1px solid #f3f4f6;">
                        <h4 style="margin: 0; color: #111827; font-size: 1.25rem; font-weight: 600; display: flex; align-items: center;">
                            @if($peminjaman->idRuangan)
                                <i class="fas fa-door-open me-3" style="color: #3b82f6; width: 30px;"></i>
                                {{ $peminjaman->ruangan->namaRuangan ?? 'Ruangan Dihapus' }}
                            @else
                                <i class="fas fa-box me-3" style="color: #3b82f6; width: 30px;"></i>
                                {{ $peminjaman->unit->namaUnit ?? 'Unit Dihapus' }}
                            @endif
                        </h4>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 15px;">
                        <h6 style="color: #6b7280; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">Tanggal</h6>
                        <p style="margin: 0; color: #111827; font-weight: 600; font-size: 1rem;">
                            <i class="fas fa-calendar-alt text-gray-400 me-2"></i>
                            {{ \Carbon\Carbon::parse($peminjaman->tanggalPinjam)->isoFormat('dddd, D MMM Y') }}
                        </p>
                    </div>
                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 15px;">
                        <h6 style="color: #6b7280; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">Pukul</h6>
                        <p style="margin: 0; color: #111827; font-weight: 600; font-size: 1rem;">
                            <i class="fas fa-clock text-gray-400 me-2"></i>
                            {{ substr($peminjaman->jamMulai, 0, 5) }} - {{ substr($peminjaman->jamSelesai, 0, 5) }}
                        </p>
                    </div>
                </div>

                @if($status == 'pending')
                <div style="background: #fffbeb; padding: 20px; border-radius: 12px; border: 1px solid #fcd34d;">
                    <h6 style="margin: 0 0 15px 0; color: #92400e; font-weight: 700; font-size: 0.95rem;">
                        <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Persetujuan
                    </h6>
                    <div style="display: flex; gap: 15px;">
                        <form action="{{ route('admin.peminjaman.approve', $peminjaman->id) }}" method="POST" style="flex: 1;">
                            @csrf
                            <button type="submit" class="btn" style="width: 100%; display:flex; justify-content: center; align-items: center; background: #22c55e; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: 600; transition: all 0.2s;">
                                <i class="fas fa-check-circle me-2"></i> Setujui
                            </button>
                        </form>
                        <form action="{{ route('admin.peminjaman.reject', $peminjaman->id) }}" method="POST" style="flex: 1;">
                            @csrf
                            <button type="submit" class="btn" style="width: 100%; display:flex; justify-content: center; align-items: center; background: #ef4444; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: 600; transition: all 0.2s;">
                                <i class="fas fa-times-circle me-2"></i> Tolak
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>

        </div>
        
        <div style="margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 25px; display: flex; justify-content: flex-end; gap: 15px;">
            <a href="{{ route('admin.ranking.index') }}" class="btn" style="background: #9ca3af; color: white; display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 500;">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Prioritas
            </a>
            <a href="{{ route('admin.peminjaman.index') }}" class="btn" style="background: #3b82f6; color: white; display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 500;">
                <i class="fas fa-list me-2"></i> Semua Peminjaman
            </a>
        </div>
    </div>
</div>
@endsection
