<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
}
