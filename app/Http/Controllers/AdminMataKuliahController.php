<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MataKuliahImport;

class AdminMataKuliahController extends Controller
{
    public function index()
    {
        $matkuls = MataKuliah::orderBy('semester')->orderBy('nama_matkul')->get();
        return view('admin.matkul.index', compact('matkuls'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        try {
            Excel::import(new MataKuliahImport, $request->file('file'));
            return back()->with('success', 'Data Mata Kuliah berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        MataKuliah::findOrFail($id)->delete();
        return back()->with('success', 'Mata Kuliah berhasil dihapus!');
    }

    public function reset()
    {
        MataKuliah::truncate();
        return back()->with('success', 'Semua data mata kuliah berhasil direset!');
    }

    public function edit($id)
    {
        $mk = MataKuliah::findOrFail($id);
        return view('admin.matkul.edit', compact('mk'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|string|unique:mata_kuliahs,kode,' . $id,
            'nama_matkul' => 'required|string',
            'semester' => 'required|string',
            'kurikulum' => 'required|string',
        ]);

        $mk = MataKuliah::findOrFail($id);
        $mk->update($request->all());

        return redirect()->route('admin.matkul.index')->with('success', 'Mata Kuliah berhasil diperbarui!');
    }
}
