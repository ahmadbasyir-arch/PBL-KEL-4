<table class="data-table">
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
        @forelse($data as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->created_at->format('d-m-Y') }}</td>
            <td>{{ $item->peminjaman->mahasiswa->namaLengkap ?? $item->peminjaman->mahasiswa->username ?? 'User Terhapus' }}</td>
            <td>{{ $item->peminjaman->ruangan->namaRuangan ?? ($item->peminjaman->unit->namaUnit ?? '-') }}</td>
            <td>{{ $item->isi_kritik_saran }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada data feedback sesuai filter.</td></tr>
        @endforelse
    </tbody>
</table>
