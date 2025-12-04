@extends('layouts.app')

@section('title', 'Validasi Pengembalian Unit')

@section('content')
<div class="card p-4">
    <h2>Validasi Pengembalian Unit</h2>
    <p><strong>Unit:</strong> {{ $peminjaman->unit->namaUnit }}</p>
    <p><strong>Peminjam:</strong> {{ $peminjaman->mahasiswa->namaLengkap }}</p>

    @if ($errors->any())
        <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.peminjaman.validate', $peminjaman->id) }}" method="POST">
        @csrf

        <div class="form-group mt-3">
            <label>Kondisi Unit Setelah Dikembalikan:</label>
            <select name="kondisi" class="form-control" required>
                <option value="">-- Pilih Kondisi --</option>
                <option value="Baik">Baik</option>
                <option value="Kurang Baik">Kurang Baik</option>
                <option value="Rusak">Rusak</option>
            </select>
        </div>

        <div class="form-group mt-3">
            <label>Catatan (opsional)</label>
            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
        </div>

        <button class="btn btn-success mt-3" type="submit">Validasi dan Selesaikan</button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mt-3">Batal</a>
    </form>
</div>
@endsection