<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminJadwalController extends Controller
{
    public function index()
    {
        $jadwals = Jadwal::with('ruangan')->orderBy('hari')->orderBy('jam_mulai')->get();
        return view('admin.jadwal.index', compact('jadwals'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xls,xlsx|max:2048',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\JadwalImport, $request->file('file'));
            return back()->with('success', 'Jadwal berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import jadwal: ' . $e->getMessage());
        }
    }
}
