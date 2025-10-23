<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;

class AdminRuanganController extends Controller
{
    public function index()
    {
        $ruangan = Ruangan::orderBy('namaRuangan')->get();
        return view('admin.sidebar-ruangan', compact('ruangan'));
    }

    public function create()
    {
        // 🔹 ubah view ke sidebar-ruangan dengan mode 'create'
        return view('admin.sidebar-ruangan', ['mode' => 'create']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'namaRuangan' => 'required|string|max:100|unique:ruangan,namaRuangan',
            'kapasitas' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string|max:255',
        ]);

        Ruangan::create([
            'namaRuangan' => $request->namaRuangan,
            'kapasitas' => $request->kapasitas,
            'fasilitas' => $request->fasilitas,
            'status' => 'tersedia',
        ]);

        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        // 🔹 ubah view ke sidebar-ruangan dengan mode 'edit'
        return view('admin.sidebar-ruangan', compact('ruangan'))->with('mode', 'edit');
    }

    public function update(Request $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);

        $request->validate([
            'namaRuangan' => 'required|string|max:100|unique:ruangan,namaRuangan,' . $id,
            'kapasitas' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string|max:255',
        ]);

        $ruangan->update($request->only(['namaRuangan', 'kapasitas', 'fasilitas']));

        return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil dihapus!');
    }

    public function show($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        // 🔹 ubah view ke sidebar-ruangan dengan mode 'show'
        return view('admin.sidebar-ruangan', compact('ruangan'))->with('mode', 'show');
    }
}