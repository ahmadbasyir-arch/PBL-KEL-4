<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    /**
     * Menampilkan formulir untuk membuat peminjaman baru.
     */
    public function create(Request $request)
    {
        $jenis = $request->query('jenis', 'ruangan');
        $listData = [];

        if ($jenis == 'ruangan') {
            $listData = DB::table('ruangan')->orderBy('namaRuangan', 'asc')->get();
        } elseif ($jenis == 'unit') {
            $listData = DB::table('unit')->orderBy('namaUnit', 'asc')->get();
        }

        return view('mahasiswa.peminjaman_form', [
            'jenis' => $jenis,
            'listData' => $listData
        ]);
    }

    /**
     * [PERBAIKAN UTAMA] Menyimpan data peminjaman baru ke dalam database.
     */
    public function store(Request $request)
    {
        // 1. Validasi semua data yang masuk dari formulir
        $validated = $request->validate([
            'jenis_item' => 'required|in:ruangan,unit',
            'tanggalPinjam' => 'required|date|after_or_equal:today',
            'jamMulai' => 'required',
            'jamSelesai' => 'required|after:jamMulai',
            'keperluan' => 'required|string|max:255',
            'items' => 'required|array', // Memastikan 'items' dipilih
            'items.*.id' => 'required|integer', // Memastikan setiap item yang dipilih valid
        ]);

        // 2. Ambil ID pengguna yang sedang login
        $userId = Auth::id();

        // 3. Looping untuk setiap item (ruangan/unit) yang dipilih
        foreach ($validated['items'] as $item) {
            // 4. Masukkan data ke tabel 'peminjaman'
            DB::table('peminjaman')->insert([
                'idMahasiswa' => $userId,
                'idRuangan' => ($validated['jenis_item'] == 'ruangan') ? $item['id'] : null,
                'idUnit' => ($validated['jenis_item'] == 'unit') ? $item['id'] : null,
                'tanggalPinjam' => $validated['tanggalPinjam'],
                'jamMulai' => $validated['jamMulai'],
                'jamSelesai' => $validated['jamSelesai'],
                'keperluan' => $validated['keperluan'],
                'status' => 'pending', // Status awal saat pengajuan adalah 'pending'
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Arahkan kembali ke dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Pengajuan peminjaman berhasil dikirim!');
    }
}