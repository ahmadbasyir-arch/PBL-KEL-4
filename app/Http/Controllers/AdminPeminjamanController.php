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

        // Pastikan hanya status tertentu yang bisa diubah oleh admin
        $validStatuses = ['approve', 'reject', 'complete'];

        // Cek apakah URL cocok dengan salah satu aksi
        if ($request->is('admin/peminjaman/*/approve')) {
            // ✅ Jika disetujui
            $peminjaman->status = 'disetujui';

            // Jika peminjaman ruangan/unit, ubah status ketersediaannya
            if ($peminjaman->ruangan) {
                $peminjaman->ruangan->update(['status' => 'dipinjam']);
            }
            if ($peminjaman->unit) {
                $peminjaman->unit->update(['status' => 'dipinjam']);
            }

            $pesan = 'Peminjaman telah disetujui.';
        } elseif ($request->is('admin/peminjaman/*/reject')) {
            // ❌ Jika ditolak
            $peminjaman->status = 'ditolak';

            // Jika sebelumnya ruangan/unit sempat dipesan, pastikan dikembalikan jadi tersedia
            if ($peminjaman->ruangan) {
                $peminjaman->ruangan->update(['status' => 'tersedia']);
            }
            if ($peminjaman->unit) {
                $peminjaman->unit->update(['status' => 'tersedia']);
            }

            $pesan = 'Peminjaman telah ditolak.';
        } elseif ($request->is('admin/peminjaman/*/complete')) {
            // ✅ Jika sudah selesai digunakan
            if ($peminjaman->status === 'disetujui') {
                $peminjaman->status = 'selesai';

                // Update ketersediaan ruangan/unit setelah selesai
                if ($peminjaman->ruangan) {
                    $peminjaman->ruangan->update(['status' => 'tersedia']);
                }
                if ($peminjaman->unit) {
                    $peminjaman->unit->update(['status' => 'tersedia']);
                }

                $pesan = 'Peminjaman telah diselesaikan.';
            } else {
                return redirect()->back()->with('error', 'Hanya peminjaman yang disetujui yang dapat diselesaikan.');
            }
        } else {
            $peminjaman->status = 'pending';
            $pesan = 'Status peminjaman dikembalikan ke pending.';
        }

        $peminjaman->save();

        return redirect()->back()->with('success', $pesan);
    }
}
