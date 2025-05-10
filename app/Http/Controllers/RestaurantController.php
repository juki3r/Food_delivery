<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RestaurantController extends Controller
{
    public function register(Request $request)
    {
        // Validation (this will automatically check if the phone number is unique)
        $request->validate([
            'restaurant_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|unique:restaurants,phone',
            'password' => 'required|string|min:8',
        ]);

        try {
            // Create the rider if validation passes
            $restaurant = Restaurant::create([
                'restaurant_name' => $request->restaurant_name,
                'owner_name' => $request->owner_name,
                'address' => $request->address,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Restaurant registered successfully',
                'restaurant' => $restaurant,
            ], 201);
        } catch (\Exception $e) {
            // In case of error, return failure response
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed. Please try again later.',
                'error' => $e->getMessage(), // Optional: remove in production
            ], 500);
        }
    }

    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'phone' => 'required',
            'password' => 'required'
        ]);

        // Find rider by phone
        $restaurant = Restaurant::where('phone', $request->phone)->first();

        // Check if rider exists and password is correct
        if (!$restaurant || !Hash::check($request->password, $restaurant->password)) {
            return response()->json(['message' => 'Invalid phone or password'], 401);
        }

        // Generate a random token
        $restaurant->api_token = Str::random(60);
        $restaurant->save();

        // Success response (you can return more data)
        return response()->json([
            'message' => 'Login successful',
            'restaurant' => $restaurant,
            'token' => $restaurant->api_token,
        ]);
    }
}
