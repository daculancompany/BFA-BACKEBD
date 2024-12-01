<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Personnel;
use App\Models\Buildinginfo;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Store a new booking
    public function store(Request $request)
    {
       // return $request->all();
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'buildings_id' => 'required|exists:buildings,id', // Ensure 'buildings_id' is provided and exists in the 'buildings' table
            'type' => 'required|in:permits,survey', // Validate 'type' is either 'permits' or 'survey'
            'appointment_date' => 'required|date', // Validate that 'appointment_date' is a valid date
            'status' => 'sometimes|in:pending,approved,canceled', // Optional status validation
            //'approved_by_admin_id' => 'nullable|exists:users,id', // Optional, but if provided, must exist in 'users' table
        ]);

        // If validation fails, return the validation errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking = Booking::create($request->all());

        Notification::create([
            'user_id' => 1,
            'booking_id' => $booking->id,
            'type' => 'new-booking',
            'userType' => 'admin',
            'data' => 'New Booking',
            'is_read' => 0,

        ]);

        // Create the booking record with the request data
        

        // Return a success response with the created booking data
        return response()->json([
            'message' => 'Booking created successfully! Awaiting approval.',
            'booking' => $booking,
        ], 201);
    }

    // Get all bookings with additional data (personnel name, building name, and formatted appointment date)
    public function index()
    {
        $bookings = Booking::with(['personnel', 'building', 'approvedBy'])->get()->map(function ($booking) {
            // Add the formatted date and other relevant details
            $booking->formatted_date = Carbon::parse($booking->appointment_date)->format('Y-m-d H:i:s');
            $booking->building_info = $booking->building ? $booking->building->name : 'No building assigned';
            $booking->personnel_name = $booking->personnel ? $booking->personnel->name : 'Not assigned';
            $booking->buildingInfo = Buildinginfo::with(['images'])->where('booking_id', $booking->id)->first();
            return $booking;
        });
        

        return response()->json($bookings);
    }

    // Get a single booking by ID with additional data
    public function show($id)
    {
        $booking = Booking::with(['personnel', 'building', 'approvedBy'])->findOrFail($id);

        // Add the formatted date and other relevant details
        $booking->formatted_date = Carbon::parse($booking->appointment_date)->format('Y-m-d H:i:s');
        $booking->building_info = $booking->building ? $booking->building->name : 'No building assigned';
        $booking->personnel_name = $booking->personnel ? $booking->personnel->name : 'Not assigned';

        return response()->json($booking);
    }

    // Update an existing booking
    public function update(Request $request, $id)
    {
        Log::info('Update booking request:', $request->all());

        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,canceled,deployed', // Status validation with deployed added
            'approved_by_admin_id' => 'required_if:status,approved|exists:users,id', // Only required if status is 'approved'
            'personnel_id' => 'nullable|exists:personnel,id', // Personnel ID is optional but must exist if provided
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the booking record by ID
        $booking = Booking::findOrFail($id);

        // Update the status
        $status = $request->input('status');
        $booking->status = (string) $status; // Explicitly cast status to string to avoid database issues

        // Handle specific statuses (approved, canceled, deployed)
        if ($status === 'approved') {
            $booking->approved_by_admin_id = $request->input('approved_by_admin_id');
            // Deploy personnel if personnel ID is provided
            if ($request->has('personnel_id')) {
                $this->deployPersonnel($request->input('personnel_id'), $booking);
            }
        } elseif ($status === 'canceled') {
            $booking->approved_by_admin_id = null;
            $booking->personnel_id = null;
        }

        // Save the updated booking record
        $booking->save();

        // Return a success response with the updated booking
        return response()->json([
            'message' => 'Booking updated successfully!',
            'booking' => $booking,
        ]);
    }

    // Delete a booking
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully!']);
    }

    // Deploy personnel to a booking
    public function deploy(Request $request, $bookingId)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'personnel_id' => 'required|exists:personnel,id', // Personnel ID must exist in the 'personnel' table
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the booking and personnel records
        $booking = Booking::findOrFail($bookingId);
        $personnel = Personnel::findOrFail($request->personnel_id);

        // Update the booking with the deployed personnel
        $booking->update([
            'personnel_id' => $personnel->id,
            'status' => 'deployed', // Ensure 'deployed' is a valid status in your database
        ]);

        // Return success response
        return response()->json(['message' => 'Personnel deployed successfully.', 'booking' => $booking]);
    }

    // Approve a booking
    public function approveBooking($id, Request $request)
    {
        // Validate the incoming approval request
        $request->validate([
            'status' => 'required|in:approved', // Status must be 'approved'
            'approved_by_admin_id' => 'required|exists:users,id', // Admin ID must exist in the 'users' table
        ]);

        // Find the booking record and update its status
        $booking = Booking::findOrFail($id);
        $booking->status = $request->input('status');
        $booking->approved_by_admin_id = $request->input('approved_by_admin_id');
        $booking->save();

        // Return success response
        return response()->json(['message' => 'Booking approved successfully', 'booking' => $booking], 200);
    }

    public function notification(){
        return response()->json(['unreadCount' => Notification::where('is_read',0)->where('userType','admin')->count(), 'notifications' =>  Notification::with(['booking.building.owner'])->latest()->get()], 200);
    }

    public function updateNotification(){
        Notification::where('userType', 'admin')
        ->update(['is_read' =>1]);
    }

    

    
}
