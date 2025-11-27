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
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|max:100|unique:users',
            'nim' => 'required|string|max:20|unique:users',
            'role' => 'required|in:mahasiswa,dosen,admin,staff',
            'password' => 'required|string|min:8',
        ]);

        // Simpan user baru tanpa kolom namaLengkap
        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'nim' => $validated['nim'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'name' => $validated['username'], // default isi dengan username
        ]);

        Auth::login($user, true);

        return $this->redirectByRole($user);
    }

    // ===============================
    // LOGIN USER
    // ===============================
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email_or_nim' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = $request->input('email_or_nim');

        // Deteksi tipe login: email / nim / username
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $loginType = 'email';
        } elseif (is_numeric($loginInput)) {
            $loginType = 'nim';
        } else {
            $loginType = 'username';
        }

        if (Auth::attempt([$loginType => $loginInput, 'password' => $credentials['password']], $request->filled('remember'))) {
            $request->session()->regenerate();
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Pastikan nama tetap terisi (misalnya login dari Google)
            if (empty($user->name) && !empty($user->username)) {
                $user->name = $user->username;
                $user->save();
            }

            return $this->redirectByRole($user);
        }

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