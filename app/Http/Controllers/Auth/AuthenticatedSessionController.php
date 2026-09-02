<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Chukua taarifa za mtumiaji aliyelog in
        $user = Auth::user();

        // Elekeza mtumiaji kulingana na Role yake
        if ($user->role === 'librarian') {
            return redirect()->intended(route('library.index', absolute: false));
        }

        if ($user->role === 'supervisor') {
            return redirect()->intended(route('supervisor.index', absolute: false));
        }

        // Kama ni Mwanafunzi (Student) au role nyingine
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}