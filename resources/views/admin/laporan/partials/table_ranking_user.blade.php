<table class="data-table">
    <thead>
        <tr>
            <th width="50" class="text-center">Rank</th>
            <th>Pengguna</th>
            <th>Kategori</th>
            <th class="text-center">Total Pinjam</th>
            <th class="text-center">Skor SAW</th>
            <th>Detail Nilai</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $item)
        <tr>
            <td class="text-center">
                @if($item->rank == 1) <i class="fas fa-trophy text-warning"></i> 
                @elseif($item->rank == 2) <i class="fas fa-medal text-secondary"></i>
                @elseif($item->rank == 3) <i class="fas fa-medal text-danger"></i>
                @else {{ $item->rank }}
                @endif
            </td>
            <td>
                <div class="fw-bold text-dark">{{ $item->user->name ?? $item->user->username ?? '-' }}</div>
                <div class="text-muted small">{{ $item->user->email ?? '' }}</div>
            </td>
            <td>{{ ucfirst($item->user->role ?? '-') }}</td>
            <td class="text-center">{{ $item->total_pinjam }} x</td>
            <td class="text-center fw-bold text-primary">{{ $item->saw_score }}</td>
            <td class="small text-muted">
                C1: {{ $item->raw_metrics['C1'] }} | C2: {{ $item->raw_metrics['C2'] }} <br>
                C3: {{ $item->raw_metrics['C3'] }} | C4: {{ $item->raw_metrics['C4'] }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-5">
                <i class="fas fa-chart-line fa-3x mb-3 text-muted opacity-25"></i>
                <p class="text-muted m-0">Tidak ada data ranking untuk periode ini.</p>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
