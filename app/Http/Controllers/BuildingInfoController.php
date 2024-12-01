<?php

namespace App\Http\Controllers;

use App\Models\BuildingInfo;
use App\Models\Booking;
use App\Models\Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Notification;

class BuildingInfoController extends Controller
{
    /**
     * Store building information.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */


    public function store(Request $request)
    {


        try {

            // Find the booking by ID
            $booking = Booking::find($request->input('booking_id'));

            if (!$booking) {
                return response()->json(['error' => 'Booking not found'], 404);
            }

            //save image

            // Update the status
            $booking->status = "completed";
            $booking->updated_at = now();
            $booking->save();

            // Set default values for nullable fields if not provided
            $fsicOccupancy = $request->input('fsicOccupancy') === 'true' ? 1 : 0;
            $fsicNewPermit = $request->input('fsicNewPermit') === 'true' ? 1 : 0;
            $fsicRenewPermit = $request->input('fsicRenewPermit') === 'true' ? 1 : 0;
            $fsicAnnualInspection = $request->input('fsicAnnualInspection') === 'true' ? 1 : 0;
            $verificationInspection = $request->input('verificationInspection') === 'true' ? 1 : 0;
            $ntc = $request->input('ntc') === 'true' ? 1 : 0;
            $ntcv = $request->input('ntcv') === 'true' ? 1 : 0;
            $abatement = $request->input('abatement') === 'true' ? 1 : 0;
            $closure = $request->input('closure') === 'true' ? 1 : 0;
            $disapproval = $request->input('disapproval') === 'true' ? 1 : 0;
            $others = $request->input('others') === 'true' ? 1 : 0;

            $mercantile = $request->input('mercantile') === 'true' ? 1 : 0;
            $business = $request->input('business') === 'true' ? 1 : 0;
            $reinforcedconcrete = $request->input('reinforcedconcrete') === 'true' ? 1 : 0;
            $timberframedwalls = $request->input('timberframedwalls') === 'true' ? 1 : 0;
            $steel = $request->input('steel') === 'true' ? 1 : 0;
            $mixed = $request->input('mixed') === 'true' ? 1 : 0;

            // Create a new BuildingInfo record
            $buildingInfo = BuildingInfo::create([
                'booking_id' => $request->input('booking_id'),
                'remarks' =>  $request->input('inspectionResult'),
                'reasons' =>  $request->input('failureReason'),
                'building_name' => $request->input('buildingName'),
                'address' => $request->input('address'),
                'business_name' => $request->input('businessName'),
                'nature_of_business' => $request->input('natureOfBusiness'),
                'owner_name' => $request->input('ownerName'),
                'fsec_no' => $request->input('fsecNo'),
                'building_permit' => $request->input('buildingPermit'),
                'fsic_no' => $request->input('fsicNo'),
                'business_permit_no' => $request->input('businessPermitNo'),
                'fire_insurance_no' => $request->input('fireInsuranceNo'),
                'contact_no' => $request->input('contactNo'),
                'inspection_order_no' => $request->input('inspectionOrderNo'),
                //'date_issued' => $request->input('dateIssued'),
                //'date_inspected' => $request->input('dateInspected'),
                //'inspection_during_construction' => $request->input('inspectionDuringConstruction', 0), // Default to 0 if null
                'fsic_occupancy' => $fsicOccupancy,
                'fsic_new_permit' => $fsicNewPermit,
                'fsic_renew_permit' => $fsicRenewPermit,
                'fsic_annual_inspection' => $fsicAnnualInspection,
                'verification_inspection' => $verificationInspection,
                'ntc' => $ntc,
                'ntcv' => $ntcv,
                'abatement' => $abatement,
                'closure' => $closure,
                'disapproval' => $disapproval,
                'others' => $others,
                'mercantile' => $mercantile,
                'business' => $business,
                'reinforcedconcrete' => $reinforcedconcrete,
                'timberframedwalls' => $timberframedwalls,
                'steel' => $steel,
                'mixed' => $mixed,


            ]);

            if ($request->hasFile('images')) {
                $savedPaths = [];
                foreach ($request->file('images') as $file) {
                    if ($file->isValid()) {
                        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('uploads'), $filename);
                        $savedPaths[] = url('uploads/' . $filename);
                        Images::create([
                            'info_id' => $buildingInfo->id,
                            'image' => $filename,
                        ]);
                    }
                }
            }
            $types = $request->input('inspectionResult') == 'fail' ? 'Fail Booking' : 'Pass Booking';

            Notification::create([
                'user_id' => 1,
                'booking_id' => $request->input('booking_id'),
                'type' => 'inspect-booking',
                'userType' => 'admin',
                'data' => $types,
                'is_read' => 0,
    
            ]);

            // Log successful creation of building info
            Log::info('Building Info successfully saved:', ['id' => $buildingInfo->id]);

            // Return success response with created building info
            return response()->json([
                'message' => 'Building information saved successfully',
                'data' => $buildingInfo
            ], 201);
        } catch (\Exception $e) {
            // Log the actual error
            Log::error('Failed to save building information:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return error response with appropriate status code
            return response()->json([
                'message' => 'Failed to save building information.',
                'error' => 'An unexpected error occurred. Please try again later.' // Generic message for users
            ], 500);
        }
    }

