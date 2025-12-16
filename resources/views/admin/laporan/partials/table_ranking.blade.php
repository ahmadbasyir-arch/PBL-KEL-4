<table class="data-table">
    <thead>
        <tr>
            <th width="50" class="text-center">No</th>
            <th>Peminjam</th>
            <th>Item & Waktu</th>
            <th>Keperluan</th>
            <th class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $item)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>
                <div class="fw-bold text-dark">{{ $item->mahasiswa->name ?? $item->mahasiswa->username ?? '-' }}</div>
                <div class="text-muted small">{{ $item->mahasiswa->role == 'dosen' ? 'Dosen' : 'Mahasiswa' }}</div>
            </td>
            <td>
                <div class="fw-bold text-primary">
                    {{ $item->ruangan->namaRuangan ?? ($item->unit->namaUnit ?? '-') }}
                </div>
                <div class="text-muted small">
                     {{ \Carbon\Carbon::parse($item->tanggalPinjam)->isoFormat('D MMM Y') }}
                </div>
            </td>
            <td>{{ $item->keperluan }}</td>
            <td class="text-center">
                 <span class="status-badge status-pending">Pending</span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-5">
                <i class="fas fa-check-circle fa-3x mb-3 text-muted opacity-25"></i>
                <p class="text-muted m-0">Tidak ada antrian pending saat ini.</p>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
