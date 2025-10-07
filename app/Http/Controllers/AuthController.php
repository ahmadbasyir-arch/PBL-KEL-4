<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ===============================
    // TAMPILAN LOGIN & REGISTER
    // ===============================
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
        Auth::login($user);

        // Arahkan sesuai peran (role)
        return $this->redirectByRole($user);
    }

    // ===============================
    // LOGIN USER
    // ===============================
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $loginInput = $request->input('username');
        $loginType = filter_var($loginInput, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : (is_numeric($loginInput) ? 'nim' : 'username');

        if (Auth::attempt([$loginType => $loginInput, 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            $user = Auth::user();
            return $this->redirectByRole($user);
        }

        return back()->withErrors([
            'username' => 'Akun tidak ditemukan atau password salah.',
        ]);
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