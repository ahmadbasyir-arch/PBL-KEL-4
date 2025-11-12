<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        // [INI PENAMBAHANNYA]
        // Validasi domain email
        if (
            !str_ends_with($googleUser->getEmail(), '@politala.ac.id') &&
            !str_ends_with($googleUser->getEmail(), '@mhs.politala.ac.id')
        ) {
            return redirect('/login')->with('error', 'Login Google hanya diizinkan untuk email Politala.');
        }

        // 🔍 Cek apakah user sudah ada berdasarkan google_id atau email
        $existingUser = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($existingUser) {
            // 🧩 Pastikan google_id tersimpan
            if (!$existingUser->google_id) {
                $existingUser->google_id = $googleUser->getId();
                $existingUser->save();
            }

            Auth::login($existingUser);

            // Jika profil belum lengkap arahkan ke lengkapi profil
            if ($existingUser->is_completed == 0) {
                return redirect()->route('lengkapi.profil');
            }

            // 🔁 Arahkan ke dashboard sesuai role
            return $this->redirectByRole($existingUser);
        }

        // 🆕 Jika belum ada, buat user baru
        $username = $this->generateUniqueUsername($googleUser->getName());

        $user = User::create([
            'name'         => $googleUser->getName(), // langsung dari Google
            'username'     => $username,
            'email'        => $googleUser->getEmail(),
            'google_id'    => $googleUser->getId(),
            'avatar'       => $googleUser->getAvatar(),
            'password'     => Hash::make(Str::random(16)),
            'role'         => 'mahasiswa', // Default role
            'is_completed' => 0,
        ]);

        Auth::login($user);

        return redirect()->route('lengkapi.profil');
    }

    /**
     * 🔧 Membuat username unik agar tidak bentrok.
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
        $user = Auth::user();
        return view('auth.lengkapi-profil', compact('user'));
    }

    /**
     * Simpan data profil tambahan dari form lengkapi-profil.
     */
    public function storeCompleteProfile(Request $request)
    {
        $request->validate([
            'nim'      => 'required|string|max:50|unique:users,nim,' . Auth::id(),
            'role'     => 'required|in:mahasisw,dosen', // <-- Pastikan ini sesuai dengan enum di database Anda
            'password' => 'required|string|min:6|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Perbarui data tanpa mengubah nama dari Google
        $user->update([
            'nim'          => $request->nim,
            'role'         => $request->role,
            'password'     => Hash::make($request->password),
            'is_completed' => 1,
        ]);

        // 🔁 Arahkan ke dashboard sesuai role
        return $this->redirectByRole($user, 'Profil berhasil dilengkapi!');
    }

    /**
     * 🔀 Fungsi bantu untuk redirect berdasarkan role.
     */
    private function redirectByRole($user, $message = null)
    {
        $message = $message ?? 'Berhasil login dengan Google!';

        switch ($user->role) {
            case 'mahasiswa':
                return redirect()->route('mahasiswa.dashboard')->with('success', $message);
            case 'dosen':
                return redirect()->route('dosen.dashboard')->with('success', $message);
            case 'admin':
                return redirect()->route('admin.dashboard')->with('success', $message);
            case 'staff':
                return redirect()->route('staff.dashboard')->with('success', $message);
            default:
                return redirect()->route('dashboard')->with('success', $message);
        }
    }
}