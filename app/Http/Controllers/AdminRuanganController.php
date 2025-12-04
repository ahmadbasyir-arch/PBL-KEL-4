<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\Peminjaman;

class AdminRuanganController extends Controller
{
    public function index()
    {
        // 1. Perbaikan Data Otomatis (jika null)
        Ruangan::whereNull('status')->orWhere('status', '')->update(['status' => 'tersedia']);

        // 2. Sinkronisasi Status dengan Peminjaman Aktif (HANYA HARI INI)
        // Ambil ID ruangan yang sedang dipinjam HARI INI
        $activeIds = Peminjaman::whereNotNull('idRuangan')
            ->whereDate('tanggalPinjam', now()->toDateString()) // Filter tanggal hari ini
            ->whereIn('status', ['disetujui', 'digunakan', 'sedang digunakan', 'menyelesaikan', 'menunggu_validasi'])
            ->pluck('idRuangan')
            ->toArray();

        // Update status menjadi 'digunakan' untuk ruangan yang ada di daftar aktif
        if (!empty($activeIds)) {
            Ruangan::whereIn('id', $activeIds)->update(['status' => 'digunakan']);
        }

        // Update status menjadi 'tersedia' untuk ruangan yang TIDAK ada di daftar aktif
        // (Opsional: hanya jika sebelumnya 'digunakan' atau 'dipinjam' agar tidak menimpa status 'perawatan' jika ada)
        Ruangan::whereNotIn('id', $activeIds)
            ->whereIn('status', ['dipinjam', 'digunakan'])
            ->update(['status' => 'tersedia']);

        $ruangan = Ruangan::orderBy('namaRuangan')->get();
        return view('admin.sidebar-ruangan', compact('ruangan'));
    }

    public function create()
    {
        // mode create dikirim agar bisa dipakai di view
        return view('admin.sidebar-ruangan', ['mode' => 'create']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'namaRuangan' => 'required|string|max:100|unique:ruangan,namaRuangan',
            'lokasi' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:1',
            'status' => 'nullable|string|max:50'
        ]);

        Ruangan::create([
            'namaRuangan' => $request->namaRuangan,
            'lokasi' => $request->lokasi,
            'kapasitas' => $request->kapasitas,
            'status' => $request->status ?? 'tersedia',
        ]);

        return redirect()->route('admin.ruangan.index')->with('success', 'Ruangan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        return view('admin.sidebar-ruangan', [
            'mode' => 'edit',
            'ruangan' => $ruangan
        ]);
    }

    public function update(Request $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);

        $request->validate([
            'namaRuangan' => 'required|string|max:100|unique:ruangan,namaRuangan,' . $id,
            'lokasi' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:1',
            'status' => 'nullable|string|max:50'
        ]);

        $ruangan->update($request->only(['namaRuangan', 'lokasi', 'kapasitas', 'status']));

        return redirect()->route('admin.ruangan.index')->with('success', 'Data ruangan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return redirect()->route('admin.ruangan.index')->with('success', 'Ruangan berhasil dihapus!');
    }

    public function show($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        return view('admin.sidebar-ruangan', [
            'mode' => 'show',
            'ruangan' => $ruangan
        ]);
    }
}