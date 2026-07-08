<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        // Send password reset link
        $status = Password::sendResetLink($validated);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Password reset link sent to your email'
            ]);
        }

        return response()->json([
            'message' => 'Unable to send reset link'
        ], Response::HTTP_BAD_REQUEST);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $validated,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password reset successful'
            ]);
        }

        return response()->json([
            'message' => 'Invalid or expired reset token'
        ], Response::HTTP_BAD_REQUEST);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        // Revoke all tokens to force re-login
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password changed successfully. Please login again.'
        ]);
    }
}