<table class="data-table">
    <thead>
        <tr>
            <th width="50" class="text-center">No</th>
            <th>Pengguna</th>
            <th>Rating</th>
            <th>Ulasan</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $item)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>
                <div class="fw-bold text-dark">{{ $item->user->name ?? 'User' }}</div>
                <div class="text-muted small">{{ $item->user->role ?? '-' }}</div>
            </td>
            <td>
                <div class="text-warning small">
                    @for($i=0; $i<$item->rating; $i++) <i class="fas fa-star"></i> @endfor
                    @for($i=$item->rating; $i<5; $i++) <i class="far fa-star"></i> @endfor
                </div>
            </td>
            <td>{{ $item->komentar }}</td>
            <td class="text-muted small">
                 {{ $item->created_at->format('d/m/Y H:i') }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-5">
                <i class="far fa-comments fa-3x mb-3 text-muted opacity-25"></i>
                <p class="text-muted m-0">Belum ada ulasan.</p>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
