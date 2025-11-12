<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $jumlahPeminjaman = Peminjaman::count();
        $menunggu = Peminjaman::where('status', 'Menunggu')->count();
        $disetujui = Peminjaman::where('status', 'Disetujui')->count();
        $ditolak = Peminjaman::where('status', 'Ditolak')->count();

        return view('admin.dashboard', compact('jumlahPeminjaman', 'menunggu', 'disetujui', 'ditolak'));
    }

    public function peminjaman()
    {
        $data = Peminjaman::latest()->get();
        return view('admin.peminjaman', compact('data'));
    }

    public function setujui($id)
    {
        $p = Peminjaman::findOrFail($id);
        $p->status = 'Disetujui';
        $p->save();

        return back()->with('success', 'Peminjaman disetujui.');
    }

    public function tolak($id)
    {
        $p = Peminjaman::findOrFail($id);
        $p->status = 'Ditolak';
        $p->save();

        return back()->with('success', 'Peminjaman ditolak.');
    }
}