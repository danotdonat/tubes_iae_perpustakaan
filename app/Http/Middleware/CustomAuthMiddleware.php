<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CustomAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Simple session-based authentication
        if (!Session::has('user_logged_in')) {
            Session::flash('error', 'Anda harus login terlebih dahulu!');
            return redirect()->route('login');
        }

        return $next($request);
    }
}
