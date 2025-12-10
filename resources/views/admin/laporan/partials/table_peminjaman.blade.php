<table class="data-table">
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
        @forelse($data as $index => $item)
        @php
            $status = strtolower($item->status);
            $badgeClass = match($status) {
                'menunggu' => 'status-pending',
                'disetujui' => 'status-disetujui',
                'digunakan' => 'status-digunakan',
                'deny' => 'status-ditolak',
                default => 'status-default'
            };
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($item->tanggalPinjam)->format('d-m-Y') }}</td>
            <td>{{ $item->mahasiswa->namaLengkap ?? $item->mahasiswa->username ?? '-' }}</td>
            <td>
                @if($item->ruangan)
                    {{ $item->ruangan->namaRuangan }}
                @elseif($item->unit)
                    {{ $item->unit->namaUnit }}
                @else
                    -
                @endif
            </td>
            <td>{{ $item->keperluan }}</td>
            <td>
                <span class="status-badge {{ $badgeClass }}">
                    {{ ucfirst($status) }}
                </span>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada data peminjaman aktif sesuai filter.</td></tr>
        @endforelse
    </tbody>
</table>
