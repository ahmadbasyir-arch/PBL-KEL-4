@extends('layouts.app')

@section('title', 'Manajemen Data Unit')

@section('content')
<div class="section-header">
    <h1>Manajemen Data Unit</h1>
</div>

@if (session('success'))
    <div class="success-message">{{ session('success') }}</div>
@endif

@if (isset($mode) && in_array($mode, ['create', 'edit', 'show']))
    <div class="card p-4">
        <h3>
            @if ($mode == 'create')
                Tambah Unit Baru
            @elseif ($mode == 'edit')
                Edit Unit
            @else
                Detail Unit
            @endif
        </h3>
        <form 
            action="{{ $mode == 'create' ? route('admin.unit.store') : ($mode == 'edit' ? route('admin.unit.update', $unit->id) : '#') }}" 
            method="POST">
            @csrf
            @if ($mode == 'edit')
                @method('PUT')
            @endif

            <div class="form-group">
                <label>Nama Unit</label>
                <input type="text" name="namaUnit" class="form-control"
                    value="{{ old('namaUnit', $unit->namaUnit ?? '') }}" 
                    {{ $mode == 'show' ? 'readonly' : '' }}>
            </div>

            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" name="jumlah" class="form-control"
                    value="{{ old('jumlah', $unit->jumlah ?? '') }}"
                    {{ $mode == 'show' ? 'readonly' : '' }}>
            </div>

            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control" {{ $mode == 'show' ? 'readonly' : '' }}>{{ old('keterangan', $unit->keterangan ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control" {{ $mode == 'show' ? 'disabled' : '' }}>
                    @foreach (['tersedia', 'dipinjam', 'perawatan'] as $status)
                        <option value="{{ $status }}" 
                            {{ (old('status', $unit->status ?? '') == $status) ? 'selected' : '' }}>
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
                <a href="{{ route('admin.unit.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
@else
<a href="{{ route('admin.unit.create') }}" class="btn btn-primary btn-sm">
    <i class="fas fa-plus"></i> Tambah Unit
</a>

<div class="table-container mt-3">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Unit</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($unit as $item)
                <tr>
                    <td><strong>{{ $item->namaUnit }}</strong></td>
                    <td>{{ $item->jumlah }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                    <td>
                        <span class="status-badge status-{{ Str::slug($item->status) }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.unit.show', $item->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Detail</a>
                        <a href="{{ route('admin.unit.edit', $item->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                        <form action="{{ route('admin.unit.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus unit ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada data unit.</td>
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