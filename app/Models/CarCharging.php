<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarCharging extends Model
{
  use HasFactory;

  protected $fillable = [
    'start_time',
    'end_time',
    'charging_amount',
    'location_MPRN'
  ];

  public function location()
  {
    return $this->belongsTo(Location::class, 'location_MPRN', 'MPRN');
  }
}
