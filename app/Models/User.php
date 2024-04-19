<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  // Traits used by the User model
  use HasApiTokens, HasFactory, Notifiable;

  // Constants defining user roles
  const ROLE_ADMIN = 'admin';
  const ROLE_USER = 'user';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'deleted',
    'active_MPRN'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token'
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed'
  ];

  // A user can have many locations
  public function locations()
  {
    return $this->hasMany(Location::class);
  }

  // A user has one active location
  public function activeLocation()
  {
    return $this->hasOne(Location::class, 'MPRN', 'active_MPRN');
  }

  // Check if the user is an admin
  public function isAdmin()
  {
    return $this->role === self::ROLE_ADMIN;
  }

  // Check if the user is a regular user
  public function isUser()
  {
    return $this->role === self::ROLE_USER;
  }
}
