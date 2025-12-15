<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'msg' => 'Invalid credentials'
            ], 422);
        }

        $request->session()->regenerate();

        return response()->json([
            'user' => Auth::user()
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'msg' => 'Logged out'
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'balance' => $user->balance,
            'assets' => $user->assets
        ]);
    }
}
