@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-center" style="width: 100%; margin-top: 40px;">
    <div class="card shadow-sm border-0" style="width: 95%; max-width: 1250px;">

        <div class="card-header bg-primary text-white py-3 text-center">
            <h5 class="mb-0">Riwayat Peminjaman</h5>
        </div>

        <div class="card-body" style="padding: 30px;">

            <style>
                .riwayat-table {
                    table-layout: fixed !important;
                    width: 100%;
                    border-collapse: separate;
                    border-spacing: 0 10px;
                }
                .riwayat-table th {
                    background: #26324a !important;
                    color: white;
                    text-align: center;
                    padding: 15px;
                    border: none;
                    text-transform: uppercase;
                    font-size: 0.85rem;
                    letter-spacing: 1px;
                }
                .riwayat-table td {
                    background: white;
                    padding: 15px !important;
                    vertical-align: middle;
                    border-top: 1px solid #eee;
                    border-bottom: 1px solid #eee;
                }
                .riwayat-table tr:first-child td { border-top: none; }
                
                /* Soft Status Badges */
                .status-badge {
                    padding: 6px 16px;
                    border-radius: 50px;
                    font-weight: 600;
                    font-size: 0.8rem;
                    display: inline-block;
                }
                .status-selesai { background-color: #d1fae5; color: #065f46; } /* Green */
                .status-ditolak { background-color: #fee2e2; color: #991b1b; } /* Red */
                .status-disetujui { background-color: #dbeafe; color: #1e40af; } /* Blue */
                .status-pending { background-color: #fef3c7; color: #92400e; } /* Yellow */
                .status-default { background-color: #f3f4f6; color: #374151; } /* Gray */

                .col-jenis { width: 12%; }
                .col-nama { width: 25%; }
                .col-tanggal { width: 15%; }
                .col-waktu { width: 18%; }
                .col-keperluan { width: 25%; }
                
                .btn-action {
                    border-radius: 20px;
                    font-size: 0.8rem;
                    font-weight: 600;
                    padding: 6px 14px;
                    transition: all 0.2s;
                }
                .btn-action:hover { transform: translateY(-1px); }
            </style>

            <div class="table-responsive">
                <table class="table riwayat-table align-middle text-center" style="border: none;">
                    <thead>
                        <tr>
                            <th class="col-jenis" style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">Jenis</th>
                            <th class="col-nama">Nama Ruangan / Unit</th>
                            <th class="col-tanggal">Tanggal</th>
                            <th class="col-waktu">Waktu</th>
                            <th class="col-keperluan">Keperluan</th>
                            <th width="10%">Status</th>
                            <th width="12%" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($riwayat as $item)
                        <tr style="box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                            <td class="text-secondary fw-bold">
                                @if($item->idRuangan)
                                    <i class="fas fa-door-open me-1"></i> Ruangan
                                @elseif($item->idUnit)
                                    <i class="fas fa-project-diagram me-1"></i> Unit
                                @endif
                            </td>

                            <td class="text-start fw-bold text-dark">
                                @if($item->idRuangan)
                                    {{ $item->ruangan->namaRuangan ?? '-' }}
                                @elseif($item->idUnit)
                                    {{ $item->unit->namaUnit ?? '-' }}
                                @endif
                            </td>

                            <td class="text-muted">{{ \Carbon\Carbon::parse($item->tanggalPinjam)->format('d M Y') }}</td>

                            <td class="text-muted">{{ date('H:i', strtotime($item->jamMulai)) }} - {{ date('H:i', strtotime($item->jamSelesai)) }}</td>

                            <td class="text-start text-secondary">{{ Str::limit($item->keperluan, 30) }}</td>

                            <td>
                                @php
                                    $statusClass = match($item->status) {
                                        'selesai' => 'status-selesai',
                                        'ditolak' => 'status-ditolak',
                                        'disetujui' => 'status-disetujui',
                                        'pending' => 'status-pending',
                                        default => 'status-default',
                                    };
                                    $statusLabel = match($item->status) {
                                        'selesai' => 'Selesai',
                                        'ditolak' => 'Ditolak',
                                        'disetujui' => 'Disetujui',
                                        'pending' => 'Pending',
                                        default => ucfirst($item->status),
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            <td>
                                @if($item->status == 'selesai')
                                    <span class="text-success small fw-bold"><i class="fas fa-check-circle"></i> Selesai</span>
                                @else
                                    <span class="text-muted small" style="opacity: 0.5;"><i class="fas fa-minus"></i></span>
                                @endif
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <img src="https://img.icons8.com/ios/100/cbd5e0/empty-box.png" alt="Empty" style="width: 60px; opacity: 0.5;">
                                <p class="mt-2">Belum ada riwayat peminjaman.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            </div>

        </div>

    </div>
</div>

@endsection
