<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    // GET

    public function dashboard()
    {
        return view("admin.dashboard");
    }

    public function lectures()
    {
        return view("admin.lectures");
    }
}
