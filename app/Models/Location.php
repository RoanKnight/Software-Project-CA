<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
  use HasFactory;

  protected $fillable = [
    'address',
    'MPRN',
    'user_id',
    'deleted'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function solarPanels()
  {
    return $this->hasMany(SolarPanel::class);
  }
}
