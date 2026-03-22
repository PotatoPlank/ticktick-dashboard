<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('pocketid')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $user = Socialite::driver('pocketid')->user();

        if (! ($user->getRaw()['email_verified'] ?? false)) {
            abort(403, 'Email not verified.');
        }

        session(['authenticated' => true]);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('authenticated');

        return redirect()->route('auth.redirect');
    }
}
