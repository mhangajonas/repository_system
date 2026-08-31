<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSupervisor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'supervisor') {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Huna ruhusa ya kufikia ukurasa huu.');
    }
}