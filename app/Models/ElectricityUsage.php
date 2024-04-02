<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricityUsage extends Model
{
  use HasFactory;

  protected $fillable = [
    'location_MPRN',
    'deleted'
  ];

  public function location()
  {
    return $this->belongsTo(Location::class, 'location_MPRN', 'MPRN');
  }
}
