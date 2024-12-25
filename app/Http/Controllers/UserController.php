<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get all non-admin users (staff members).
     */
    public function index()
    {
        // Return all users where is_admin is false
        return User::where('is_admin', false)->get();
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        // Validate the request input
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed', // Requires new_password_confirmation to match new_password
        ], [
            'old_password.required' => 'The current password is required.',
            'new_password.required' => 'The new password is required.',
            'new_password.min' => 'The new password must be at least 8 characters.',
            'new_password.confirmed' => 'The new password confirmation does not match.',
        ]);

        $user = $request->user(); // Retrieve the authenticated user

        // Check if the old password is correct
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['error' => 'The current password is incorrect.'], 422);
        }

        // Update the user's password
        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password updated successfully.']);
    }


    /**
     * Get the authenticated user's information.
     */
    public function show(Request $request)
    {
        return response()->json($request->user());
    }
}
