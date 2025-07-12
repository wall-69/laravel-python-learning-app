<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        User::create($data);

        return redirect(route("login"))
            ->with("success", "Váš účet bol úspešne vytvorený. Aktivujte si ho cez overovací e-mail.");
    }
}
