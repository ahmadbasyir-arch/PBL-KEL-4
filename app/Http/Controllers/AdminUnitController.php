<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Peminjaman;

class AdminUnitController extends Controller
{
    public function index()
    {
        // 1. Perbaikan Data Otomatis
        Unit::whereNull('status')->orWhere('status', '')->update(['status' => 'tersedia']);

        // 2. Sinkronisasi Status dengan Peminjaman Aktif (HANYA HARI INI)
        $activeIds = Peminjaman::whereNotNull('idUnit')
            ->whereDate('tanggalPinjam', now()->toDateString()) // Filter tanggal hari ini
            ->whereIn('status', ['disetujui', 'digunakan', 'sedang digunakan', 'menyelesaikan', 'menunggu_validasi'])
            ->pluck('idUnit')
            ->toArray();

        if (!empty($activeIds)) {
            Unit::whereIn('id', $activeIds)->update(['status' => 'digunakan']);
        }

        Unit::whereNotIn('id', $activeIds)
            ->whereIn('status', ['dipinjam', 'digunakan'])
            ->update(['status' => 'tersedia']);

        $unit = Unit::orderBy('namaUnit')->get();
        return view('admin.sidebar-unit', compact('unit'));
    }

    public function create()
    {
        // tidak ubah logika, hanya pastikan route create berfungsi
        return view('admin.unit.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kodeUnit' => 'required|string|max:20|unique:unit,kodeUnit',
            'namaUnit' => 'required|string|max:100|unique:unit,namaUnit',
            'kategori' => 'nullable|string|max:100',
        ]);

        Unit::create([
            'kodeUnit' => $request->kodeUnit,
            'namaUnit' => $request->namaUnit,
            'kategori' => $request->kategori,
            'status'   => 'tersedia', // Default status
        ]);

        return redirect()->route('unit.index')->with('success', 'Unit berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        return view('admin.unit.edit', compact('unit'));
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $request->validate([
            'kodeUnit' => 'required|string|max:20|unique:unit,kodeUnit,' . $id,
            'namaUnit' => 'required|string|max:100|unique:unit,namaUnit,' . $id,
            'kategori' => 'nullable|string|max:100',
        ]);

        $unit->update($request->only(['kodeUnit', 'namaUnit', 'kategori']));

        return redirect()->route('unit.index')->with('success', 'Data unit berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();

        return redirect()->route('unit.index')->with('success', 'Unit berhasil dihapus!');
    }

    public function show($id)
    {
        $unit = Unit::findOrFail($id);
        return view('admin.unit.show', compact('unit'));
    }
}