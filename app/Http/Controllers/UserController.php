<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'exists:users'],
            'password' => ['required', 'string'],
        ]);

        if (auth()->attempt($credentials)) {
            return redirect()->intended('/dashboard');
        } else {
            return redirect()->back()->withErrors(['message' => 'Invalid credentials.']);
        }
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'], 
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'password' => bcrypt($validatedData['password']),
            'average_result' => 0,
            'played_games' => 0,
            'give_ups' => 0,
        ]);

        auth()->login($user);

        return redirect()->route('dashboard')->with('success', 'Registration successful.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

}
