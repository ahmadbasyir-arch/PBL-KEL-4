@extends('layouts.app')

@section('title', 'Formulir Pengajuan Peminjaman')

@section('content')
<div class="section-header">
    <h1>Formulir Peminjaman {{ ucfirst($jenis) }}</h1>
</div>

<form action="{{ route('peminjaman.store') }}" method="POST" id="peminjamanForm" class="form-peminjaman">
    @csrf
    <input type="hidden" name="jenis_item" value="{{ $jenis }}">

    <div class="form-group-dynamic">
        <label>Nama {{ ucfirst($jenis) }}</label>
        <div id="item-container"></div>
        <button type="button" id="addItem" class="btn-tambah-item">
            <i class="fas fa-plus"></i> Tambah {{ ucfirst($jenis) }}
        </button>
    </div>

    <div class="form-group">
        <label for="tanggalPinjam">Tanggal Peminjaman</label>
        <input type="date" id="tanggalPinjam" name="tanggalPinjam" class="form-control"
        value="{{ request('tanggal', now()->toDateString()) }}" required>
    </div>

    <div class="form-row">
        <div class="form-group" style="flex: 1; margin-right: 10px;">
            <label for="jamMulai">Jam Mulai</label>
            <input type="time" id="jamMulai" name="jamMulai" class="form-control" required>
        </div>
        <div class="form-group" style="flex: 1; margin-left: 10px;">
            <label for="jamSelesai">Jam Selesai</label>
            <input type="time" id="jamSelesai" name="jamSelesai" class="form-control" required>
        </div>
    </div>

    <div class="form-group">
        <label for="keperluan">Keperluan</label>
        <select id="keperluan" name="keperluan" class="form-control" required>
            <option value="">-- Pilih Keperluan --</option>
            <option>Kuliah Pengganti</option>
            <option>Seminar PKL</option>
            <option>Seminar TA</option>
            <option>Seminar PBL</option>
            <option>Uji Kompetensi</option>
            <option>Sidang Tugas Akhir</option>
            <option>Acara Institusi</option>
            <option>Perkuliahan</option>
            <option>Rapat Dosen</option>
            <option>Kegiatan Sosial</option>
            <option>Seminar</option>
            <option>Evaluasi</option>
            <option>Lomba</option>
            <option>Bazar</option>
            <option>Pelatihan Mahasiswa Baru</option>
            <option>Rapat Panitia</option>
            <option>Kegiatan Alumni</option>
            <option>Pengabdian Masyarakat</option>
            <option>Studi Banding</option>
        </select>
    </div>

    <div class="form-actions" style="margin-top: 20px;">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> Kirim Pengajuan
        </button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>

<script>
    const availableItems = @json($listData ?? []);
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemContainer = document.getElementById('item-container');
    const addItemButton = document.getElementById('addItem');
    let itemCount = 0;

    function createItemRow() {
        itemCount++;

        let optionsHtml = '<option value="">-- Pilih --</option>';

        availableItems.forEach(item => {
            let name = item.namaRuangan || item.namaUnit;

// Jika UNIT → tampilkan kodeUnit
if (item.kodeUnit) {
    name = `${item.namaUnit} — ${item.kodeUnit}`;
}

let text = '(tersedia)';
let disabled = '';

if (item.is_used) {
    text = '(sedang digunakan)';
    disabled = 'disabled';
}

optionsHtml += `<option value="${item.id}" ${disabled}>${name} ${text}</option>`;

        });

        const div = document.createElement('div');
        div.className = 'item-row';
        div.style.display = 'flex';
        div.style.marginBottom = '10px';

        div.innerHTML = `
            <select name="items[][id]" class="form-control" required style="flex:1;">
                ${optionsHtml}
            </select>
            <button type="button" class="btn btn-danger btn-sm removeItem" style="margin-left:10px;">
                <i class="fas fa-trash"></i>
            </button>
        `;

        itemContainer.appendChild(div);
    }

    addItemButton.addEventListener('click', createItemRow);

    itemContainer.addEventListener('click', function(e){
        if (e.target.closest('.removeItem')) {
            e.target.closest('.item-row').remove();
        }
    });

    createItemRow();
});
</script>
@endsection
