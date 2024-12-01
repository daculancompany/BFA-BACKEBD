<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buildinginfo extends Model
{
    use HasFactory;

    // Specify the table name if it does not follow the default plural naming convention
    protected $table = 'building_info';

    // Define the fillable attributes
    protected $fillable = [
        'booking_id',
        'building_name',
        'address',
        'business_name',
        'nature_of_business',
        'owner_name',
        'contact_no',
        'inspection_order_no',
        'date_issued',
        'date_inspected',
        'fsec_no',
        'building_permit',
        'fsic_no',
        'business_permit_no',
        'fire_insurance_no',
        'inspection_during_construction',
        'fsic_occupancy',
        'fsic_new_permit',
        'fsic_renew_permit',
        'fsic_annual_inspection',
        'verification_inspection',
        'ntc',
        'ntcv',
        'abatement',
        'closure',
        'disapproval',
        'others',
        'mercantile',
        'business',
        'reinforcedconcrete',
        'timberframedwalls',
        'steel',
        'mixed',         // Added others (string field)'
        'remarks',
        'reasons',
    ];

    // Automatically cast attributes to appropriate types
    protected $casts = [
        'date_issued' => 'date',
        'date_inspected' => 'date',
        'inspection_during_construction' => 'boolean',
        'fsic_occupancy' => 'boolean',
        'fsic_new_permit' => 'boolean',
        'fsic_renew_permit' => 'boolean',
        'fsic_annual_inspection' => 'boolean',
        'verification_inspection' => 'boolean',
        'ntc' => 'boolean',
        'ntcv' => 'boolean',
        'abatement' => 'boolean',
        'closure' => 'boolean',
        'disapproval' => 'boolean',
        'mercantile' => 'integer',
        'business' => 'integer',
        'reinforcedconcrete' => 'integer',
        'timberframedwalls' => 'integer',
        'steel' => 'integer',
        'mixed' => 'integer',
        'remarks',
        'reasons',
        
    ];

    // Optional: If your table has created_at and updated_at fields, Laravel will handle them automatically.
    protected $dates = [
        'created_at',
        'updated_at',
        'date_issued',  // Ensure this is treated as a date object
        'date_inspected',  // Ensure this is treated as a date object
    ];

    /**
     * Relationship: A BuildingInfo belongs to a Personnel.
     */
    public function personnel()
    {
        return $this->belongsTo(Personnel::class); // Adjust if personnel ID is a different foreign key
    }

    /**
     * Relationship: A BuildingInfo belongs to a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class); // Adjust if user ID is a different foreign key
    }

    /**
     * Accessor: Format the contact number if needed (e.g., adding dashes or formatting it in a specific way).
     */
    public function getFormattedContactNoAttribute()
    {
        // Example: Format the contact number (adjust based on your format)
        return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $this->contact_no);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function updateStatus($status)
    {
        // Ensure the status is one of the valid values
        $validStatuses = ['Passed', 'Failed',];
        if (in_array($status, $validStatuses)) {
            $this->status = $status;
            $this->save(); // Save the status update to the database
            return true;
        }
        return false; // If the status is invalid, return false
    }

    public function images()
    {
        return $this->hasMany(Images::class, 'info_id');
    }
}

