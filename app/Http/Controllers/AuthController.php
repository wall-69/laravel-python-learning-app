<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // GET

    public function login()
    {
        return view("auth.login");
    }

    public function register()
    {
        return view("auth.register");
    }

    // POST

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        if (!Auth::attempt($credentials, (bool) $request->remember_me)) {
            return back()->withErrors([
                "email" => __("auth.failed")
            ])->onlyInput("email");
        }

        $user = Auth::user();

        if ($user->banned_at !== null) {
            Auth::logout();

            session()->invalidate();
            session()->regenerateToken();

            return back()->withErrors([
                "email" => __("auth.banned")
            ])->onlyInput("email");
        }

        session()->regenerate();

        return redirect()->intended(route("index"));
    }

    public function logout()
    {
        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect(route("index"));
    }
}
