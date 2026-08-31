<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // FR-1.3 Role-Based Access Control Redirect
        if ($user->role === 'supervisor') {
            return redirect()->route('supervisor.index');
        }

        if ($user->role === 'librarian') {
            return redirect()->route('library.index');
        }

        // Student Dashboard
        $myDocuments = Repository::where('user_id', $user->id)->latest()->get();
        return view('dashboard', compact('myDocuments'));
    }
}