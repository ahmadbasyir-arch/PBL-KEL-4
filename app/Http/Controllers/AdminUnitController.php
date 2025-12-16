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
        return view('admin.sidebar-unit', ['mode' => 'create']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kodeUnit' => 'required|string|max:20|unique:unit,kodeUnit',
            'namaUnit' => 'required|string|max:100|unique:unit,namaUnit',
        ]);

        Unit::create([
            'kodeUnit' => $request->kodeUnit,
            'namaUnit' => $request->namaUnit,
            'status'   => 'tersedia', // Default status
        ]);

        return redirect()->route('admin.unit.index')->with('success', 'Unit berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        return view('admin.sidebar-unit', ['mode' => 'edit', 'unit' => $unit]);
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $request->validate([
            'kodeUnit' => 'required|string|max:20|unique:unit,kodeUnit,' . $id,
            'namaUnit' => 'required|string|max:100|unique:unit,namaUnit,' . $id,
        ]);

        $unit->update($request->only(['kodeUnit', 'namaUnit']));

        return redirect()->route('admin.unit.index')->with('success', 'Data unit berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();

        return redirect()->route('admin.unit.index')->with('success', 'Unit berhasil dihapus!');
    }

    public function show($id)
    {
        $unit = Unit::findOrFail($id);
        return view('admin.sidebar-unit', ['mode' => 'show', 'unit' => $unit]);
    }
}