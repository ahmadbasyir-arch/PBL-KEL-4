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
}
