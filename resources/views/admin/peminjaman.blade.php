@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Data Peminjaman</h2>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Mahasiswa</th>
                <th>Ruangan</th>
                <th>Keperluan</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->mahasiswa->nama ?? '-' }}</td>
                <td>{{ $item->ruangan->nama ?? '-' }}</td>
                <td>{{ $item->keperluan }}</td>
                <td>{{ $item->tanggal_pinjam }}</td>
                <td>{{ $item->status }}</td>
                <td>
                    @if($item->status === 'Menunggu')
                        <form action="{{ route('admin.setujui', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm">Setujui</button>
                        </form>
                        <form action="{{ route('admin.tolak', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-danger btn-sm">Tolak</button>
                        </form>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
            </tr>
            @empty
                <tr><td colspan="7" class="text-center">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection