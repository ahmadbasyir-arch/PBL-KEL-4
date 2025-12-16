<!DOCTYPE html>
<html>
<head>
    <title>Laporan Ranking Peminjam</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .meta { font-size: 10px; color: #777; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Ranking Peminjam (Metode SAW)</h2>
        <p>Jurusan Teknologi Informasi - Politeknik Negeri Tanah Laut</p>
        <p>
            Periode: 
            @if(isset($startDate) && isset($endDate))
                {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }} - {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}
            @else
                Semua Waktu
            @endif
        </p>
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30" class="text-center">Rank</th>
                <th>Pengguna</th>
                <th>Kategori</th>
                <th class="text-center">Total Pinjam</th>
                <th class="text-center">Skor SAW</th>
                <th>Detail Kriteria</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rankings as $r)
                <tr>
                    <td class="text-center">{{ $r->rank }}</td>
                    <td>
                        {{ $r->user->name ?? $r->user->username ?? '-' }}<br>
                        <small class="text-muted">{{ $r->user->email ?? '' }}</small>
                    </td>
                    <td>{{ ucfirst($r->user->role ?? '-') }}</td>
                    <td class="text-center">{{ $r->total_pinjam }} x</td>
                    <td class="text-center"><strong>{{ $r->saw_score }}</strong></td>
                    <td style="font-size: 10px;">
                        C1 (Urgensi): {{ $r->raw_metrics['C1'] }}<br>
                        C2 (Perencanaan): {{ $r->raw_metrics['C2'] }}<br>
                        C3 (Durasi): {{ $r->raw_metrics['C3'] }}<br>
                        C4 (Kondisi): {{ $r->raw_metrics['C4'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
