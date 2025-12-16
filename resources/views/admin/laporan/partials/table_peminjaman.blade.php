<table class="data-table">
    <thead>
        <tr>
            <th width="50" class="text-center">No</th>
            <th>Peminjam</th>
            <th>Item & Keperluan</th>
            <th>Waktu</th>
            <th class="text-center">Status</th>
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
                'ditolak' => 'status-ditolak',
                'selesai' => 'status-selesai',
                default => 'status-default'
            };
        @endphp
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>
                <div class="fw-bold text-dark">{{ $item->mahasiswa->name ?? $item->mahasiswa->username ?? '-' }}</div>
                <div class="fw-bold text-dark">{{ $item->mahasiswa->name ?? $item->mahasiswa->username ?? '-' }}</div>
                <div class="text-muted small mb-1">{{ $item->mahasiswa->email ?? '' }}</div>
                <span class="badge bg-light text-dark border">{{ ucfirst($item->mahasiswa->role ?? '-') }}</span>
            </td>
            <td>
                <div class="fw-bold text-primary">
                    @if($item->ruangan)
                         <i class="fas fa-door-open me-1"></i> {{ $item->ruangan->namaRuangan }}
                    @elseif($item->unit)
                        <i class="fas fa-video me-1"></i> {{ $item->unit->namaUnit }}
                    @else
                        -
                    @endif
                </div>
                <div class="text-muted small fst-italic mt-1">"{{ Illuminate\Support\Str::limit($item->keperluan, 40) }}"</div>
            </td>
            <td>
                <div class="text-dark"><i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($item->tanggalPinjam)->isoFormat('D MMM Y') }}</div>
                <div class="text-muted small"><i class="far fa-clock me-1"></i> {{ substr($item->jamMulai, 0, 5) }} - {{ substr($item->jamSelesai, 0, 5) }}</div>
            </td>
            <td class="text-center">
                <span class="status-badge {{ $badgeClass }}">
                    @if($status == 'menunggu') <i class="fas fa-clock"></i>
                    @elseif($status == 'disetujui') <i class="fas fa-check"></i>
                    @elseif($status == 'ditolak') <i class="fas fa-times"></i>
                    @endif
                    {{ ucfirst($status) }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-5">
                <i class="fas fa-clipboard-list fa-3x mb-3 text-muted opacity-25"></i>
                <p class="text-muted m-0">Tidak ada data peminjaman aktif sesuai filter tanggal.</p>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
