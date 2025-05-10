<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Rider;
use Illuminate\Support\Str;
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

    public function riderlogin(Request $request)
    {
        // Validate input
        $request->validate([
            'phone' => 'required',
            'password' => 'required'
        ]);

        // Find rider by phone
        $rider = Rider::where('phone', $request->phone)->first();

        // Check if rider exists and password is correct
        if (!$rider || !Hash::check($request->password, $rider->password)) {
            return response()->json(['message' => 'Invalid phone or password'], 401);
        }

        // Generate a random token
        $rider->api_token = Str::random(60);
        $rider->save();

        // Success response (you can return more data)
        return response()->json([
            'message' => 'Login successful',
            'rider' => $rider,
            'token' => $rider->api_token,
        ]);
    }

    //Update Location of the rider
    public function updateLocation(Request $request)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['message' => 'Token required'], 401);
        }

        $rider = Rider::where('api_token', $token)->first();

        if (!$rider) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $rider->latitude = $request->latitude;
        $rider->longitude = $request->longitude;
        $rider->save();

        return response()->json(['message' => 'Location updated']);
    }

    public function updateStatus(Request $request)
    {
        $bearerToken = $request->header('Authorization');

        if (!$bearerToken) {
            return response()->json(['message' => 'Token required'], 401);
        }

        // Remove "Bearer " prefix
        $token = str_replace('Bearer ', '', $bearerToken);

        $rider = Rider::where('api_token', $token)->first();

        if (!$rider) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $request->validate([
            'status' => 'required|in:online,offline',
        ]);

        $rider->status = $request->status;
        $rider->save();

        return response()->json(['message' => 'Status updated successfully.']);
    }

    public function getStatus(Request $request)
    {
        // Get token from the Authorization header
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Authorization token missing'], 401);
        }

        // Extract token
        $token = str_replace('Bearer ', '', $authHeader);

        // Find the rider by token
        $rider = Rider::where('api_token', $token)->first();

        if (!$rider) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        // Return rider profile
        return response()->json([
            'name' => $rider->name,
            'phone' => $rider->phone,
            'status' => $rider->status,
        ]);
    }


    public function getPendingOrders(Request $request)
    {
        // Get token from the Authorization header
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Authorization token missing'], 401);
        }

        // Extract token
        $token = str_replace('Bearer ', '', $authHeader);

        // Find the rider by token
        $rider = Rider::where('api_token', $token)->first();

        if (!$rider) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        // Fetch orders where status is 'pending' and assigned to the rider
        $orders = Order::where('status', 'pending')->where('rider_id', $rider->id)->get();

        // Return the orders as a JSON response
        return response()->json($orders);
    }
}
