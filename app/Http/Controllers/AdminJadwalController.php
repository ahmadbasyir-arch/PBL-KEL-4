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
    public function destroy($id)
    {
        Jadwal::findOrFail($id)->delete();
        return back()->with('success', 'Jadwal berhasil dihapus!');
    }

    public function reset()
    {
        Jadwal::truncate();
        return back()->with('success', 'Semua data jadwal berhasil direset!');
    }

    public function edit($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $ruangans = Ruangan::all();
        return view('admin.jadwal.edit', compact('jadwal', 'ruangans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'mata_kuliah' => 'required|string',
            'dosen' => 'required|string',
            'kelas' => 'required|string',
            'ruangan_id' => 'nullable|exists:ruangan,id',
        ]);

        $jadwal = Jadwal::findOrFail($id);
        $jadwal->update($request->all());

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil diperbarui!');
    }
}
