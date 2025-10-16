<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Arahkan user ke halaman login Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback setelah login Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal login dengan Google.');
        }

        // 🔍 Cek apakah user sudah pernah login via Google atau email
        $existingUser = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($existingUser) {
            // Jika sudah ada, login langsung
            Auth::login($existingUser);
            return redirect()->route('dashboard');
        }

        // 🆕 Jika belum ada, buat user baru (gunakan kolom standar Laravel: name)
        $username = $this->generateUniqueUsername($googleUser->getName());

        $user = User::create([
            'name'       => $googleUser->getName(), // ✅ kolom standar Laravel
            'username'   => $username,
            'email'      => $googleUser->getEmail(),
            'google_id'  => $googleUser->getId(),
            'password'   => bcrypt(Str::random(16)),
        ]);

        Auth::login($user);

        // Jika profil belum lengkap (nim/role kosong)
        if (empty($user->nim) || empty($user->role)) {
            return redirect()->route('lengkapi.profil');
        }

        return redirect('/dashboard');
    }

    /**
     * 🔧 Fungsi bantu: buat username unik agar tidak bentrok.
     */
    private function generateUniqueUsername($name)
    {
        $base = Str::slug($name, '_');
        $username = $base;
        $count = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . '_' . $count;
            $count++;
        }

        return $username;
    }

    /**
     * Menampilkan form lengkapi profil.
     */
    public function showCompleteProfile()
    {
        return view('auth.lengkapi-profil');
    }

    /**
     * Simpan data profil tambahan.
     */
    public function storeCompleteProfile(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255', // ✅ ganti dari namaLengkap → name
            'nim'       => 'required|string|max:50|unique:mahasiswa,nim',
            'role'      => 'required|in:mahasiswa,dosen',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update([
            'name'      => $request->name, // ✅ kolom standar Laravel
            'nim'       => $request->nim,
            'role'      => $request->role,
            'password'  => bcrypt($request->password),
        ]);

        return redirect('/dashboard')->with('success', 'Profil berhasil dilengkapi!');
    }
}