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

        return view('mahasiswa.peminjaman_form', compact('jenis', 'listData'));
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
        $idMahasiswa = $user->mahasiswa->id ?? null;

        if (!$idMahasiswa) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        foreach ($validated['items'] as $item) {
            Peminjaman::create([
                'idMahasiswa'   => $idMahasiswa,
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

    // ==================================================
    // Admin: update status (setuju/tolak/complete)
    // ==================================================
    public function updateStatus(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $request->validate([
            'status' => 'required|string',
            'kondisi_pengembalian' => 'nullable|string',
        ]);

        $peminjaman->status = strtolower($request->input('status'));
        $peminjaman->kondisi_pengembalian = $request->input('kondisi_pengembalian');
        $peminjaman->save();

        return redirect()->back()->with('success', 'Status peminjaman berhasil diperbarui.');
    }

    // ==================================================
    // Mahasiswa: ajukan selesai
    // ==================================================
    public function ajukanSelesai($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // hanya boleh ajukan selesai jika statusnya disetujui / digunakan
        if (in_array(strtolower($peminjaman->status), ['disetujui', 'digunakan', 'sedang digunakan'])) {
            $peminjaman->status = 'menunggu_validasi';
            $peminjaman->save();

            return redirect()->back()->with('success', 'Pengajuan selesai berhasil dikirim. Menunggu validasi admin.');
        }

        return redirect()->back()->with('error', 'Peminjaman ini belum dapat diajukan selesai.');
    }
}