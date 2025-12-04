@extends('layouts.app')

@section('title', 'Manajemen Data Ruangan')

@section('content')
    <div class="welcome-banner">
        <h1>Manajemen Data Ruangan</h1>
        <p>Kelola data ruangan kampus dengan mudah dan efisien.</p>
    </div>

    @if (isset($mode) && in_array($mode, ['create', 'edit', 'show']))
        <div class="interactive-table">
            <div class="section-header" style="margin-bottom: 20px;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 10px;">
                    @if ($mode == 'create')
                        <i class="fas fa-plus-circle" style="color: #3b82f6;"></i> Tambah Ruangan Baru
                    @elseif ($mode == 'edit')
                        <i class="fas fa-edit" style="color: #f59e0b;"></i> Edit Ruangan
                    @else
                        <i class="fas fa-info-circle" style="color: #0ea5e9;"></i> Detail Ruangan
                    @endif
                </h2>
            </div>

            <form 
                action="{{ $mode == 'create' ? route('admin.ruangan.store') : ($mode == 'edit' ? route('admin.ruangan.update', $ruangan->id) : '#') }}" 
                method="POST">
                @csrf
                @if ($mode == 'edit')
                    @method('PUT')
                @endif

                <div class="form-group mb-3">
                    <label style="font-weight: 600; color: #374151; margin-bottom: 5px; display: block;">Nama Ruangan</label>
                    <input type="text" name="namaRuangan" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;"
                        value="{{ old('namaRuangan', $ruangan->namaRuangan ?? '') }}" 
                        {{ $mode == 'show' ? 'readonly' : '' }}>
                </div>

                <div class="form-group mb-3">
                    <label style="font-weight: 600; color: #374151; margin-bottom: 5px; display: block;">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;"
                        value="{{ old('lokasi', $ruangan->lokasi ?? '') }}"
                        {{ $mode == 'show' ? 'readonly' : '' }}>
                </div>

                <div class="form-group mb-3">
                    <label style="font-weight: 600; color: #374151; margin-bottom: 5px; display: block;">Kapasitas</label>
                    <input type="number" name="kapasitas" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;"
                        value="{{ old('kapasitas', $ruangan->kapasitas ?? '') }}"
                        {{ $mode == 'show' ? 'readonly' : '' }}>
                </div>

                <div class="form-group mb-3">
                    <label style="font-weight: 600; color: #374151; margin-bottom: 5px; display: block;">Status</label>
                    <select name="status" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;" {{ $mode == 'show' ? 'disabled' : '' }}>
                        @foreach (['tersedia', 'dipinjam', 'perawatan'] as $status)
                            <option value="{{ $status }}" 
                                {{ (old('status', $ruangan->status ?? '') == $status) ? 'selected' : '' }}>
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
                    <a href="{{ route('admin.ruangan.index') }}" class="btn btn-secondary" style="background-color: #6b7280; border: none; padding: 10px 20px; border-radius: 6px; color: white; font-weight: 600; text-decoration: none;">
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
                <i class="fas fa-door-open" style="color: #3b82f6;"></i> Daftar Ruangan
            </h2>
            <a href="{{ route('admin.ruangan.create') }}" class="btn btn-primary shadow-sm" style="background-color: #3b82f6; border: none; padding: 8px 16px; border-radius: 6px; color: white; font-weight: 600; text-decoration: none; font-size: 0.9rem;">
                <i class="fas fa-plus"></i> Tambah Ruangan
            </a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Ruangan</th>
                    <th>Lokasi</th>
                    <th>Kapasitas</th>
                    <th>Status</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ruangan as $item)
                    <tr>
                        <td><strong>{{ $item->namaRuangan }}</strong></td>
                        <td>{{ $item->lokasi ?? '-' }}</td>
                        <td>{{ $item->kapasitas }} orang</td>
                        <td>
                            <span class="status-badge status-{{ Str::slug($item->status ?: 'tersedia') }}">
                                {{ ucfirst($item->status ?: 'tersedia') }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <div class="action-buttons" style="justify-content: center; display: flex; gap: 5px;">
                                <a href="{{ route('admin.ruangan.show', $item->id) }}" class="btn btn-info btn-sm" style="background-color: #0ea5e9; border: none; color: white; padding: 5px 10px; border-radius: 4px;"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.ruangan.edit', $item->id) }}" class="btn btn-warning btn-sm" style="background-color: #f59e0b; border: none; color: white; padding: 5px 10px; border-radius: 4px;"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.ruangan.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus ruangan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="background-color: #ef4444; border: none; color: white; padding: 5px 10px; border-radius: 4px;"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3" style="text-align: center; padding: 20px; color: #6b7280;">Belum ada data ruangan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
@endsection