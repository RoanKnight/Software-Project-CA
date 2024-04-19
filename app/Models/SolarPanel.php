<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolarPanel extends Model
{
  use HasFactory;

  // The attributes that are mass assignable
  protected $fillable = [
    'location_MPRN',
    'deleted'
  ];

  // A solar panel belongs to a location
  public function location()
  {
    return $this->belongsTo(Location::class, 'location_MPRN', 'MPRN');
  }
}
