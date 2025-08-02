<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    // GET

    public function notice()
    {
        return view("auth.verify-email");
    }

    // POST

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect(route("index"));
    }

    public function send(Request $request)
    {
        try {
            $request->user()->sendEmailVerificationNotification();

            return back()->with("success", "Overovací e-mail bol odoslaný!");
        } catch (ThrottleRequestsException $e) {
            return back()->with("error", "Počkajte pred opätovným odoslaním overovacieho e-mailu.");
        }
    }
}
