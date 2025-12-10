@extends('layouts.app')

@section('title', 'Validasi Pengembalian Unit')

@section('content')
<div class="interactive-table">
    <div class="section-header" style="margin-bottom: 20px;">
        <h2>Validasi Pengembalian Unit</h2>
    </div>

    {{-- Info Peminjaman --}}
    <div class="mb-4 p-3" style="background-color: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
        <div class="row">
            <div class="col-md-6 mb-3 mb-md-0">
                <label class="text-muted small font-weight-bold d-block">UNIT YANG DIPINJAM</label>
                <span class="h5 text-dark">{{ $peminjaman->unit->namaUnit }}</span>
            </div>
            <div class="col-md-6">
                <label class="text-muted small font-weight-bold d-block">PEMINJAM</label>
                <span class="h5 text-dark">{{ $peminjaman->mahasiswa->namaLengkap }}</span>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.peminjaman.validate', $peminjaman->id) }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="font-weight-bold">Kondisi Unit Setelah Dikembalikan</label>
                    <select name="kondisi" class="form-control" required style="height: auto; padding: 10px;">
                        <option value="">-- Pilih Kondisi --</option>
                        <option value="Baik">Baik</option>
                        <option value="Kurang Baik">Kurang Baik</option>
                        <option value="Rusak">Rusak</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="font-weight-bold">Catatan (opsional)</label>
            <textarea name="catatan" class="form-control" rows="4" placeholder="Tambahkan catatan mengenai kondisi barang atau hal lainnya..."></textarea>
        </div>

        <div class="mt-4 pt-3 border-top">
            <button class="btn btn-success px-4 py-2" type="submit">
                <i class="fas fa-check mr-2"></i> Validasi dan Selesaikan
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary px-4 py-2 ml-2">Batal</a>
        </div>
    </form>
</div>
@endsection

