<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'is_admin' => 'boolean', // Optional: Specify if the user is an admin
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_admin' => $request->is_admin ?? false, // Default to false if not provided
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            // Return success response
            return response()->json([
                'access_token' => $token,
                'user' => $user,
            ]);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Login an existing user.
     */
    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,client', // Validate role as admin or client
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 403);
        }

        $credentials = ['email' => $request->email, 'password' => $request->password];

        try {
            if (!auth()->attempt($credentials)) {
                return response()->json(['error' => 'Invalid Credentials'], 403);
            }

            $user = User::where('email', $request->email)->firstOrFail();

            // Check if the user's role matches the selected role
            if ($request->role === 'admin' && !$user->is_admin) {
                return response()->json(['error' => 'Unauthorized: You are not an admin'], 403);
            }

            if ($request->role === 'client' && $user->is_admin) {
                return response()->json(['error' => 'Unauthorized: You are not a client'], 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            // Return response
            return response()->json([
                'access_token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    /**
     * Logout the user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        // Return success response
        return response()->json([
            'message' => 'User has been logged out',
        ]);
    }
}
