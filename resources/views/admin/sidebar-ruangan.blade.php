@extends('layouts.app')

@section('title', 'Manajemen Data Ruangan')

@section('content')
<div class="section-header">
    <h1>Manajemen Data Ruangan</h1>
</div>

@if (session('success'))
    <div class="success-message">{{ session('success') }}</div>
@endif

{{-- ============ MODE TAMBAH / EDIT / DETAIL ============ --}}
@if (isset($mode) && in_array($mode, ['create', 'edit', 'show']))
    <div class="card p-4">
        <h3>
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

            <div class="form-group">
                <label>Nama Ruangan</label>
                <input type="text" name="namaRuangan" class="form-control"
                    value="{{ old('namaRuangan', $ruangan->namaRuangan ?? '') }}" 
                    {{ $mode == 'show' ? 'readonly' : '' }}>
            </div>

            <div class="form-group">
                <label>Kapasitas</label>
                <input type="number" name="kapasitas" class="form-control"
                    value="{{ old('kapasitas', $ruangan->kapasitas ?? '') }}"
                    {{ $mode == 'show' ? 'readonly' : '' }}>
            </div>

            <div class="form-group">
                <label>Fasilitas</label>
                <textarea name="fasilitas" class="form-control" {{ $mode == 'show' ? 'readonly' : '' }}>{{ old('fasilitas', $ruangan->fasilitas ?? '') }}</textarea>
            </div>

            <div class="form-group">
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
                    <button type="submit" class="btn btn-success">
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
<a href="{{ route('admin.ruangan.create') }}" class="btn btn-primary btn-sm">
    <i class="fas fa-plus"></i> Tambah Ruangan
</a>

<div class="table-container mt-3">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Ruangan</th>
                <th>Kapasitas</th>
                <th>Fasilitas</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ruangan as $item)
                <tr>
                    <td><strong>{{ $item->namaRuangan }}</strong></td>
                    <td>{{ $item->kapasitas }} orang</td>
                    <td>{{ $item->fasilitas ?? '-' }}</td>
                    <td>
                        <span class="status-badge status-{{ Str::slug($item->status) }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.ruangan.show', $item->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Detail</a>
                        <a href="{{ route('admin.ruangan.edit', $item->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                        <form action="{{ route('admin.ruangan.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus ruangan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada data ruangan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

<style>
.status-badge {
    padding: 5px 10px;
    border-radius: 6px;
    font-weight: 600;
}
.status-tersedia { background: #d4edda; color: #155724; }
.status-dipinjam { background: #f8d7da; color: #721c24; }
.status-perawatan { background: #fff3cd; color: #856404; }
</style>
@endsection