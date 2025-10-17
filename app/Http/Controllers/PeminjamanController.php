<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ruangan;
use App\Models\Unit;
use App\Models\Peminjaman;

class PeminjamanController extends Controller
{
    /**
     * Menampilkan formulir peminjaman baru.
     */
    public function create(Request $request)
    {
        // Ambil parameter jenis (default = ruangan)
        $jenis = $request->query('jenis', 'ruangan');

        // Default listData kosong (gunakan collection, bukan array biasa)
        $listData = collect();

        // 🔹 Ambil data dari model
        if ($jenis === 'ruangan') {
            $listData = Ruangan::orderBy('namaRuangan', 'asc')->get(['id', 'namaRuangan']);
        } elseif ($jenis === 'unit') {
            $listData = Unit::orderBy('namaUnit', 'asc')->get(['id', 'namaUnit']);
        }

        // Kirim data ke view
        return view('mahasiswa.peminjaman_form', compact('jenis', 'listData'));
    }

    /**
     * Menyimpan data peminjaman baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_item'   => 'required|in:ruangan,unit',
            'tanggalPinjam' => 'required|date|after_or_equal:today',
            'jamMulai'      => 'required',
            'jamSelesai'    => 'required|after:jamMulai',
            'keperluan'     => 'required|string|max:255',
            'items'         => 'required|array',
            'items.*.id'    => 'required|integer|exists:ruangan,id',
        ]);

$user = Auth::user();
$idMahasiswa = $user->mahasiswa->id; // ambil ID dari relasi mahasiswa

foreach ($validated['items'] as $item) {
    Peminjaman::create([
        'idMahasiswa'  => $idMahasiswa,
        'idRuangan'    => $validated['jenis_item'] === 'ruangan' ? $item['id'] : null,
        'idUnit'       => $validated['jenis_item'] === 'unit' ? $item['id'] : null,
        'tanggalPinjam'=> $validated['tanggalPinjam'],
        'jamMulai'     => $validated['jamMulai'],
        'jamSelesai'   => $validated['jamSelesai'],
        'keperluan'    => $validated['keperluan'],
        'status'       => 'pending',
    ]);
}


        return redirect()
            ->route('dashboard')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim!');
    }
}