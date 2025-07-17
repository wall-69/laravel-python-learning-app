<?php

namespace App\Http\Controllers;

use Auth;
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
                "password" => __("auth.failed")
            ])->onlyInput("email");
        }

        session()->regenerate();

        return redirect(route("index"));
    }

    public function logout()
    {
        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect(route("index"));
    }
}
