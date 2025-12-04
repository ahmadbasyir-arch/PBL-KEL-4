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
     * TOMBOL VALIDASI — arahkan ke halaman form jika Unit
     */
    public function formValidasi($id)
    {
        $peminjaman = Peminjaman::with(['unit', 'ruangan'])->findOrFail($id);

        // jika ruangan → tidak perlu form → langsung selesai
        if ($peminjaman->ruangan && !$peminjaman->unit) {
            return $this->validateSelesaiDirect($peminjaman);
        }

        // jika unit → tampilkan form validasi
        return view('admin.peminjaman.validasi', compact('peminjaman'));
    }

    /**
     * VALIDASI SELESAI (POST)
     */
    public function validateSelesai(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $isUnit = !empty($peminjaman->idUnit);

        // validasi hanya jika Unit
        if ($isUnit) {
            $request->validate([
                'kondisi' => 'required|string|max:50',
                'catatan' => 'nullable|string|max:255',
            ]);
        }

        if (!in_array($peminjaman->status, ['menunggu_validasi', 'menyelesaikan'])) {
            return redirect()->back()->with('error', 'Peminjaman ini belum diajukan penyelesaian oleh mahasiswa.');
        }

        // simpan kondisi (jika unit)
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

        // update stok
        if ($peminjaman->ruangan) {
            $peminjaman->ruangan->update(['status' => 'tersedia']);
        }
        if ($peminjaman->unit) {
            $peminjaman->unit->update(['status' => 'tersedia']);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Peminjaman berhasil divalidasi sebagai selesai.');
    }

    /**
     * Jika Ruangan → auto selesai
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

