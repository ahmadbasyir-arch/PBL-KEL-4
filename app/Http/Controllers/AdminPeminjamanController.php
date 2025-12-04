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

    /**
     * -----------------------------
     * HANDLE SETUJU / TOLAK / COMPLETE
     * -----------------------------
     */
    public function updateStatus(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // Tentukan aksi berdasarkan route name
        $action = $request->route()->getName();

        if ($action === 'admin.peminjaman.approve') {
            $peminjaman->status = 'disetujui';
            
            // Update status unit menjadi 'digunakan' jika ada unit yang dipinjam
            if ($peminjaman->unit) {
                $peminjaman->unit->update(['status' => 'digunakan']);
            }

            // Update status ruangan menjadi 'digunakan' jika ada ruangan yang dipinjam
            if ($peminjaman->ruangan) {
                $peminjaman->ruangan->update(['status' => 'digunakan']);
            }
        } 
        elseif ($action === 'admin.peminjaman.reject') {
            $peminjaman->status = 'ditolak';
        } 
        elseif ($action === 'admin.peminjaman.complete') {
            $peminjaman->status = 'menyelesaikan';
        }

        $peminjaman->save();

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    /**
     * FORM VALIDASI UNIT / RUANGAN
     */
    public function formValidasi($id)
    {
        $peminjaman = Peminjaman::with(['unit', 'ruangan'])->findOrFail($id);

        if ($peminjaman->ruangan && !$peminjaman->unit) {
            return $this->validateSelesaiDirect($peminjaman);
        }

        return view('admin.peminjaman.validasi', compact('peminjaman'));
    }

    /**
     * VALIDASI SELESAI (POST)
     */
    public function validateSelesai(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $isUnit = !empty($peminjaman->idUnit);

        if ($isUnit) {
            $request->validate([
                'kondisi' => 'required|string|max:50',
                'catatan' => 'nullable|string|max:255',
            ]);
        }

        if (!in_array($peminjaman->status, ['menunggu_validasi', 'menyelesaikan'])) {
            return redirect()->back()->with('error', 'Peminjaman ini belum diajukan penyelesaian oleh mahasiswa.');
        }

        if ($isUnit) {
            $peminjaman->kondisi_pengembalian = $request->kondisi;
            $peminjaman->catatan_pengembalian = $request->catatan;
        }

        $peminjaman->status = 'selesai';
        $peminjaman->save();

        Pengembalian::create([
            'idPeminjaman' => $peminjaman->id,
            'tanggalKembali' => now(),
            'kondisi' => $isUnit ? $request->kondisi : 'Baik',
            'catatan' => $isUnit ? $request->catatan : null,
        ]);

        if ($peminjaman->unit) {
            $peminjaman->unit->update(['status' => 'tersedia']);
        }

        if ($peminjaman->ruangan) {
            $peminjaman->ruangan->update(['status' => 'tersedia']);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Peminjaman berhasil divalidasi sebagai selesai.');
    }

    /**
     * AUTO SELESAI UNTUK RUANGAN
     */
    private function validateSelesaiDirect($peminjaman)
    {
        $peminjaman->status = 'selesai';
        $peminjaman->save();

        Pengembalian::create([
            'idPeminjaman' => $peminjaman->id,
            'tanggalKembali' => now(),
            'kondisi' => 'Baik',
            'catatan' => null
        ]);

        if ($peminjaman->ruangan) {
            $peminjaman->ruangan->update(['status' => 'tersedia']);
        }


        return redirect()->route('admin.dashboard')
            ->with('success', 'Peminjaman ruangan selesai tanpa validasi kondisi.');
    }
}