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
}
