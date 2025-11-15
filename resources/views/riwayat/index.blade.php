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
                }
                .riwayat-table th {
                    background: #26324a !important;
                    color: white;
                    text-align: center;
                    padding: 12px;
                }
                .riwayat-table td {
                    padding: 10px 12px !important;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                .col-jenis { width: 12%; }
                .col-nama { width: 28%; }
                .col-tanggal { width: 15%; }
                .col-waktu { width: 18%; }
                .col-keperluan { width: 27%; }
            </style>

            <div class="table-responsive">
                <table class="table table-bordered riwayat-table align-middle text-center">
                    <thead>
                        <tr>
                            <th class="col-jenis">Jenis</th>
                            <th class="col-nama">Nama Ruangan / Unit</th>
                            <th class="col-tanggal">Tanggal</th>
                            <th class="col-waktu">Waktu</th>
                            <th class="col-keperluan">Keperluan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($riwayat as $item)
                        <tr>
                            <td>
                                @if($item->idRuangan)
                                    Ruangan
                                @elseif($item->idUnit)
                                    Unit
                                @endif
                            </td>

                            <td class="text-start">
                                @if($item->idRuangan)
                                    {{ $item->ruangan->namaRuangan ?? '-' }}
                                @elseif($item->idUnit)
                                    {{ $item->unit->namaUnit ?? '-' }}
                                @endif
                            </td>

                            <td>{{ $item->tanggalPinjam }}</td>

                            <td>{{ $item->jamMulai }} - {{ $item->jamSelesai }}</td>

                            <td class="text-start">{{ $item->keperluan }}</td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Belum ada riwayat peminjaman.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>

@endsection
