<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ==============================
    // TAMPILAN LOGIN & REGISTER
    // ==============================
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // ===============================
    // REGISTER USER BARU
    // ===============================
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

        // Simpan user baru
        $user = User::create([
            'namaLengkap' => $validated['namaLengkap'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'nim' => $validated['nim'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        // Login otomatis setelah registrasi
        Auth::login($user, true); // ✅ remember me aktif

        // Arahkan sesuai peran (role)
        return $this->redirectByRole($user);
    }

    // ===============================
    // LOGIN USER
    // ===============================
    public function login(Request $request)
    {
        // ✅ Ganti agar cocok dengan input form (email_or_nim)
        $credentials = $request->validate([
            'email_or_nim' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = $request->input('email_or_nim');

        // ✅ Deteksi tipe login: email / nim / username
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $loginType = 'email';
        } elseif (is_numeric($loginInput)) {
            $loginType = 'nim';
        } else {
            $loginType = 'username';
        }

        // ✅ Login dengan "remember me" jika dicentang
        if (Auth::attempt([$loginType => $loginInput, 'password' => $credentials['password']], $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            return $this->redirectByRole($user);
        }

        // ✅ Jika gagal
        return back()->withErrors([
            'email_or_nim' => 'Email, NIM, atau password salah.',
        ])->withInput();
    }

    // ===============================
    // LOGOUT USER
    // ===============================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // ===============================
    // REDIRECT BERDASARKAN ROLE
    // ===============================
    private function redirectByRole($user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'mahasiswa':
                return redirect()->route('mahasiswa.dashboard');
            case 'dosen':
                return redirect()->route('dosen.dashboard');
            case 'staff':
                return redirect()->route('staff.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }
}