<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;

    // Define the fillable attributes
    protected $fillable = [
        'image',
        'info_id',
    ];

    

}
