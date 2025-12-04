@extends('layouts.app')

@section('title', 'Manajemen Data Unit')

@section('content')
    <div class="welcome-banner">
        <h1>Manajemen Data Unit</h1>
        <p>Kelola data unit peralatan dengan ringkas.</p>
    </div>

    @if (isset($mode) && in_array($mode, ['create', 'edit', 'show']))
        <div class="interactive-table">
            <div class="section-header" style="margin-bottom: 20px;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 10px;">
                    @if ($mode == 'create')
                        <i class="fas fa-plus-circle" style="color: #3b82f6;"></i> Tambah Unit Baru
                    @elseif ($mode == 'edit')
                        <i class="fas fa-edit" style="color: #f59e0b;"></i> Edit Unit
                    @else
                        <i class="fas fa-info-circle" style="color: #0ea5e9;"></i> Detail Unit
                    @endif
                </h2>
            </div>

            <form 
                action="{{ $mode == 'create' ? route('admin.unit.store') : ($mode == 'edit' ? route('admin.unit.update', $unit->id) : '#') }}" 
                method="POST">
                @csrf
                @if ($mode == 'edit')
                    @method('PUT')
                @endif

                <div class="form-group mb-3">
                    <label style="font-weight: 600; color: #374151; margin-bottom: 5px; display: block;">Kode Unit</label>
                    <input type="text" name="kodeUnit" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;"
                        value="{{ old('kodeUnit', $unit->kodeUnit ?? '') }}" 
                        {{ $mode == 'show' ? 'readonly' : '' }}>
                </div>

                <div class="form-group mb-3">
                    <label style="font-weight: 600; color: #374151; margin-bottom: 5px; display: block;">Nama Unit</label>
                    <input type="text" name="namaUnit" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;"
                        value="{{ old('namaUnit', $unit->namaUnit ?? '') }}" 
                        {{ $mode == 'show' ? 'readonly' : '' }}>
                </div>

                <div class="form-group mb-3">
                    <label style="font-weight: 600; color: #374151; margin-bottom: 5px; display: block;">Kategori</label>
                    <input type="text" name="kategori" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;"
                        value="{{ old('kategori', $unit->kategori ?? '') }}" 
                        {{ $mode == 'show' ? 'readonly' : '' }}>
                </div>

                <div class="form-group mb-3">
                    <label style="font-weight: 600; color: #374151; margin-bottom: 5px; display: block;">Status</label>
                    <select name="status" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;" {{ $mode == 'show' ? 'disabled' : '' }}>
                        @foreach (['tersedia', 'dipinjam', 'perawatan'] as $status)
                            <option value="{{ $status }}" 
                                {{ (old('status', $unit->status ?? '') == $status) ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4" style="display: flex; gap: 10px;">
                    @if ($mode != 'show')
                        <button type="submit" class="btn btn-success shadow-sm" style="background-color: #10b981; border: none; padding: 10px 20px; border-radius: 6px; color: white; font-weight: 600;">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    @endif
                    <a href="{{ route('admin.unit.index') }}" class="btn btn-secondary" style="background-color: #6b7280; border: none; padding: 10px 20px; border-radius: 6px; color: white; font-weight: 600; text-decoration: none;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    @else
    {{-- ============ MODE INDEX (DAFTAR) ============ --}}
    <div class="interactive-table">
        <div class="section-header" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 10px; margin: 0;">
                <i class="fas fa-video" style="color: #3b82f6;"></i> Daftar Unit
            </h2>
            <a href="{{ route('admin.unit.create') }}" class="btn btn-primary shadow-sm" style="background-color: #3b82f6; border: none; padding: 8px 16px; border-radius: 6px; color: white; font-weight: 600; text-decoration: none; font-size: 0.9rem;">
                <i class="fas fa-plus"></i> Tambah Unit
            </a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode Unit</th>
                    <th>Nama Unit</th>
                    <th>Status</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($unit as $item)
                    <tr>
                        <td><strong>{{ $item->kodeUnit }}</strong></td>
                        <td>{{ $item->namaUnit }}</td>
                        <td>
                            <span class="status-badge status-{{ Str::slug($item->status ?: 'tersedia') }}">
                                {{ ucfirst($item->status ?: 'tersedia') }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <div class="action-buttons" style="justify-content: center; display: flex; gap: 5px;">
                                <a href="{{ route('admin.unit.show', $item->id) }}" class="btn btn-info btn-sm" style="background-color: #0ea5e9; border: none; color: white; padding: 5px 10px; border-radius: 4px;"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.unit.edit', $item->id) }}" class="btn btn-warning btn-sm" style="background-color: #f59e0b; border: none; color: white; padding: 5px 10px; border-radius: 4px;"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.unit.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus unit ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="background-color: #ef4444; border: none; color: white; padding: 5px 10px; border-radius: 4px;"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3" style="text-align: center; padding: 20px; color: #6b7280;">Belum ada data unit.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
@endsection