<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Sign up a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Save the new user with their details
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'is_admin' => false, // By default, they are NOT an admin
        ]);

        // Give them a login token right away
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
            'message' => 'Registration successful',
        ], 201);
    }

    /**
     * Log in a user and give them a token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        // Look for a user with this email
        $user = User::where('email', $credentials['email'])->first();

        // Check if the password matches
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email or password is incorrect.'],
            ]);
        }

        // Remove any old tokens (so the user only stays logged in once)
        $user->tokens()->delete();

        // Make a fresh token for this login
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
            'message' => 'Login successful',
        ]);
    }

    /**
     * Log out the user (remove their current token).
     */
    public function logout(Request $request): JsonResponse
    {
        // Delete the token they are using right now
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get the currently logged-in user’s info.
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }
}
