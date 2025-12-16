<!DOCTYPE html>
<html>
<head>
    <title>Laporan Ulasan Pengguna</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .rating { color: #d97706; font-weight: bold; }
        .meta { font-size: 10px; color: #777; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Ulasan Pengguna</h2>
        <p>Jurusan Teknologi Informasi - Politeknik Negeri Tanah Laut</p>
        <p>Periode: {{ $periode }}</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Pengguna</th>
                <th style="width: 10%;">Role</th>
                <th style="width: 10%;">Rating</th>
                <th style="width: 50%;">Komentar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ulasan as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <div>{{ $item->user->name }}</div>
                        <div class="meta">{{ $item->user->email }}</div>
                    </td>
                    <td style="text-align: center;">{{ ucfirst($item->user->role) }}</td>
                    <td style="text-align: center;" class="rating">{{ $item->rating }} / 5</td>
                    <td>
                        {{ $item->komentar }}
                        <div class="meta">{{ $item->created_at->translatedFormat('d M Y H:i') }}</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Tidak ada data ulasan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
