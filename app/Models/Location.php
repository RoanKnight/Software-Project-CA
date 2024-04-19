<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
  use HasFactory;

  // Define the primary key field
  protected $primaryKey = 'MPRN';

  // Indicates if the IDs are auto-incrementing
  public $incrementing = false;

  // Define the data type of the primary key
  protected $keyType = 'string';

  // The attributes that are mass assignable
  protected $fillable = [
    'MPRN',
    'address',
    'EirCode',
    'deleted',
    'user_id'
  ];

  // A location belongs to a user
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  // A location can have many solar panels
  public function solarPanels()
  {
    return $this->hasMany(SolarPanel::class, 'location_MPRN', 'MPRN');
  }

  // A location can have many electricity usages
  public function electricityUsages()
  {
    return $this->hasMany(ElectricityUsage::class, 'location_MPRN', 'MPRN');
  }

  // A location can have many car chargings
  public function carChargings()
  {
    return $this->hasMany(CarCharging::class, 'location_MPRN', 'MPRN');
  }
}
