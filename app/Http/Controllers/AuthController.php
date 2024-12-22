<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // $validated=$request->validate([
        //     'name'=>'required|string|max:255',
        //     'email'=>'required|string||email|max:255|unique:users',
        //     'password'=>'required|string|min:6|confirmed',
        // ]);
        
        $validated=Validator::make($request->all(), [
          'name'=>'required|string|max:255',
            'email'=>'required|string||email|max:255|unique:users',
            'password'=>'required|string|min:6|confirmed',
        ]);
        
        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }

        try {
            $user= User::create([
           'name'=>$request->name,
            'email'=>$request->email,
           'password'=> Hash::make($request->password),
       ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            //return
            return response()->json([
                'access_token'=>$token,
                'user'=>$user
            ]);

        } catch (\Exception $th) {
            return response()->json(['error'=>$th->getMessage()]);
        }

       
    }
    //login
    public function login(Request $request)
    {

        $validated=Validator::make($request->all(), [
         'email'=>'required|string||email',
         'password'=>'required|string|min:6',
     ]);
        
        if ($validated->fails()) {
            return response()->json($validated->errors(), 403);
        }


        $credentials=['email'=>$request->email,'password'=>$request->password];
        try {
            if (!auth()->attempt($credentials)) {
                return response()->json(['error'=>'invalid Credentials'], 403);
            }

            $user=User::where('email', $request->email)->firstOrFail();

            $token=$user->createToken('auth_token')->plainTextToken;

            //return
            return response()->json([
            'access_token'=>$token,
            'user'=>$user
            ]);

        

        } catch (\Exception $th) {
            return response()->json(['error'=>$th->getMessage()]);

        }
    }
    //logout

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();


        //return
        return response()->json([
        'message'=>'user has been logged out'
        ]);

    }

}
