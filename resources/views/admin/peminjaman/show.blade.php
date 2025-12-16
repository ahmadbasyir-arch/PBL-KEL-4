@extends('layouts.app')

@section('title', 'Detail Permintaan Peminjaman')

@section('content')
<div class="content-wrapper">
    <div class="welcome-banner" style="padding: 20px 30px; margin-bottom: 25px;">
        <h1 style="font-size: 1.5rem; margin-bottom: 5px;">Detail Peminjaman</h1>
        <p style="opacity: 0.9;">Review detail permintaan sebelum memberikan keputusan.</p>
    </div>

    <div class="card" style="border-radius: 12px; border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <div class="card-body" style="padding: 30px;">
            <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                
                {{-- Left Column: User & Status --}}
                <div style="flex: 1; min-width: 300px; border-right: 1px solid #f3f4f6; padding-right: 30px;">
                    <div style="margin-bottom: 25px;">
                        <span class="status-badge status-{{ $peminjaman->status }}">
                            {{ ucfirst($peminjaman->status) }}
                        </span>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <h6 style="color: #6b7280; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Peminjam</h6>
                        <div style="display: flex; align-items: center; gap: 15px; margin-top: 10px;">
                            <div style="width: 50px; height: 50px; background: #eff6ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 1.25rem; font-weight: 700;">
                                {{ substr($peminjaman->mahasiswa->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <h5 style="margin: 0; font-size: 1.1rem; color: #111827;">{{ $peminjaman->mahasiswa->name ?? '-' }}</h5>
                                <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">{{ $peminjaman->mahasiswa->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h6 style="color: #6b7280; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Keperluan</h6>
                        <p style="margin-top: 5px; color: #374151; line-height: 1.6;">
                            {{ $peminjaman->keperluan }}
                        </p>
                    </div>
                </div>

                {{-- Right Column: Item & Timing --}}
                <div style="flex: 1; min-width: 300px;">
                    <div style="margin-bottom: 20px;">
                        <h6 style="color: #6b7280; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Item Yang Diminta</h6>
                        <div style="background: #f9fafb; padding: 15px; border-radius: 10px; margin-top: 8px;">
                            <h4 style="margin: 0; color: #111827; font-size: 1.1rem;">
                                @if($peminjaman->idRuangan)
                                    <i class="fas fa-door-open me-2" style="color: #3b82f6;"></i>
                                    {{ $peminjaman->ruangan->namaRuangan ?? 'Ruangan Dihapus' }}
                                @else
                                    <i class="fas fa-box me-2" style="color: #3b82f6;"></i>
                                    {{ $peminjaman->unit->namaUnit ?? 'Unit Dihapus' }}
                                @endif
                            </h4>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                        <div>
                            <h6 style="color: #6b7280; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Tanggal</h6>
                            <p style="margin-top: 5px; color: #111827; font-weight: 600;">
                                {{ \Carbon\Carbon::parse($peminjaman->tanggalPinjam)->isoFormat('dddd, D MMM Y') }}
                            </p>
                        </div>
                        <div>
                            <h6 style="color: #6b7280; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Pukul</h6>
                            <p style="margin-top: 5px; color: #111827; font-weight: 600;">
                                {{ substr($peminjaman->jamMulai, 0, 5) }} - {{ substr($peminjaman->jamSelesai, 0, 5) }} WIB
                            </p>
                        </div>
                    </div>

                    @if($peminjaman->status == 'pending')
                    <div style="background: #fffbeb; padding: 15px; border-radius: 8px; border: 1px solid #fcd34d;">
                        <h6 style="margin: 0 0 10px 0; color: #92400e; font-weight: 700;">Konfirmasi Persetujuan</h6>
                        <div style="display: flex; gap: 10px;">
                            <form action="{{ route('admin.peminjaman.approve', $peminjaman->id) }}" method="POST" style="flex: 1;">
                                @csrf
                                <button type="submit" class="btn btn-success" style="width: 100%; justify-content: center; background: #22c55e;">
                                    <i class="fas fa-check-circle me-1"></i> Setujui
                                </button>
                            </form>
                            <form action="{{ route('admin.peminjaman.reject', $peminjaman->id) }}" method="POST" style="flex: 1;">
                                @csrf
                                <button type="submit" class="btn btn-danger" style="width: 100%; justify-content: center; background: #ef4444;">
                                    <i class="fas fa-times-circle me-1"></i> Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>

            </div>
            
            <div style="margin-top: 30px; border-top: 1px solid #f3f4f6; padding-top: 20px; text-align: right;">
                <a href="{{ route('admin.ranking.index') }}" class="btn btn-secondary" style="background: #9ca3af; color: white; margin-right: 10px;">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Prioritas
                </a>
                <a href="{{ route('admin.peminjaman.index') }}" class="btn btn-primary" style="background: #3b82f6;">
                    <i class="fas fa-list me-1"></i> Semua Peminjaman
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
