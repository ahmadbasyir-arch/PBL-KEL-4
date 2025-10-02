@extends('layouts.app')

@section('title', 'Formulir Pengajuan Peminjaman')

@section('content')
<div class="section-header">
    <h1>Formulir Peminjaman {{ ucfirst($jenis) }}</h1>
</div>

<form action="{{ route('peminjaman.store') }}" method="POST" id="peminjamanForm" class="form-peminjaman">
    @csrf
    <input type="hidden" name="jenis_item" value="{{ $jenis }}">

    {{-- Bagian Pilih Item di paling atas --}}
    <div class="form-group-dynamic">
        <label>Nama {{ ucfirst($jenis) }}</label>
        <div id="item-container">
            {{-- Dropdown item akan ditambahkan oleh JavaScript di sini --}}
        </div>
        {{-- Tombol dengan kelas 'btn-success' agar berwarna hijau --}}
        <button type="button" id="addItem" class="btn-tambah-item"><i class="fas fa-plus"></i> Tambah {{ ucfirst($jenis) }}</button>
    </div>

    {{-- Bagian detail peminjaman di bawahnya --}}
    <div class="form-group">
        <label for="tanggalPinjam">Tanggal Peminjaman</label>
        <input type="date" id="tanggalPinjam" name="tanggalPinjam" class="form-control" required>
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
        <textarea id="keperluan" name="keperluan" class="form-control" rows="4" required placeholder="Jelaskan keperluan Anda..."></textarea>
    </div>

    <div class="form-actions" style="margin-top: 20px;">
        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim Pengajuan</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>

{{-- Menyimpan data dari Controller ke dalam variabel JavaScript --}}
<script>
    const availableItems = @json($listData);
</script>

{{-- Script untuk fungsionalitas CRUD dinamis --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemContainer = document.getElementById('item-container');
        const addItemButton = document.getElementById('addItem');
        let itemCount = 0;

        function createItemRow() {
            itemCount++;
            const div = document.createElement('div');
            div.className = 'item-row';
            div.id = `item-row-${itemCount}`;
            div.style.display = 'flex';
            div.style.alignItems = 'center';
            div.style.marginBottom = '10px';

            let optionsHtml = '<option value="">-- Pilih Item --</option>';
            availableItems.forEach(item => {
                const itemName = item.namaRuangan || item.namaUnit;
                optionsHtml += `<option value="${item.id}">${itemName}</option>`;
            });

            div.innerHTML = `
                <select name="items[][id]" class="form-control" required style="flex: 1;">
                    ${optionsHtml}
                </select>
                <button type="button" class="btn btn-danger btn-sm removeItem" data-row-id="${itemCount}" style="margin-left: 10px;">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            itemContainer.appendChild(div);
        }

        addItemButton.addEventListener('click', function() {
            createItemRow();
        });

        itemContainer.addEventListener('click', function(e) {
            if (e.target.closest('.removeItem')) {
                const button = e.target.closest('.removeItem');
                const rowId = button.getAttribute('data-row-id');
                const rowToRemove = document.getElementById(`item-row-${rowId}`);
                if (rowToRemove) {
                    rowToRemove.remove();
                }
            }
        });
        
        createItemRow();

        var today = new Date().toISOString().split('T')[0];
        document.getElementById('tanggalPinjam').setAttribute('min', today);
    });
</script>
@endsection