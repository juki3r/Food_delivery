<?php

namespace App\Http\Controllers;

use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RiderController extends Controller
{
    public function register(Request $request)
    {
        // Validation (this will automatically check if the phone number is unique)
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:riders,phone',
            'password' => 'required|string|min:6',
        ]);

        try {
            // Create the rider if validation passes
            $rider = Rider::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Rider registered successfully',
                'rider' => $rider,
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


}
