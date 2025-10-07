<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;

class AdminPeminjamanController extends Controller
{
    /**
     * Menampilkan daftar semua peminjaman untuk admin
     */
    public function index()
    {
        $peminjaman = Peminjaman::with(['user', 'ruangan', 'unit'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.peminjaman.index', compact('peminjaman'));
    }

    /**
     * Mengubah status peminjaman (disetujui / ditolak / selesai)
     */
    public function updateStatus(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // Tentukan aksi berdasarkan URL
        if ($request->is('admin/peminjaman/*/approve')) {
            $peminjaman->status = 'disetujui';
        } elseif ($request->is('admin/peminjaman/*/reject')) {
            $peminjaman->status = 'ditolak';
        } elseif ($request->is('admin/peminjaman/*/complete')) {
            $peminjaman->status = 'selesai';
        } else {
            $peminjaman->status = 'pending';
        }

        $peminjaman->save();

        return redirect()->back()->with('success', 'Status peminjaman berhasil diperbarui.');
    }
}