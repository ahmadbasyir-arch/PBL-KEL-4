<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ... (fungsi showLoginForm dan showRegisterForm tidak perlu diubah) ...
    public function showLoginForm() { return view('auth.login'); }
    public function showRegisterForm() { return view('auth.register'); }


    // [PERBAIKAN] Memproses data registrasi & LOGIN OTOMATIS
    public function register(Request $request)
    {
        $validated = $request->validate([
            'namaLengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:mahasiswa',
            'email' => 'required|email|max:100|unique:mahasiswa',
            'nim' => 'required|string|max:20|unique:mahasiswa',
            'role' => 'required|in:mahasiswa,dosen,admin,staff',
            'password' => 'required|string|min:8',
        ]);

        // Buat user baru
        $user = User::create([
            'namaLengkap' => $validated['namaLengkap'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'nim' => $validated['nim'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        // Login user yang baru dibuat secara otomatis
        Auth::login($user);

        // Arahkan langsung ke dashboard
        return redirect()->route('dashboard');
    }


    // [PERBAIKAN] Memproses data login dengan redirect yang benar
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $loginInput = $request->input('username');
        $loginType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : (is_numeric($loginInput) ? 'nim' : 'username');

        if (Auth::attempt([$loginType => $loginInput, 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            
            // Arahkan ke route 'dashboard' yang akan kita definisikan
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => 'Akun tidak ditemukan atau password salah.',
        ]);
    }

    // ... (fungsi logout tidak perlu diubah) ...
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}