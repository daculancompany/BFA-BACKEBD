<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\BuildingOwnerController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BuildingInfoController;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// User Registration and Login Routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Building Owner Registration and Login Routes (public)
Route::post('/building-owner/register', [BuildingOwnerController::class, 'register']);
Route::post('/building-owner/login', [BuildingOwnerController::class, 'login']);

// Building Routes
Route::post('/buildings/register', [BuildingController::class, 'register']);
Route::get('/buildings', [BuildingController::class, 'index']);
Route::get('/buildings-all', [BuildingController::class, 'buildingsAll']);
// Booking Routes with additional custom routes for booking actions
Route::apiResource('bookings', BookingController::class);
Route::get('/bookings/{id}', [BookingController::class, 'show']);
Route::put('/bookings/{id}/approve', [BookingController::class, 'approveBooking']);
Route::put('/bookings/{bookingId}/deploy', [BookingController::class, 'deploy']);

// Personnel Registration and Login Routes
Route::get('/personnel', [PersonnelController::class, 'index']);
Route::post('/personnel/register', [PersonnelController::class, 'register']);
Route::post('/personnel/login', [PersonnelController::class, 'login']);
Route::get('/my-booking', [PersonnelController::class, 'myBooking']);

// Protected Routes (requires Sanctum authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Building Owner Logout
    Route::post('/building-owner/logout', [BuildingOwnerController::class, 'logout']);
    Route::get('/building-owners', [BuildingOwnerController::class, 'index']);

    // Get authenticated building owner details
    Route::get('/building-owner', [BuildingOwnerController::class, 'getBusinessOwner']);

    // Get authenticated user data
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Building Info Route (Ensure the controller exists)
Route::post('/save-inpection', [BuildingInfoController::class, 'store']);
Route::get('/building-info', [BuildingInfoController::class, 'index']);  // Fetch all building info
Route::post('/inspection-status', [BuildinginfoController::class, 'updateInspectionStatus']);
Route::get('/notification', [BookingController::class, 'notification']);
Route::get('/update-notification', [BookingController::class, 'updateNotification']);