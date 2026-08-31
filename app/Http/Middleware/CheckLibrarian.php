<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLibrarian
{
    public function handle(Request $request, Closure $next)
    {
        // Ruhusu Librarian NA Admin kuingia kwenye Reports / Analytics
        if (Auth::check() && in_array(Auth::user()->role, ['librarian', 'admin'])) {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'Hauna ruhusa ya kufikia ukurasa huu.');
    }
}