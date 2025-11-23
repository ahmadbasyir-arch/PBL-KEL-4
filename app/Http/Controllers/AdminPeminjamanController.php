<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Pengembalian;

class AdminPeminjamanController extends Controller
{
    public function index()
    {
        $peminjaman = Peminjaman::with(['mahasiswa', 'ruangan', 'unit'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.peminjaman.index', compact('peminjaman'));
    }

    public function updateStatus(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | APPROVE
        |--------------------------------------------------------------------------
        */
        if ($request->is('admin/peminjaman/*/approve')) {

            if ($peminjaman->status === 'pending') {

                // STATUS DISETUJUI
                $peminjaman->status = 'disetujui';

                // RUANGAN / UNIT DISET DIPINJAM
                if ($peminjaman->ruangan) {
                    $peminjaman->ruangan->update(['status' => 'dipinjam']);
                }
                if ($peminjaman->unit) {
                    $peminjaman->unit->update(['status' => 'dipinjam']);
                }

                $pesan = 'Peminjaman telah disetujui.';
            } else {
                return redirect()->back()->with('error', 'Peminjaman ini tidak dapat disetujui lagi.');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | REJECT
        |--------------------------------------------------------------------------
        */
        elseif ($request->is('admin/peminjaman/*/reject')) {

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
        }

        /*
        |--------------------------------------------------------------------------
        | COMPLETE
        |--------------------------------------------------------------------------
        */
        elseif ($request->is('admin/peminjaman/*/complete')) {

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
        }

        else {
            $pesan = 'Tidak ada aksi valid.';
        }

        $peminjaman->save();

        return redirect()->back()->with('success', $pesan);
    }

    /**
     * VALIDASI SELESAI
     */
    public function validateSelesai(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $request->validate([
            'kondisi' => 'required|string|max:50',
            'catatan' => 'nullable|string|max:255',
        ]);

        if (!in_array($peminjaman->status, ['menunggu_validasi', 'menyelesaikan'])) {
            return redirect()->back()->with('error', 'Peminjaman ini belum diajukan penyelesaian oleh mahasiswa.');
        }

        $peminjaman->kondisi_pengembalian = $request->kondisi;
        $peminjaman->catatan_pengembalian = $request->catatan;
        $peminjaman->status = 'selesai';
        $peminjaman->save();

        Pengembalian::create([
            'idPeminjaman' => $peminjaman->id,
            'tanggal_pengembalian' => now(),
            'kondisi' => $request->kondisi,
            'catatan' => $request->catatan,
        ]);

        if ($peminjaman->ruangan) {
            $peminjaman->ruangan->update(['status' => 'tersedia']);
        }
        if ($peminjaman->unit) {
            $peminjaman->unit->update(['status' => 'tersedia']);
        }

        return redirect()->back()->with('success', 'Peminjaman telah divalidasi dan dicatat sebagai selesai.');
    }
}
