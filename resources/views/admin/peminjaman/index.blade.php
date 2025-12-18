@extends('layouts.app')

@section('title', 'Data Peminjaman')

@section('content')
<div class="section-header">
    <h1>Data Peminjaman Terbaru</h1>
</div>

@if (session('success'))
    <div class="success-message">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="error-message">{{ session('error') }}</div>
@endif

<div class="interactive-table">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Mahasiswa</th>
                <th>Dipinjam</th>
                <th>Keperluan</th>
                <th>Status</th>
                <th>Tanggal Pinjam</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($peminjaman as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <!-- Perubahan kecil di bawah: prioritaskan user->name, lalu mahasiswa->name, lalu mahasiswa->namaLengkap -->
                    <td>{{ $p->user->name ?? $p->mahasiswa->name ?? $p->mahasiswa->namaLengkap ?? '-' }}</td>
                    <td>
                        @if ($p->ruangan)
                            {{ $p->ruangan->namaRuangan }}
                        @elseif ($p->unit)
                            {{ $p->unit->namaUnit }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $p->keperluan }}</td>
                    <td>
                        @php
                            $label = ucfirst(str_replace('_', ' ', $p->status));
                            $class = match ($p->status) {
                                'pending' => 'status-pending',
                                'disetujui', 'digunakan' => 'status-disetujui',
                                'menyelesaikan', 'menunggu_validasi' => 'status-warning',
                                'selesai' => 'status-selesai',
                                'ditolak' => 'status-ditolak',
                                default => 'status-default',
                            };
                        @endphp
                        <span class="status-badge {{ $class }}">{{ $label }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggalPinjam)->isoFormat('D MMM YYYY') }}</td>
                    <td>
                        {{-- 🔹 Tombol aksi sesuai status --}}
                        @if ($p->status === 'pending')
                            <form action="{{ route('admin.peminjaman.approve', $p->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn btn-success btn-sm"><i class="fas fa-check"></i> Setuju</button>
                            </form>
                            <form action="{{ route('admin.peminjaman.reject', $p->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Tolak</button>
                            </form>

                        @elseif ($p->status === 'menyelesaikan' || $p->status === 'menunggu_validasi')
                            <button type="button" class="btn btn-success btn-sm"
    onclick="openValidationModal({{ $p->id }}, '{{ $p->ruangan ? 'ruangan' : 'unit' }}')"
    data-id="{{ $p->id }}"
    data-jenis="{{ $p->ruangan ? 'ruangan' : 'unit' }}">
    <i class="fas fa-check"></i> Validasi
</button>

                            <form action="{{ route('admin.peminjaman.reject', $p->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Tolak Selesai</button>
                            </form>

                        @elseif ($p->status === 'disetujui' || $p->status === 'digunakan')
                            <span class="status-badge status-disetujui">
                                <i class="fas fa-play"></i> Sedang Digunakan
                            </span>

                        @elseif ($p->status === 'selesai')
                            <span class="status-badge status-selesai">
                                <i class="fas fa-check-circle"></i> Selesai
                            </span>

                        @elseif ($p->status === 'ditolak')
                            <span class="status-badge status-ditolak">
                                <i class="fas fa-times-circle"></i> Ditolak
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;">Belum ada data peminjaman.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ✅ Modal Validasi Pengembalian --}}
<div id="validationModal" class="modal" style="display:none;">
    <div class="modal-content">
        <h4>Validasi Pengembalian</h4>
        <form id="validationForm" method="POST" action="">
            @csrf
            
            <div class="form-group">
                <label for="kondisi">Kondisi Barang</label>
                <select name="kondisi" id="kondisi" class="form-control" required>
                    <option value="">-- Pilih Kondisi --</option>
                    <option value="Bagus">Bagus</option>
                    <option value="Lecet">Lecet</option>
                    <option value="Rusak">Rusak</option>
                </select>
            </div>

            <div class="form-group">
                <label for="catatan">Catatan (opsional)</label>
                <textarea name="catatan" id="catatan" class="form-control" rows="3"
                    placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn btn-success">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeValidationModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- ============================
     SCRIPT: modal + submit
     ============================ --}}
