<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\Booking;
use App\Models\Buildinginfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PersonnelController extends Controller
{
    // Register a new personnel
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:personnel,email', // Corrected table and column reference
            'password' => 'required|string|min:8',
        ]);

        $personnel = Personnel::create([
            'name' => $request->name,
            'address' => $request->address,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $personnel->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
        ], 201);
    }

    // Login an existing personnel
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $personnel = Personnel::where('email', $request->email)->first(); // Fixed capitalization

        if ($personnel && Hash::check($request->password, $personnel->password)) {
            $token = $personnel->createToken('auth_token')->plainTextToken; // Token naming consistency
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

    // Get all personnel
    public function index()
    {
        $personnel = Personnel::all();
        return response()->json($personnel, 200);
    }


    public function myBooking(Request $request)
    {
        $bookings = Booking::with(['building.owner'])
            ->where('personnel_id', $request->user_id)
            ->get();

        foreach ($bookings as $booking) {
            // Add Buildinginfo for each booking
            $booking->buildingInfo = Buildinginfo::with('images')->where('booking_id', $booking->id)->first();
        }
        return response()->json([
            'success' => true,
            'bookings' => $bookings,
        ], 200);
    }
}
