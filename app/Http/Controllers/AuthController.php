<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class AuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('pocketid')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try{
            $user = Socialite::driver('pocketid')->user();
        }catch(InvalidStateException){
            return Socialite::driver('pocketid')->redirect();
        }


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
