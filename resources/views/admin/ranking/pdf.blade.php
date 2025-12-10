<!DOCTYPE html>
<html>
<head>
    <title>Laporan Ranking Peminjaman</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .center { text-align: center; }
        .meta { margin-bottom: 15px; }
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 10px; text-align: center; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Ranking Aktivitas Peminjaman</h1>
        <p>Unit Sarpras & TI - Politeknik Negeri Tanah Laut</p>
    </div>

    <div class="meta">
        <strong>Periode Laporan:</strong> {{ $periode }} <br>
        <strong>Tanggal Cetak:</strong> {{ \Carbon\Carbon::now()->format('d F Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">Rank</th>
                <th>Nama Peminjam</th>
                <th style="width: 100px;">Role</th>
                <th style="width: 100px;">Total Peminjaman</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rankings as $user)
                <tr>
                    <td class="center">{{ $user->rank }}</td>
                    <td>
                        {{ $user->name }} <br>
                        <small style="color: #666;">{{ $user->email }}</small>
                    </td>
                    <td class="center">{{ ucfirst($user->role) }}</td>
                    <td class="center"><strong>{{ $user->total_minjam }}</strong> kali</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="center">Tidak ada data peminjaman untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Informasi Peminjaman Sarpras TI
    </div>
</body>
</html>
