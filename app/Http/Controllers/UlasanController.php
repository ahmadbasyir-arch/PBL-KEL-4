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

        return redirect()->back()->with('success', 'Terima kasih atas ulasan Anda!');
    }

    public function index()
    {
        $ulasan = Ulasan::with('user')->orderByDesc('created_at')->get();
        return view('admin.ulasan.index', compact('ulasan'));
    }
}
