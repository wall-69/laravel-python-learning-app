<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // POST

    public function store(Request $request)
    {
        $data = $request->validate([
            "first_name" => "required|string",
            "last_name" => "required|string",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed|min:6",
            "tos" => "required|accepted",
        ]);

        $user = User::create($data);

        Auth::login($user);

        event(new Registered($user));

        return redirect(route("verification.notice"))
            ->with("success", "Váš účet bol úspešne vytvorený. Aktivujte si ho cez overovací e-mail.");
    }
}
