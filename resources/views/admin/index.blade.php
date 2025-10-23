@extends('layouts.app')

@section('title', 'Manajemen Data Ruangan')

@section('content')
    <div class="section-header">
        <h1>Manajemen Data Ruangan</h1>
    </div>

    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    <div class="interactive-table">
        <div class="section-header">
            <h2>Daftar Ruangan</h2>
            {{-- Tombol ini akan mengarah ke form tambah ruangan (akan kita buat nanti) --}}
            <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Ruangan</a>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Ruangan</th>
                        <th>Kapasitas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ruangan as $item)
                        <tr>
                            <td><strong>{{ $item->namaRuangan }}</strong></td>
                            <td>{{ $item->kapasitas }} orang</td>
                            <td>
                                {{-- tampilkan status ruangan apa adanya (tersedia / dipinjam / dll) --}}
                                <span class="status-badge status-{{ \Illuminate\Support\Str::slug($item->status) }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>
                                {{-- Tombol Edit dan Hapus (akan kita fungsikan nanti) --}}
                                <a href="#" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                <form action="#" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ruangan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data ruangan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection