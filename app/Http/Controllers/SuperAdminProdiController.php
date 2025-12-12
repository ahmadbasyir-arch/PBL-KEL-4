<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prodi;

class SuperAdminProdiController extends Controller
{
    public function index()
    {
        $prodi = Prodi::all();
        return view('superadmin.prodi.index', compact('prodi'));
    }

    public function create()
    {
        return view('superadmin.prodi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_prodi' => 'required|string|max:255',
            'kode_prodi' => 'required|string|max:10|unique:prodi',
        ]);

        Prodi::create($request->all());

        return redirect()->route('superadmin.prodi.index')->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function edit(Prodi $prodi)
    {
        return view('superadmin.prodi.edit', compact('prodi'));
    }

    public function update(Request $request, Prodi $prodi)
    {
        $request->validate([
            'nama_prodi' => 'required|string|max:255',
            'kode_prodi' => 'required|string|max:10|unique:prodi,kode_prodi,'.$prodi->id,
        ]);

        $prodi->update($request->all());

        return redirect()->route('superadmin.prodi.index')->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy(Prodi $prodi)
    {
        $prodi->delete();
        return redirect()->route('superadmin.prodi.index')->with('success', 'Program Studi berhasil dihapus.');
    }
}
