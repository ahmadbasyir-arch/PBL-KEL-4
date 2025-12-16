<table class="data-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>Peminjam</th>
            <th>Role</th>
            <th>Item</th>
            <th>Status</th>
            <th>Kondisi Unit</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $item)
        @php
            $status = strtolower($item->status);
            $badgeClass = match($status) {
                'selesai' => 'status-selesai',
                'ditolak' => 'status-ditolak',
                default => 'status-default'
            };
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($item->tanggalPinjam)->format('d-m-Y') }}</td>
            <td>
                {{ $item->pengembalian ? \Carbon\Carbon::parse($item->pengembalian->tanggalKembali)->format('d-m-Y') : '-' }}
            </td>
            <td>{{ $item->mahasiswa->namaLengkap ?? $item->mahasiswa->username ?? '-' }}</td>
            <td><span class="badge bg-light text-dark border">{{ ucfirst($item->mahasiswa->role ?? '-') }}</span></td>
            <td>
                @if($item->ruangan)
                    {{ $item->ruangan->namaRuangan }}
                @elseif($item->unit)
                    {{ $item->unit->namaUnit }}
                @else
                    -
                @endif
            </td>
            <td>
                <span class="status-badge {{ $badgeClass }}">
                    {{ ucfirst($status) }}
                </span>
            </td>
            <td>{{ $item->kondisi_pengembalian ?? $item->pengembalian->kondisi ?? '-' }}</td>
            <td>{{ $item->catatan_pengembalian ?? $item->pengembalian->catatan ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data riwayat sesuai filter.</td></tr>
        @endforelse
    </tbody>
</table>
