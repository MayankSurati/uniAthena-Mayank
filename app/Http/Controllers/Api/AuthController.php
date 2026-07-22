<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $register)
    {
        $user = User::create($register->validated());
        $token = $user->createToken('mobile-app')->plainTextToken;

        return $this->successResponse(
            [
                'user' => $user,
                'token' => $token,
            ],
            'User registered successfully.',
            201
        );
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return $this->errorResponse('Invalid credentials.', 401);
        }

        $user = Auth::user();
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse(
            [
                'user' => $user,
                'token' => $token,
            ],
            'User logged in successfully.'
        );
    }

    public function user(Request $request)
    {
        return $this->successResponse(
            $request->user(),
            'User fetched successfully.'
        );
    }
    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->successResponse(null, 'Logged out successfully.');
    }
}
