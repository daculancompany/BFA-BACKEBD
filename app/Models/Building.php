<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    // Define the fillable attributes
    protected $fillable = [
        'name',
        'address',
        'building_type',
        'building_owners_id', // Still included for relationship purposes
        'floors',
        'units',
        'construction_date',
        'lat',
        'lng',
    ];

    /**
     * Relationship: A Building belongs to a BuildingOwner.
     */
    public function owner()
    {
        return $this->belongsTo(BuildingOwner::class, 'building_owners_id');
    }
}
