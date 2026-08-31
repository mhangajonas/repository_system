<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation inayokubali reg_number au staff_id kulingana na role
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:student,supervisor,librarian'],
            'department' => ['nullable', 'string', 'max:255'],
            'reg_number' => ['nullable', 'string', 'max:255', 'unique:'.User::class],
            'staff_id' => ['nullable', 'string', 'max:255', 'unique:'.User::class],
        ]);

        // Hifadhi data mpya kwenye database kulingana na kama ni mwanafunzi au mfanyakazi
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department' => $request->department,
            'reg_number' => $request->role === 'student' ? $request->reg_number : null,
            'staff_id' => $request->role !== 'student' ? $request->staff_id : null,
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Akaunti yako imetengenezwa kikamilifu! Tafadhali ingia (login).');
    }
}