<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'buildings_id',
        'type',
        'appointment_date',
        'status',
        'approved_by_admin_id',
        'personnel_id',
    ];

    // Accessor to check if the booking is approved
    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }
    public function getFormattedAppointmentDateAttribute()
    {
        return \Carbon\Carbon::parse($this->appointment_date)->format('Y-m-d H:i:s');
    }
    // Define relationships if applicable
    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class, 'buildings_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_admin_id');
    }

   
}
