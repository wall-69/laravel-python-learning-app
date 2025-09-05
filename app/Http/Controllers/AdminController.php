<?php

namespace App\Http\Controllers;

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

        $imagePath = $request->file("image")->store("img", "public");

        return response()->json([
            "success" => true,
            "file" => [
                "url" => Storage::url($imagePath)
            ]
        ]);
    }
}
