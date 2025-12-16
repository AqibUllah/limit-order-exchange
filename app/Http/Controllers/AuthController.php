<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'msg' => 'Invalid credentials',
            ], 422);
        }

        return $this->auth_response($request);
    }

    public function register(RegisterRequest $request)
    {
        try {

            // registering new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,

            ]);

            if ($user) {
                if (! Auth::attempt($request->only('email', 'password'))) {
                    return response()->json([
                        'msg' => 'Invalid credentials',
                    ], 422);
                }
            }

            return $this->auth_response($request);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'msg' => 'Logged out',
        ]);
    }

    public function profile()
    {
        $user = Auth::user();

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'balance' => $user->balance,
            'assets' => $user->assets,
        ]);
    }

    public function auth_response(Request $request)
    {
        return response()->json([
            'user' => Auth::user(),
            'token' => Auth::user()->createToken($request->email)->plainTextToken,
        ]);
    }
}
