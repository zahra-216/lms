<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Laravel\Sanctum\HasApiTokens;

/**
 * Authentication Controller
 * Handles user registration, login, logout, and token management
 */
class AuthController extends Controller
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Get student role
        $studentRole = Role::where('name', 'student')->first();
        if (!$studentRole) {
            return response()->json(['message' => 'Student role not found'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $studentRole->id,
        ]);

        // Create user profile
        $user->profile()->create([]);

        // Create token
        /** @var \Laravel\Sanctum\NewAccessToken $token */
        $token = $user->createToken('auth-token');

        // Log registration activity
        $this->activityLogService->logAuthActivity('register', $user, $request);

        return response()->json([
            'user' => $user->load(['role', 'profile']),
            'token' => $token->plainTextToken,
            'message' => 'Registration successful'
        ], Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($validated)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        /** @var User $user */
        $user = Auth::user();
        /** @var \Laravel\Sanctum\NewAccessToken $token */
        $token = $user->createToken('auth-token');

        // Log login activity
        $this->activityLogService->logAuthActivity('login', $user, $request);

        return response()->json([
            'user' => $user->load(['role', 'profile']),
            'token' => $token->plainTextToken,
            'message' => 'Login successful'
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        
        // Revoke the current access token
        $user->currentAccessToken()->delete();

        // Log logout activity
        $this->activityLogService->logAuthActivity('logout', $user, $request);

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    public function logoutAll(Request $request)
    {
        // Revoke all tokens for the user
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out from all devices'
        ]);
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        
        // Revoke current token
        $user->currentAccessToken()->delete();
        
        // Create new token
        /** @var \Laravel\Sanctum\NewAccessToken $token */
        $token = $user->createToken('auth-token');

        return response()->json([
            'user' => $user->fresh()->load(['role', 'profile']),
            'token' => $token->plainTextToken,
            'message' => 'Token refreshed successfully'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load(['role', 'profile']));
    }
}