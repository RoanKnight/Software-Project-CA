<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
  use HasFactory;

  protected $primaryKey = 'MPRN';
  public $incrementing = false;
  protected $keyType = 'string';

  protected $fillable = [
    'MPRN',
    'address',
    'EirCode',
    'deleted',
    'user_id'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function solarPanels()
  {
    return $this->hasMany(SolarPanel::class, 'location_MPRN', 'MPRN');
  }

  public function electricityUsages()
  {
    return $this->hasMany(ElectricityUsage::class, 'location_MPRN', 'MPRN');
  }

  public function carChargings()
  {
    return $this->hasMany(CarCharging::class, 'location_MPRN', 'MPRN');
  }
}
