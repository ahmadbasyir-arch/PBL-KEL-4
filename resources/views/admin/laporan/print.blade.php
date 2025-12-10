<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan - {{ ucfirst($type) }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .meta { margin-bottom: 10px; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Laporan {{ ucfirst($type == 'peminjaman' ? 'Peminjaman Aktif' : ($type == 'riwayat' ? 'Riwayat Peminjaman' : 'Feedback Pengguna')) }}</h2>
        <p>Jurusan Teknologi Informasi - Politeknik Negeri Padang</p>
    </div>

    <div class="meta">
        <strong>Periode:</strong> {{ $startDate ? $startDate : 'Semua Data' }} s/d {{ $endDate ? $endDate : 'Semua Data' }} <br>
        <strong>Dicetak pada:</strong> {{ now()->format('d-m-Y H:i') }}
    </div>

    @if($type == 'feedback')
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Item</th>
                    <th>Isi Kritik/Saran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->created_at->format('d-m-Y') }}</td>
                    <td>{{ $item->peminjaman->mahasiswa->username ?? 'User Terhapus' }}</td>
                    <td>{{ $item->peminjaman->ruangan->namaRuangan ?? ($item->peminjaman->unit->namaUnit ?? '-') }}</td>
                    <td>{{ $item->isi_kritik_saran }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($type == 'riwayat')
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Peminjam</th>
                    <th>Item</th>
                    <th>Status</th>
                    <th>Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggalPinjam)->format('d-m-Y') }}</td>
                    <td>{{ $item->pengembalian ? \Carbon\Carbon::parse($item->pengembalian->tanggalKembali)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $item->mahasiswa->username ?? '-' }}</td>
                    <td>{{ $item->ruangan->namaRuangan ?? ($item->unit->namaUnit ?? '-') }}</td>
                    <td>{{ ucfirst($item->status) }}</td>
                    <td>{{ $item->pengembalian->kondisi ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Pinjam</th>
                    <th>Peminjam</th>
                    <th>Item</th>
                    <th>Keperluan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggalPinjam)->format('d-m-Y') }}</td>
                    <td>{{ $item->mahasiswa->username ?? '-' }}</td>
                    <td>{{ $item->ruangan->namaRuangan ?? ($item->unit->namaUnit ?? '-') }}</td>
                    <td>{{ $item->keperluan }}</td>
                    <td>{{ ucfirst($item->status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
