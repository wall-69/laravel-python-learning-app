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
            "email" => "required",
            "password" => "required",
        ]);

        if (Auth::attempt($credentials, (bool) $request->remember_me)) {
            session()->regenerate();

            return redirect(route("index"));
        }

        return back()->withErrors([
            "password" => "Zadali ste zlý email alebo heslo."
        ])->onlyInput("email");
    }

    public function logout()
    {
        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect(route("index"));
    }
}
