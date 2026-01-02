<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Storage;

class AdminController extends Controller
{
    // GET

    public function dashboard()
    {
        return view("admin.dashboard");
    }

    // POST

    public function uploadImage(Request $request)
    {
        $request->validate([
            "image" => "required|image"
        ]);

        $imagePath = $request->file("image")->store("img/lectures", "public");

        return response()->json([
            "success" => true,
            "file" => [
                "url" => Storage::url($imagePath)
            ]
        ]);
    }

    public function banUser(Request $request, User $user)
    {
        $user->banned_at = now();
        $user->save();

        return redirect()->back()->with("success", "Používateľ bol úspešne zablokovaný.");
    }

    public function unbanUser(Request $request, User $user)
    {
        $user->banned_at = null;
        $user->save();

        return redirect()->back()->with("success", "Používateľ bol úspešne odblokovaný.");
    }
}
