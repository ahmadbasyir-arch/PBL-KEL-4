@extends('layouts.app')

@section('title', 'Manajemen Data Unit')

@section('content')
<div class="main-container">
    <div class="section-header mb-4">
        <h1>Manajemen Data Unit</h1>
        <p>Kelola data unit peralatan dengan ringkas.</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    {{-- ============ MODE TAMBAH / EDIT / DETAIL ============ --}}
    @if (isset($mode) && in_array($mode, ['create', 'edit', 'show']))
        <div class="card p-4 shadow-sm rounded-3">
            <h3 class="mb-3">
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

                <div class="form-group mb-3">
                    <label>Kode Unit</label>
                    <input type="text" name="kodeUnit" class="form-control"
                        value="{{ old('kodeUnit', $unit->kodeUnit ?? '') }}" 
                        {{ $mode == 'show' ? 'readonly' : '' }}>
                </div>

                <div class="form-group mb-3">
                    <label>Nama Unit</label>
                    <input type="text" name="namaUnit" class="form-control"
                        value="{{ old('namaUnit', $unit->namaUnit ?? '') }}" 
                        {{ $mode == 'show' ? 'readonly' : '' }}>
                </div>

                <div class="form-group mb-3">
                    <label>Kategori</label>
                    <input type="text" name="kategori" class="form-control"
                        value="{{ old('kategori', $unit->kategori ?? '') }}" 
                        {{ $mode == 'show' ? 'readonly' : '' }}>
                </div>

                <div class="form-group mb-3">
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
                        <button type="submit" class="btn btn-success shadow-sm">
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
    {{-- ============ MODE INDEX (DAFTAR) ============ --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Daftar Unit</h3>
        <a href="{{ route('admin.unit.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus"></i> Tambah Unit
        </a>
    </div>

    <div class="card p-3 shadow-sm rounded-3">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode Unit</th>
                    <th>Nama Unit</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th style="width:180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($unit as $item)
                    <tr>
                        <td><strong>{{ $item->kodeUnit }}</strong></td>
                        <td>{{ $item->namaUnit }}</td>
                        <td>{{ $item->kategori ?? '-' }}</td>
                        <td>
                            <span class="status-badge status-{{ Str::slug($item->status) }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.unit.show', $item->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.unit.edit', $item->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.unit.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus unit ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data unit.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>

<style>
.main-container {
    max-width: 1250px;
    margin: 0 auto;
    padding: 20px;
}
.data-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
}
.data-table th {
    background: #f8f9fb;
    padding: 14px;
    text-align: left;
    font-weight: 700;
    color: #333;
}
.data-table td {
    padding: 14px;
    border-top: 1px solid #eee;
    color: #333;
}
.data-table tbody tr:hover { background: #fafafa; transition: 0.2s; }
.action-buttons { display: flex; gap: 6px; }
.status-badge {
    padding: 6px 12px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
}
.status-tersedia { background: #d4edda; color: #155724; }
.status-dipinjam { background: #f8d7da; color: #721c24; }
.status-perawatan { background: #fff3cd; color: #856404; }
</style>
@endsection