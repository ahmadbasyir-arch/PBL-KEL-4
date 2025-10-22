<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ruangan;
use App\Models\Unit;
use App\Models\Peminjaman;

class PeminjamanController extends Controller
{
    public function create(Request $request)
    {
        $jenis = $request->query('jenis', 'ruangan');
        $listData = $jenis === 'unit'
            ? Unit::orderBy('namaUnit', 'asc')->get(['id', 'namaUnit'])
            : Ruangan::orderBy('namaRuangan', 'asc')->get(['id', 'namaRuangan']);

        $user = Auth::user(); // ✅ Tambahkan agar role bisa diketahui di view

        return view('mahasiswa.peminjaman_form', compact('jenis', 'listData', 'user'));
    }

    public function store(Request $request)
    {
        $jenis = $request->input('jenis_item');

        $validated = $request->validate([
            'jenis_item'     => 'required|in:ruangan,unit',
            'tanggalPinjam'  => 'required|date|after_or_equal:today',
            'jamMulai'       => 'required',
            'jamSelesai'     => 'required|after:jamMulai',
            'keperluan'      => 'required|string|max:255',
            'items'          => 'required|array|min:1',
            'items.*.id'     => $jenis === 'unit'
                ? 'required|integer|exists:unit,id'
                : 'required|integer|exists:ruangan,id',
        ]);

        $user = Auth::user();

        // 🔧 Sekarang mahasiswa & dosen boleh melakukan peminjaman
        if (!in_array($user->role, ['mahasiswa', 'dosen'])) {
            return redirect()->back()->with('error', 'Hanya mahasiswa atau dosen yang dapat melakukan peminjaman.');
        }

        foreach ($validated['items'] as $item) {
            Peminjaman::create([
                'idMahasiswa'   => $user->id, // tetap kolom ini
                'idRuangan'     => $jenis === 'ruangan' ? $item['id'] : null,
                'idUnit'        => $jenis === 'unit' ? $item['id'] : null,
                'tanggalPinjam' => $validated['tanggalPinjam'],
                'jamMulai'      => $validated['jamMulai'],
                'jamSelesai'    => $validated['jamSelesai'],
                'keperluan'     => $validated['keperluan'],
                'status'        => 'pending',
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Pengajuan peminjaman berhasil dikirim!');
    }

    public function ajukanSelesai($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if (in_array(strtolower($peminjaman->status), ['disetujui', 'digunakan', 'sedang digunakan'])) {
            $peminjaman->status = 'menunggu_validasi';
            $peminjaman->save();

            return redirect()->back()->with('success', 'Pengajuan selesai berhasil dikirim. Menunggu validasi admin.');
        }

        return redirect()->back()->with('error', 'Peminjaman ini belum dapat diajukan selesai.');
    }
}