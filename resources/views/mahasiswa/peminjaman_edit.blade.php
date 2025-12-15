@extends('layouts.app')

@section('title', 'Edit Peminjaman')

@section('content')
<div class="section-header">
    <h1>Edit Peminjaman {{ ucfirst($peminjaman->jenis_item) }}</h1>
</div>

<form action="{{ route('peminjaman.update', $peminjaman->id) }}" 
      method="POST" id="peminjamanForm" class="form-peminjaman">
    @csrf
    @method('PUT')

    <input type="hidden" name="jenis_item" value="{{ $peminjaman->jenis_item }}">

    {{-- ========================== --}}
    {{--   DYNAMIC ITEM SELECTION  --}}
    {{-- ========================== --}}
    <div class="form-group-dynamic">
        <label>Nama {{ ucfirst($peminjaman->jenis_item) }}</label>
        <div id="item-container"></div>

        <button type="button" id="addItem" class="btn-tambah-item">
            <i class="fas fa-plus"></i> Tambah {{ ucfirst($peminjaman->jenis_item) }}
        </button>
    </div>

    {{-- TANGGAL --}}
    <div class="form-group">
        <label for="tanggalPinjam">Tanggal Peminjaman</label>
        <input type="date" id="tanggalPinjam" name="tanggalPinjam"
            value="{{ $peminjaman->tanggalPinjam }}" 
            min="{{ date('Y-m-d') }}"
            class="form-control" required>
    </div>

    {{-- JAM --}}
    <div class="form-row">
        <div class="form-group" style="flex: 1; margin-right: 10px;">
            <label for="jamMulai">Jam Mulai</label>
            <input type="time" id="jamMulai" name="jamMulai"
                value="{{ $peminjaman->jamMulai }}" 
                class="form-control" required>
        </div>

        <div class="form-group" style="flex: 1; margin-left: 10px;">
            <label for="jamSelesai">Jam Selesai</label>
            <input type="time" id="jamSelesai" name="jamSelesai"
                value="{{ $peminjaman->jamSelesai }}" 
                class="form-control" required>
        </div>
    </div>

    {{-- KEPERLUAN --}}
    <div class="form-group">
        <label for="keperluan">Keperluan</label>
        <textarea id="keperluan" name="keperluan" class="form-control" rows="4" required>
            {{ $peminjaman->keperluan }}
        </textarea>
    </div>

    {{-- BUTTON --}}
    <div class="form-actions" style="margin-top: 20px;">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Update Peminjaman
        </button>

        {{-- tombol batal otomatis sesuai role --}}
        @if(Auth::user()->role === 'mahasiswa')
            <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">Batal</a>
        @else
            <a href="{{ route('dosen.dashboard') }}" class="btn btn-secondary">Batal</a>
        @endif
    </div>

</form>

{{-- DATA UNTUK DROPDOWN --}}
<script>
    const availableItems = @json($listData);
    const selectedItems = @json($peminjaman->items); 
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemContainer = document.getElementById('item-container');
    const addItemBtn = document.getElementById('addItem');
    let itemCount = 0;

    function buildOptions(selectedId = null) {
        let html = `<option value="">-- Pilih --</option>`;

        availableItems.forEach(item => {
            const name = item.namaRuangan || item.namaUnit;

            let text = '(tersedia)';
            let disabled = '';

            if (item.is_used && item.id != selectedId) {
                text = '(sedang digunakan)';
                disabled = 'disabled';
            }

            html += `
                <option value="${item.id}" 
                    ${selectedId == item.id ? 'selected' : ''}
                    ${disabled}>
                    ${name} ${text}
                </option>`;
        });

        return html;
    }

    function createRow(selectedId = null) {
        itemCount++;

        const row = document.createElement('div');
        row.className = 'item-row';
        row.style.display = 'flex';
        row.style.marginBottom = '10px';

        row.innerHTML = `
            <select name="items[][id]" class="form-control" required style="flex:1;">
                ${buildOptions(selectedId)}
            </select>

            <button type="button" class="btn btn-danger btn-sm removeItem" style="margin-left:10px;">
                <i class="fas fa-trash"></i>
            </button>
        `;

        itemContainer.appendChild(row);
    }

    addItemBtn.addEventListener('click', () => createRow());

    itemContainer.addEventListener('click', function(e) {
        if (e.target.closest('.removeItem')) {
            e.target.closest('.item-row').remove();
        }
    });

    // LOAD DATA LAMA
    selectedItems.forEach(it => createRow(it.pivot.item_id));
});
</script>
@endsection
