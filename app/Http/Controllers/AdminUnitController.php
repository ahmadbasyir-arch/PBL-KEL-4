<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;

class AdminUnitController extends Controller
{
    public function index()
    {
        $unit = Unit::orderBy('namaUnit')->get();
        return view('admin.sidebar-unit', compact('unit'));
    }

    public function create()
    {
        // 🔹 ubah view ke sidebar-unit dengan mode 'create'
        return view('admin.sidebar-unit', ['mode' => 'create']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'namaUnit' => 'required|string|max:100|unique:unit,namaUnit',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        Unit::create([
            'namaUnit' => $request->namaUnit,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'status' => 'tersedia',
        ]);

        return redirect()->route('unit.index')->with('success', 'Unit berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        // 🔹 ubah view ke sidebar-unit dengan mode 'edit'
        return view('admin.sidebar-unit', compact('unit'))->with('mode', 'edit');
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $request->validate([
            'namaUnit' => 'required|string|max:100|unique:unit,namaUnit,' . $id,
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $unit->update($request->only(['namaUnit', 'jumlah', 'keterangan']));

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
        // 🔹 ubah view ke sidebar-unit dengan mode 'show'
        return view('admin.sidebar-unit', compact('unit'))->with('mode', 'show');
    }
}