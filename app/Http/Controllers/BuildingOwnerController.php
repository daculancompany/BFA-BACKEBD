<?php

namespace App\Http\Controllers;

use App\Models\BuildingOwner;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BuildingOwnerController extends Controller
{
    // Register a new building owner
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:building_owners',
            'password' => 'required|string|min:8',
        ]);

        $buildingOwner = BuildingOwner::create([
            'name' => $request->name,
            'address' => $request->address,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $buildingOwner->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token
        ], 201);
    }

    // Login an existing building owner
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        if($request->role === 'Building Owner'){
            $buildingOwner = BuildingOwner::where('email', $request->email)->first();
        }else{
            $buildingOwner = Personnel::where('email', $request->email)->first();
        }
       

        if ($buildingOwner && Hash::check($request->password, $buildingOwner->password)) {
            $token = $buildingOwner->createToken('authToken')->plainTextToken;
            return response()->json(['token' => $token, 'user' =>  $buildingOwner], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

    // Get a list of all building owners with specific details
    public function index()
    {
        // Retrieve only 'name', 'address', and 'email' for each building owner
        $buildingOwners = BuildingOwner::select('name', 'address', 'email')->get();

        return response()->json($buildingOwners, 200);
    }

    // Get authenticated building owner's details
    public function getBusinessOwner()
    {
        // Retrieve the authenticated building owner
        $buildingOwner = Auth::user();

        return response()->json([
            'name' => $buildingOwner->name,
            'email' => $buildingOwner->email,
            'address' => $buildingOwner->address,
        ], 200);
    }
}
