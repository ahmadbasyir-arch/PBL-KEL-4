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
    onclick="openValidationModal({{ $p->id }})"
    data-id="{{ $p->id }}">
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
                    <option value="Baik">Baik</option>
                    <option value="Kurang Baik">Kurang Baik</option>
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
    // Definisikan fungsi global supaya onclick inline tahu memanggilnya
    window.openValidationModal = function (peminjamanId) {
        try {
            console.log('[debug] openValidationModal dipanggil dengan id:', peminjamanId);
            const modal = document.getElementById('validationModal');
            const form = document.getElementById('validationForm');
            if (!modal || !form) {
                console.error('[debug] Modal atau form tidak ditemukan');
                return;
            }

            // set action sesuai route yang ada di web.php
            form.action = `/admin/peminjaman/${peminjamanId}/validate`;

            // tampilkan modal
            modal.style.display = 'flex';
            modal.style.position = 'fixed';
            modal.style.zIndex = '9999';
        } catch (err) {
            console.error('[debug] error di openValidationModal:', err);
        }
    };

    function closeValidationModal() {
        const modal = document.getElementById('validationModal');
        if (modal) modal.style.display = 'none';

        const kondisi = document.getElementById('kondisi');
        const catatan = document.getElementById('catatan');
        if (kondisi) kondisi.value = '';
        if (catatan) catatan.value = '';
    }

    // Delegation: jika tombol validasi diklik tapi inline onclick tidak bekerja,
    // gunakan data-id sebagai fallback.
    document.addEventListener('click', function (e) {
        try {
            const btn = e.target.closest('button');
            if (!btn) return;

            // tombol validasi kita punya text 'Validasi' dan/atau data-id
            const isValidasiText = btn.textContent && btn.textContent.trim().toLowerCase().includes('validasi');
            const hasDataId = btn.hasAttribute && btn.hasAttribute('data-id');

            if (isValidasiText && hasDataId) {
                const id = btn.getAttribute('data-id');
                if (id) {
                    e.preventDefault();
                    // panggil fungsi global (ini aman)
                    window.openValidationModal(Number(id));
                }
            }
        } catch (err) {
            console.error('[debug] error di delegation click handler:', err);
        }
    });

    // Submit handler menggunakan fetch (JSON) seperti sebelumnya
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('validationForm');
        if (!form) {
            console.warn('[debug] validationForm tidak ditemukan');
            return;
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const action = form.action;
            if (!action) {
                alert('Target action tidak tersedia. Silakan coba ulang.');
                return;
            }

            // Ambil token CSRF dari input hidden yang dibuat oleh @csrf
            const tokenInput = form.querySelector('input[name="_token"]');
            const csrfToken = tokenInput ? tokenInput.value : '';

            const payload = {
                kondisi: document.getElementById('kondisi') ? document.getElementById('kondisi').value : '',
                catatan: document.getElementById('catatan') ? document.getElementById('catatan').value : ''
            };

            console.log('[debug] submit payload ke', action, payload);

            fetch(action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload),
                credentials: 'same-origin'
            })
            .then(async (res) => {
                if (res.ok) {
                    closeValidationModal();
                    // jika server redirect 302, res.ok tetap true di fetch; reload agar UI update
                    window.location.reload();
                } else {
                    let text = 'Gagal memproses validasi.';
                    try {
                        const err = await res.json();
                        if (err && err.message) text = err.message;
                    } catch (err) { /* ignore */ }
                    alert(text);
                }
            })
            .catch((err) => {
                console.error('Fetch error:', err);
                alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
            });
        });
    });

    // expose close function agar tombol "Batal" tetap bekerja
    window.closeValidationModal = closeValidationModal;
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