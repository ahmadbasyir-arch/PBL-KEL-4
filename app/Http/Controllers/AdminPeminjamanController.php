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
     * Mengubah status peminjaman (disetujui / ditolak / digunakan / selesai)
     */
    public function updateStatus(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // --- Aksi SETUJU ---
        if ($request->is('admin/peminjaman/*/approve')) {
            // Hanya bisa menyetujui jika status masih pending
            if ($peminjaman->status === 'pending') {
                $peminjaman->status = 'digunakan';

                if ($peminjaman->ruangan) {
                    $peminjaman->ruangan->update(['status' => 'dipinjam']);
                }
                if ($peminjaman->unit) {
                    $peminjaman->unit->update(['status' => 'dipinjam']);
                }

                $pesan = 'Peminjaman telah disetujui dan sedang digunakan.';
            } else {
                return redirect()->back()->with('error', 'Peminjaman ini tidak dapat disetujui lagi.');
            }

        // --- Aksi TOLAK ---
        } elseif ($request->is('admin/peminjaman/*/reject')) {
            if (in_array($peminjaman->status, ['pending', 'menyelesaikan'])) {
                $peminjaman->status = 'ditolak';

                if ($peminjaman->ruangan) {
                    $peminjaman->ruangan->update(['status' => 'tersedia']);
                }
                if ($peminjaman->unit) {
                    $peminjaman->unit->update(['status' => 'tersedia']);
                }

                $pesan = 'Peminjaman telah ditolak.';
            } else {
                return redirect()->back()->with('error', 'Tidak dapat menolak peminjaman ini.');
            }

        // --- Aksi SELESAI ---
        } elseif ($request->is('admin/peminjaman/*/complete')) {
            // Hanya bisa diselesaikan jika mahasiswa sudah mengajukan selesai
            if ($peminjaman->status === 'menyelesaikan') {
                $peminjaman->status = 'selesai';

                if ($peminjaman->ruangan) {
                    $peminjaman->ruangan->update(['status' => 'tersedia']);
                }
                if ($peminjaman->unit) {
                    $peminjaman->unit->update(['status' => 'tersedia']);
                }

                $pesan = 'Peminjaman telah diselesaikan.';
            } else {
                return redirect()->back()->with('error', 'Mahasiswa belum mengajukan penyelesaian.');
            }

        } else {
            $pesan = 'Tidak ada aksi valid.';
        }

        $peminjaman->save();

        return redirect()->back()->with('success', $pesan);
    }

    /**
     * ✅ Admin memvalidasi peminjaman yang diajukan selesai oleh mahasiswa.
     */
    public function validateSelesai($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // Pastikan statusnya adalah "menunggu_validasi" sebelum bisa diselesaikan
        if ($peminjaman->status !== 'menunggu_validasi') {
            return redirect()->back()->with('error', 'Peminjaman ini belum diajukan penyelesaian oleh mahasiswa.');
        }

        $peminjaman->status = 'selesai';

        // Setelah selesai, ubah status ruangan/unit kembali tersedia
        if ($peminjaman->ruangan) {
            $peminjaman->ruangan->update(['status' => 'tersedia']);
        }
        if ($peminjaman->unit) {
            $peminjaman->unit->update(['status' => 'tersedia']);
        }

        $peminjaman->save();

        return redirect()->back()->with('success', 'Peminjaman telah divalidasi dan dinyatakan selesai.');
    }
}