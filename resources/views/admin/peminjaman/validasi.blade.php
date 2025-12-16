@extends('layouts.app')

@section('title', 'Validasi Pengembalian Unit')

@section('content')

<div class="interactive-table">
    <div class="section-header" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 10px; margin: 0;">
            <i class="fas fa-clipboard-check" style="color: #3b82f6;"></i> Validasi Pengembalian
        </h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary shadow-sm" style="background-color: #6b7280; border: none; padding: 8px 16px; border-radius: 6px; color: white; font-weight: 600; text-decoration: none; font-size: 0.9rem;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Info Box --}}
    <div class="mb-4 p-4" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
        <div class="row">
            <div class="col-md-6 mb-3 mb-md-0 d-flex align-items-center">
                <div class="icon-box bg-white text-primary rounded-circle border p-3 me-3 shadow-sm" style="width:50px; height:50px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-box fa-lg"></i>
                </div>
                <div>
                    <small class="text-uppercase text-muted fw-bold d-block mb-1">Unit Dipinjam</small>
                    <h5 class="fw-bold text-dark mb-0">{{ $peminjaman->unit->namaUnit }}</h5>
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-center">
                <div class="icon-box bg-white text-info rounded-circle border p-3 me-3 shadow-sm" style="width:50px; height:50px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-user fa-lg"></i>
                </div>
                <div>
                    <small class="text-uppercase text-muted fw-bold d-block mb-1">Peminjam</small>
                    <h5 class="fw-bold text-dark mb-0">{{ $peminjaman->mahasiswa->namaLengkap }}</h5>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4 shadow-sm border-0">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.peminjaman.validate', $peminjaman->id) }}" method="POST">
        @csrf

        <div class="form-group mb-4">
            <label class="fw-bold text-dark mb-2" style="font-weight:600;">Kondisi Unit</label>
            <div class="input-group">
                <span class="input-group-text bg-white text-muted" style="border-right:0;"><i class="fas fa-heartbeat"></i></span>
                <select name="kondisi" class="form-select border-start-0 ps-0 form-control" required style="height: 48px; border-left:0;">
                    <option value="" selected disabled>-- Pilih Kondisi Unit --</option>
                    <option value="Baik">Baik (Layak Pakai)</option>
                    <option value="Kurang Baik">Kurang Baik (Ada Lecet/Minor)</option>
                    <option value="Rusak">Rusak (Perlu Perbaikan)</option>
                </select>
            </div>
            <div class="form-text text-muted mt-1">Pastikan kondisi fisik unit sesuai dengan pilihan.</div>
        </div>

        <div class="form-group mb-4">
            <label class="fw-bold text-dark mb-2" style="font-weight:600;">Catatan Tambahan (Opsional)</label>
            <textarea name="catatan" class="form-control" rows="4" placeholder="Tulis catatan jika ada kerusakan atau hal penting lainnya..."></textarea>
        </div>

        <div class="mt-4 pt-3 border-top">
            <button class="btn btn-success btn-lg fw-bold w-100 shadow-sm" type="submit" style="background-color: #10b981; border: none;">
                <i class="fas fa-check-circle me-2"></i> Validasi & Selesaikan
            </button>
        </div>
    </form>
</div>

<style>
    /* Ensure form controls match the interactive table aesthetic */
    .form-control, .form-select {
        border-color: #d1d5db;
        padding: 12px;
        box-shadow: none !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }
    .input-group-text {
        border-color: #d1d5db;
        background-color: #fff;
    }
</style>

@endsection
