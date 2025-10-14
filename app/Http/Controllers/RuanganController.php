<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan; // Menggunakan Model Ruangan

class RuanganController extends Controller
{
    /**
     * Menampilkan halaman daftar semua ruangan.
     */
    public function index()
    {
        // Ambil semua data dari tabel 'ruangan', diurutkan berdasarkan nama
        $ruangan = Ruangan::orderBy('namaRuangan', 'asc')->get();

        // Kirim data ke view
        return view('admin.ruangan.index', compact('ruangan'));
    }

    // Nanti kita akan tambahkan fungsi create(), store(), edit(), update(), destroy() di sini
}
