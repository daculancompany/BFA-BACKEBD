<?php

namespace App\Http\Controllers;

use App\Models\Building;
use APP\Models\Booking;
use App\Models\BuildingOwner; // Import the BuildingOwner model if needed
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BuildingController extends Controller
{
    /**
     * Register a new building.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'lat' => 'required|max:255',
            'lng' => 'required|max:255',
            'building_type' => 'required|string|max:100',
            'floors' => 'nullable|integer|min:0',
            'units' => 'nullable|integer|min:0',
            'construction_date' => 'nullable|date',

        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        // Fetch the building owner ID based on your logic
        $buildingOwner = BuildingOwner::first(); // Fetching the first building owner; adjust as necessary.
        $buildingOwnerId = $buildingOwner ? $buildingOwner->id : null; // Get the owner ID or null

        // Create a new building record
        $building = Building::create([
            'name' => $request->name,
            'address' => $request->address,
            'building_type' => $request->building_type,
            'building_owners_id' => $buildingOwnerId, // Automatically set the owner ID
            'floors' => $request->floors,
            'units' => $request->units,
            'construction_date' => $request->construction_date,
            'lat' => $request->lat,
            'lng' => $request->lng,
        ]);

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Building registered successfully.',
            'data' => $building,
        ], 201);
    }

    /**
     * Get a list of buildings.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Fetch all buildings
        if (!($request->user_id)) {
            $buildings = Building::get();
            // Return a success response
            
            return response()->json([
                'success' => true,
                'data' => $buildings,
            ], 200);
        } else {
            $buildings = Building::where('building_owners_id', $request->user_id)->get();
            // Return a success response
            return response()->json([
                'success' => true,
                'buildings' => $buildings,
            ], 200);
        }
    }

    public function buildingsAll(Request $request)
    {
        // Fetch all buildings
        $buildings = Building::get();

        // Return a success response
        return response()->json([
            'success' => true,
            'buildings' => $buildings,
        ], 200);
    }

    public function getBookings()
    {
        // Fetch bookings with their related personnel and building data
        $bookings = Booking::with(['personnel', 'building', 'approvedBy'])
            ->get()
            ->map(function ($booking) {
                // Add formatted time and date to the booking
                $booking->formatted_date = $booking->formatted_appointment_date;
                $booking->building_info = $booking->building ? $booking->building->name : 'No building assigned';

                return $booking;
            });

        // Return a success response with the bookings data
        return response()->json($bookings);
    }
}
