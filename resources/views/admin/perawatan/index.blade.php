@extends('layouts.app')

@section('title', 'Prioritas Perawatan Aset')

@section('content')
<div class="content-wrapper" style="padding: 0 30px;">
    
    {{-- Hero Section --}}
    <div class="welcome-banner" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 30px; border-radius: 16px; margin-bottom: 30px; color: white; position: relative; overflow: hidden;">
        <div style="position: relative; z-index: 2;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                <div style="background: rgba(255,255,255,0.2); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-tools" style="font-size: 1.5rem;"></i>
                </div>
                <h1 style="font-size: 2rem; font-weight: 800; margin: 0;">Prioritas Perawatan Aset</h1>
            </div>
            <p style="font-size: 1.1rem; opacity: 0.9; max-width: 800px; line-height: 1.6; margin-bottom: 25px;">
                Sistem analisis (SAW) untuk mendeteksi aset yang membutuhkan perawatan based on intensitas penggunan dan riwayat kerusakan.
            </p>
            
            <div style="display: flex; gap: 20px;">
                <div style="background: rgba(0,0,0,0.2); padding: 10px 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8;">C1: Frekuensi</div>
                    <div style="font-weight: 700;">Benefit (35%)</div>
                </div>
                <div style="background: rgba(0,0,0,0.2); padding: 10px 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8;">C2: Workload</div>
                    <div style="font-weight: 700;">Benefit (25%)</div>
                </div>
                <div style="background: rgba(0,0,0,0.2); padding: 10px 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8;">C3: Masalah</div>
                    <div style="font-weight: 700;">Benefit (40%)</div>
                </div>
            </div>
        </div>
        {{-- Decorative Circle --}}
        <div style="position: absolute; bottom: -50px; right: -20px; width: 250px; height: 250px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
    </div>

    <div class="card" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 12px;">
        <div class="card-body" style="padding: 0;">
            <table class="data-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead style="background: #f9fafb;">
                    <tr>
                        <th style="padding: 20px; text-align: center; width: 80px; font-weight: 700; color: #4b5563; border-bottom: 2px solid #e5e7eb;">Rank</th>
                        <th style="padding: 20px; text-align: left; font-weight: 700; color: #4b5563; border-bottom: 2px solid #e5e7eb;">Nama Aset</th>
                        <th style="padding: 20px; text-align: center; font-weight: 700; color: #4b5563; border-bottom: 2px solid #e5e7eb;">Tipe</th>
                        <th style="padding: 20px; text-align: center; font-weight: 700; color: #4b5563; border-bottom: 2px solid #e5e7eb;">Analisis Kriteria</th>
                        <th style="padding: 20px; text-align: center; font-weight: 700; color: #4b5563; border-bottom: 2px solid #e5e7eb;">Urgency Score</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rankings as $item)
                        <tr style="transition: background 0.2s; border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 20px; text-align: center;">
                                <div style="background: {{ $item->rank <= 3 ? '#fee2e2' : '#f3f4f6' }}; color: {{ $item->rank <= 3 ? '#991b1b' : '#6b7280' }}; font-weight: 800; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    {{ $item->rank }}
                                </div>
                            </td>
                            <td style="padding: 20px;">
                                <div style="font-weight: 700; color: #111827; font-size: 1rem;">
                                    {{ $item->asset->namaRuangan ?? $item->asset->namaUnit }}
                                </div>
                                <div style="color: #6b7280; font-size: 0.85rem; margin-top: 4px;">
                                    {{ $item->asset->kodeUnit ?? '-' }}
                                </div>
                            </td>
                            <td style="padding: 20px; text-align: center;">
                                @if($item->asset->type == 'Ruangan')
                                    <span class="badge" style="background: #eff6ff; color: #1d4ed8; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem;">Ruangan</span>
                                @else
                                    <span class="badge" style="background: #fdf2f8; color: #db2777; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem;">Unit</span>
                                @endif
                            </td>
                            <td style="padding: 20px; text-align: center;">
                                <div style="display: flex; gap: 10px; justify-content: center;">
                                    <div title="Dipinjam {{ $item->raw_metrics['C1'] }} kali" style="text-align: center; background: #f3f4f6; padding: 8px 12px; border-radius: 8px;">
                                        <div style="font-size: 0.7rem; color: #6b7280; text-transform: uppercase;">Freq</div>
                                        <div style="font-weight: 700; color: #374151;">{{ $item->raw_metrics['C1'] }}x</div>
                                    </div>
                                    <div title="Total {{ $item->raw_metrics['C2'] }} jam pemakaian" style="text-align: center; background: #f3f4f6; padding: 8px 12px; border-radius: 8px;">
                                        <div style="font-size: 0.7rem; color: #6b7280; text-transform: uppercase;">Hours</div>
                                        <div style="font-weight: 700; color: #374151;">{{ $item->raw_metrics['C2'] }}h</div>
                                    </div>
                                    <div title="{{ $item->raw_metrics['C3'] }} laporan masalah" style="text-align: center; background: {{ $item->raw_metrics['C3'] > 0 ? '#fee2e2' : '#ecfccb' }}; padding: 8px 12px; border-radius: 8px;">
                                        <div style="font-size: 0.7rem; color: {{ $item->raw_metrics['C3'] > 0 ? '#991b1b' : '#3f6212' }}; text-transform: uppercase;">Issues</div>
                                        <div style="font-weight: 700; color: {{ $item->raw_metrics['C3'] > 0 ? '#991b1b' : '#3f6212' }};">{{ $item->raw_metrics['C3'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 20px; text-align: center;">
                                <div style="font-size: 1.25rem; font-weight: 800; color: #ef4444;">
                                    {{ $item->saw_score }}
                                </div>
                                <div style="font-size: 0.75rem; color: #9ca3af;">Priority Index</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #6b7280;">
                                <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 15px; color: #d1d5db;"></i>
                                <p>Data aset belum cukup untuk dianalisis.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
