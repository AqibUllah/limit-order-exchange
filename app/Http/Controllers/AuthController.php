<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Asset;
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

                // handle balances
                $this->handleBalances($user);

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
        $user = $request->user();

        if ($user) {
            $user->currentAccessToken()->delete();
            $user->tokens()->delete();
        }

        return response()->json([
            'msg' => 'Logged out',
        ]);
    }

    public function profile(Request $request)
    {
        $user = Auth::user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
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

    public function handleBalances(User $user)
    {
        $user->balance = 50000;
        $user->save();

        Asset::firstOrCreate([
            'user_id' => $user->id,
            'symbol' => 'BTC',
        ], [
            'amount' => 1,
            'locked_amount' => 0,
        ]);

        Asset::firstOrCreate([
            'user_id' => $user->id,
            'symbol' => 'ETH',
        ], [
            'amount' => 10,
            'locked_amount' => 0,
        ]);
    }
}
