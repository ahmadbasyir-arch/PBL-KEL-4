<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ulasan;
use Illuminate\Support\Facades\Auth;

class UlasanController extends Controller
{
    public function create()
    {
        return view('ulasan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|max:1000',
        ]);

        Ulasan::create([
            'id_user' => Auth::id(),
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);

        // 🔔 NOTIFIKASI KE ADMIN
        $admins = \App\Models\User::where('role', 'admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminNotification(
            'Ulasan Baru',
            "Ada ulasan baru dari " . Auth::user()->name,
            route('admin.ulasan.index'),
            'success'
        ));

        return redirect()->back()->with('success', 'Terima kasih atas ulasan Anda!');
    }

    public function index()
    {
        $ulasan = Ulasan::with('user')->orderByDesc('created_at')->get();
        return view('admin.ulasan.index', compact('ulasan'));
    }

    public function exportPdf(Request $request)
    {
        $periode = $request->input('periode', 'bulanan');
        $query = Ulasan::with('user');

        $now = \Carbon\Carbon::now();

        if ($periode == 'harian') {
            $query->whereDate('created_at', $now->format('Y-m-d'));
        } elseif ($periode == 'mingguan') {
            $query->whereBetween('created_at', [$now->startOfWeek()->format('Y-m-d'), $now->endOfWeek()->format('Y-m-d')]);
        } elseif ($periode == 'bulanan') {
            $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
        } elseif ($periode == 'semester') {
            if ($now->month >= 7) {
                $query->whereMonth('created_at', '>=', 7);
            } else {
                $query->whereMonth('created_at', '<=', 6);
            }
            $query->whereYear('created_at', $now->year);
        } elseif ($periode == 'tahunan') {
            $query->whereYear('created_at', $now->year);
        }

        $ulasan = $query->orderByDesc('created_at')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.ulasan.pdf', [
            'ulasan' => $ulasan,
            'periode' => ucfirst($periode)
        ]);

        return $pdf->download('Laporan_Ulasan_' . ucfirst($periode) . '_' . date('Ymd') . '.pdf');
    }
}
