@extends('layouts.app')

@section('title', 'Manajemen Data Ruangan')

@section('content')
<div class="section-header">
    <h1>Manajemen Data Ruangan</h1>
    <p>Kelola data ruangan kampus dengan mudah dan efisien.</p>
</div>

@if (session('success'))
    <div class="success-message">{{ session('success') }}</div>
@endif

{{-- ============ MODE TAMBAH / EDIT / DETAIL ============ --}}
@if (isset($mode) && in_array($mode, ['create', 'edit', 'show']))
    <div class="card p-4 shadow-sm">
        <h3 class="mb-3">
            @if ($mode == 'create')
                Tambah Ruangan Baru
            @elseif ($mode == 'edit')
                Edit Ruangan
            @else
                Detail Ruangan
            @endif
        </h3>

        <form 
            action="{{ $mode == 'create' ? route('admin.ruangan.store') : ($mode == 'edit' ? route('admin.ruangan.update', $ruangan->id) : '#') }}" 
            method="POST">
            @csrf
            @if ($mode == 'edit')
                @method('PUT')
            @endif

            <div class="form-group mb-3">
                <label>Nama Ruangan</label>
                <input type="text" name="namaRuangan" class="form-control"
                    value="{{ old('namaRuangan', $ruangan->namaRuangan ?? '') }}" 
                    {{ $mode == 'show' ? 'readonly' : '' }}>
            </div>

            <div class="form-group mb-3">
                <label>Lokasi</label>
                <input type="text" name="lokasi" class="form-control"
                    value="{{ old('lokasi', $ruangan->lokasi ?? '') }}"
                    {{ $mode == 'show' ? 'readonly' : '' }}>
            </div>

            <div class="form-group mb-3">
                <label>Kapasitas</label>
                <input type="number" name="kapasitas" class="form-control"
                    value="{{ old('kapasitas', $ruangan->kapasitas ?? '') }}"
                    {{ $mode == 'show' ? 'readonly' : '' }}>
            </div>

            <div class="form-group mb-3">
                <label>Status</label>
                <select name="status" class="form-control" {{ $mode == 'show' ? 'disabled' : '' }}>
                    @foreach (['tersedia', 'dipinjam', 'perawatan'] as $status)
                        <option value="{{ $status }}" 
                            {{ (old('status', $ruangan->status ?? '') == $status) ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mt-3">
                @if ($mode != 'show')
                    <button type="submit" class="btn btn-success shadow-sm">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                @endif
                <a href="{{ route('admin.ruangan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
@else
{{-- ============ MODE INDEX (DAFTAR) ============ --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Daftar Ruangan</h3>
    <a href="{{ route('admin.ruangan.create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus"></i> Tambah Ruangan
    </a>
</div>

<div class="card p-3 shadow-sm">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Ruangan</th>
                <th>Lokasi</th>
                <th>Kapasitas</th>
                <th>Status</th>
                <th style="width:180px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ruangan as $item)
                <tr>
                    <td><strong>{{ $item->namaRuangan }}</strong></td>
                    <td>{{ $item->lokasi ?? '-' }}</td>
                    <td>{{ $item->kapasitas }} orang</td>
                    <td>
                        <span class="status-badge status-{{ Str::slug($item->status) }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.ruangan.show', $item->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.ruangan.edit', $item->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.ruangan.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus ruangan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data ruangan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

<style>
.data-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
}
.data-table th {
    background: #f6f7f9;
    padding: 12px;
    text-align: left;
    font-weight: 700;
    color: #333;
}
.data-table td {
    padding: 12px;
    border-top: 1px solid #eee;
    color: #333;
}
.data-table tbody tr:hover { background: #fafafa; }
.action-buttons { display: flex; gap: 6px; }
.status-badge {
    padding: 5px 10px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
}
.status-tersedia { background: #d4edda; color: #155724; }
.status-dipinjam { background: #f8d7da; color: #721c24; }
.status-perawatan { background: #fff3cd; color: #856404; }
</style>
@endsection