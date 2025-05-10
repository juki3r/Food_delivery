<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function register(Request $request)
    {
        // Validate input
        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:6',
        ]);

        // Create user
        $user = User::create([
            'name'      => $request->name,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
        ]);

        // Return response
        return response()->json([
            'message' => 'User registered successfully.',
            'user'    => $user
        ], 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'phone'    => 'required',
            'password' => 'required',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate a new token
        $user->api_token = Str::random(60);
        $user->save();

        return response()->json([
            'message'   => 'Login successful',
            'api_token' => $user->api_token,
            'user'      => $user
        ]);
    }
}