<script>
(function () {
    // Helper untuk mengambil CSRF Token
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
               document.querySelector('input[name="_token"]')?.value;
    }

    // Fungsi Submit Validation (Re-usable)
    function submitValidation(url, payload) {
        console.log('[debug] Submitting validation to:', url, payload);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload),
            credentials: 'same-origin'
        })
        .then(async (res) => {
            if (res.ok) {
                // Tutup modal jika terbuka
                closeValidationModal();
                // Reload halaman
                window.location.reload();
            } else {
                let text = 'Gagal memproses validasi.';
                try {
                    const err = await res.json();
                    if (err && err.message) text = err.message;
                } catch (e) {}
                alert(text);
            }
        })
        .catch((err) => {
            console.error('Fetch error:', err);
            alert('Terjadi kesalahan koneksi.');
        });
    }

    // Global: Buka Modal atau Langsung Validasi
    window.openValidationModal = function (peminjamanId, jenis) {
        const url = `/admin/peminjaman/${peminjamanId}/validate`;

        // LOGIKA UTAMA:
        // Jika Ruangan -> Langsung konfirmasi & submit (tanpa form)
        // Jika Unit    -> Buka modal isi form
        
        if (jenis !== 'unit') {
            if (confirm('Validasi pengembalian ruangan ini? Status akan menjadi Selesai.')) {
                // Payload kosong / default untuk ruangan
                submitValidation(url, { kondisi: 'Baik', catatan: '-' });
            }
            return; 
        }

        // Jika Unit, buka modal
        const modal = document.getElementById('validationModal');
        const form = document.getElementById('validationForm');
        
        if (modal && form) {
            form.action = url; // Set action form
            
            // Reset form
            document.getElementById('kondisi').value = '';
            document.getElementById('catatan').value = '';

            modal.style.display = 'flex';
        }
    };

    window.closeValidationModal = function () {
        const modal = document.getElementById('validationModal');
        if (modal) modal.style.display = 'none';
    };

    // Event Listener untuk Submit Form (Hanya untuk Unit)
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('validationForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const payload = {
                    kondisi: document.getElementById('kondisi').value,
                    catatan: document.getElementById('catatan').value
                };
                submitValidation(form.action, payload);
            });
        }
    });

    // Delegation untuk tombol validasi (jika onclick inline gagal)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('button');
        if (btn && btn.hasAttribute('data-id') && btn.textContent.toLowerCase().includes('validasi')) {
            const id = btn.getAttribute('data-id');
            const jenis = btn.getAttribute('data-jenis');
            // Panggil global function jika belum terpanggil oleh onclick
            // Note: Biasanya onclick inline menangani ini, tapi ini backup.
            // Kita cek apakah event sudah di-handle? Tidak mudah.
            // Asumsi: jika inline onclick ada, dia jalan duluan.
        }
    });
})();
</script>

{{-- ✅ Styling tambahan --}}
<style>
    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: 600;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-disetujui { background: #d4edda; color: #155724; }
    .status-ditolak { background: #f8d7da; color: #721c24; }
    .status-selesai { background: #e2e3e5; color: #383d41; }
    .status-warning { background: #ffeeba; color: #856404; }

    .btn { padding: 5px 10px; border-radius: 6px; font-weight: 600; margin: 2px; cursor: pointer; }
    .btn-success { background-color: #28a745; color: white; border: none; }
    .btn-danger { background-color: #dc3545; color: white; border: none; }
    .btn-secondary { background-color: #6c757d; color: white; border: none; }

    .modal {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.4);
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .modal-content {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        width: 400px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .modal-actions { margin-top: 15px; text-align: right; }
    .form-group { margin-bottom: 10px; }
    .form-control {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }
</style>
@endsection