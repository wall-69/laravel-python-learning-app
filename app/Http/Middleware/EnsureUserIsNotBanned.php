<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()?->banned_at !== null) {
            Auth::logout();

            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route("login")->with([
                "danger" => __("auth.banned")
            ]);
        }

        return $next($request);
    }
}
