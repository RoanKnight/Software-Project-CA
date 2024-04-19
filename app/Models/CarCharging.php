<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarCharging extends Model
{
    use HasFactory;

    // The attributes that are mass assignable
    protected $fillable = [
        'start_time',
        'end_time',
        'charging_amount',
        'location_MPRN'
    ];

    // car charging record belongs to a location
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_MPRN', 'MPRN');
    }
}
