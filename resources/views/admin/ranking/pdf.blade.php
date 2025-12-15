<!DOCTYPE html>
<html>
<head>
    <title>Laporan Ranking SAW</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .meta { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 2px 5px; border-radius: 4px; font-size: 10px; color: white; }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; color: black; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 10px; text-align: right; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Perengkingan Peminjaman (Metode SAW)</h2>
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <div class="meta">
        <strong>Bobot Kriteria:</strong><br>
        <ul>
            <li>C1 (Kepentingan): {{ $bobot['C1']*100 }}% [Benefit]</li>
            <li>C2 (Perencanaan): {{ $bobot['C2']*100 }}% [Benefit]</li>
            <li>C3 (Durasi): {{ $bobot['C3']*100 }}% [Cost]</li>
            <li>C4 (Kondisi): {{ $bobot['C4']*100 }}% [Benefit]</li>
        </ul>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">Rank</th>
                <th width="25%">Nama</th>
                <th width="20%">Role</th>
                <th class="text-center" width="10%">Total Pinjam</th>
                <th class="text-center" width="15%">Skor SAW (V)</th>
                <th width="25%">Detail Normalisasi (R)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rankings as $r)
                <tr>
                    <td class="text-center">{{ $r->rank }}</td>
                    <td>{{ $r->user->name }}</td>
                    <td>{{ ucfirst($r->user->role ?? '-') }}</td>
                    <td class="text-center">{{ $r->total_minjam }}</td>
                    <td class="text-center"><strong>{{ $r->saw_score }}</strong></td>
                    <td style="font-size: 10px;">
                        R1: {{ number_format($r->detail['C1'], 2) }}<br>
                        R2: {{ number_format($r->detail['C2'], 2) }}<br>
                        R3: {{ number_format($r->detail['C3'], 2) }}<br>
                        R4: {{ number_format($r->detail['C4'], 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data peminjaman yang masuk kriteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh Sistem Informasi Peminjaman
    </div>
</body>
</html>
