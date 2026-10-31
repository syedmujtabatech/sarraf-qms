<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        $users = User::with('branch', 'assignedCounter')->get();
        return view('auth.login', compact('users'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function quickLogin(int $userId)
    {
        $user = User::findOrFail($userId);
        Auth::login($user);
        request()->session()->regenerate();
        return $this->redirectBasedOnRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    protected function redirectBasedOnRole(User $user)
    {
        if ($user->role === 'teller') {
            return redirect()->route('teller.index');
        }
        if ($user->role === 'greeter') {
            return redirect()->route('kiosk.index');
        }
        return redirect()->route('admin.dashboard');
    }
}
