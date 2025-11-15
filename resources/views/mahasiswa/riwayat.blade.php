@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Riwayat Peminjaman</h4>
    </div>

    <div class="card-body">

        @if($riwayat->isEmpty())
            <p class="text-center text-muted">Belum ada riwayat peminjaman.</p>
        @else

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Jenis</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($riwayat as $item)
                        <tr>
                            <td>{{ $item->idRuangan ? 'Ruangan' : 'Unit' }}</td>

                            <td>
                                {{ $item->ruangan->namaRuangan ?? $item->unit->namaUnit }}
                            </td>

                            <td>{{ $item->tanggalPinjam }}</td>

                            <td>{{ $item->jamMulai }} - {{ $item->jamSelesai }}</td>

                            <td>{{ $item->keperluan }}</td>

                            <td>
                                <span class="badge bg-primary">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        @endif

    </div>
</div>
@endsection
