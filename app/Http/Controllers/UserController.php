<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProgress;
use Auth;
use Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // GET

    public function profile(Request $request, string $firstName, string $lastName, User $user)
    {
        $expectedFirstName = strtolower($user->first_name);
        $expectedLastName = strtolower($user->last_name);

        // If the first or last name is not correct we redirect to the correct one
        if (!$firstName || !$lastName || $expectedFirstName != $firstName || $expectedLastName != $lastName) {
            return redirect($user->profile_url);
        }

        $latestActivity = $user->completedQuizzes->merge($user->completedExercises)->sortByDesc('created_at')->take(5);

        return view("user.profile", [
            "user" => $user->load("progress"),
            "latestActivity" => $latestActivity
        ]);
    }

    public function settings(Request $request)
    {
        return view("user.settings");
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

    public function destroy(Request $request, User $user)
    {
        if ($user != $request->user()) {
            abort(403);
        }

        $request->validate([
            "delete_password" => "required"
        ]);

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                "delete_password" => "Zadali ste nesprávne heslo."
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect(route("index"))->with("success", "Účet bol úspešne vymazaný.");
    }

    public function changePassword(Request $request, User $user)
    {
        if ($user != $request->user()) {
            abort(403);
        }

        $data = $request->validate([
            "old_password" => "required",
            "password" => "required|confirmed|min:6",
        ])->validateWithBag("change-password");

        // Check password
        if (!Hash::check($request->old_password, $user->password)) {
            throw ValidationException::withMessages([
                "old_password" => "Zadali ste nesprávne heslo."
            ]);
        }

        $user->update($data);

        return back()->with("success", "Vaše heslo bolo úspešne zmenené.");
    }
}