    // Retrieve all building information
    public function index()
    {
        // Fetch all building information from the database
        $buildings = BuildingInfo::all();

        // Log the response data for debugging
        Log::info('Fetched Building Info:', $buildings->toArray());

        // Transform field names before returning them (optional but helpful)
        $transformedBuildings = $buildings->map(function ($building) {
            return [
                'buildingName' => $building->building_name,
                'address' => $building->address,
                'businessName' => $building->business_name,
                'natureOfBusiness' => $building->nature_of_business,
                'ownerName' => $building->owner_name,
                'fsecNo' => $building->fsec_no,
                'buildingPermit' => $building->building_permit,
                'fsicNo' => $building->fsic_no,
                'businessPermitNo' => $building->business_permit_no,
                'fireInsuranceNo' => $building->fire_insurance_no,
                'contactNo' => $building->contact_no,
                'inspectionOrderNo' => $building->inspection_order_no,
                'dateIssued' => $building->date_issued,
                'dateInspected' => $building->date_inspected,
                'inspectionDuringConstruction' => $building->inspection_during_construction,
                'fsicOccupancy' => $building->fsic_occupancy,
                'fsicNewPermit' => $building->fsic_new_permit,
                'fsicRenewPermit' => $building->fsic_renew_permit,
                'fsicAnnualInspection' => $building->fsic_annual_inspection,
                'verificationInspection' => $building->verification_inspection,
                'ntc' => $building->ntc,
                'ntcv' => $building->ntcv,
                'abatement' => $building->abatement,
                'closure' => $building->closure,
                'disapproval' => $building->disapproval,
                'others' => $building->others,
                'mercantile' => $building->mercantile,
                'business' => $building->business,
                'reinforcedconcrete' => $building->reinforcedconcrete,
                'timberframedwalls' => $building->timberframedwalls,
                'steel' => $building->steel,
                'mixed' => $building->mixed,
            ];
        });

        // Return transformed data as JSON response
        return response()->json($transformedBuildings);
    }

    public function updateInspectionStatus(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'buildingId' => 'required|exists:building_info,id', // Ensure the building exists in the database
            'status' => 'required|in:Passed,Failed', // Ensure status is either "Passed" or "Failed"
        ]);

        // Find the building info by ID
        $building = Buildinginfo::find($request->buildingId);

        // Update the status of the building
        if ($building->updateStatus($request->status)) {
            // Return a success response
            return response()->json(['message' => 'Inspection status updated successfully.'], 200);
        }

        // Return an error if status is invalid
        return response()->json(['message' => 'Invalid status.'], 400);
    }
}
