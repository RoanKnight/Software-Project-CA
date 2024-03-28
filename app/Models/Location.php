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
    return $this->hasMany(SolarPanel::class);
  }
}
