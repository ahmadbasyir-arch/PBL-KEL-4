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
        $peminjaman = Peminjaman::latest()->get();
        return view('admin.peminjaman.index', compact('peminjaman'));
    }

    /**
     * Mengubah status peminjaman (disetujui / ditolak)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
        ]);

        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status = strtolower($request->status);
        $peminjaman->save();

        return redirect()->back()->with('success', 'Status peminjaman berhasil diperbarui.');
    }
}