<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'username' => Str::slug($googleUser->getName(), '_'),
                'google_id' => $googleUser->getId(),
                'password' => bcrypt(Str::random(16))
            ]
        );

        Auth::login($user);

        return redirect('/dashboard'); // arahkan ke dashboard setelah login
    }
}