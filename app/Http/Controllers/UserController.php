<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProgress;
use Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET

    public function profile(Request $request, User $user)
    {
        $latestActivity = $user->completedQuizzes->merge($user->completedExercises)->sortByDesc('created_at')->take(5);

        return view("user.profile", [
            "user" => $user->load("progress"),
            "latestActivity" => $latestActivity
        ]);
    }

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

        // Create UserProgress
        UserProgress::create(["user_id" => $request->user()->id]);

        return redirect(route("verification.notice"))
            ->with("success", "Váš účet bol úspešne vytvorený. Aktivujte si ho cez overovací e-mail.");
    }
}
