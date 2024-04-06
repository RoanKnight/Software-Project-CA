<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Location;
use App\Models\User;

class LocationSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run(): void
  {
    $locations = [
      [
        'MPRN' => '10000000001',
        'address' => '123 Main St',
        'EirCode' => 'A1B2C3D',
        'user_id' => 1
      ],
      [
        'MPRN' => '10000000002',
        'address' => '456 Broadway',
        'EirCode' => 'D4E5F6G',
        'user_id' => 1
      ],
      [
        'MPRN' => '10000000003',
        'address' => '789 Park Ave',
        'EirCode' => 'G7H8I9J',
        'user_id' => 2
      ],
    ];

    Location::insert($locations);

    foreach ($locations as $locationData) {
      $user = User::find($locationData['user_id']);
      $locationDirectory = 'users/' . $user->email . '/' . str_replace(' ', '_', $locationData['address']);
      if (!Storage::exists($locationDirectory)) {
        Storage::makeDirectory($locationDirectory);
      }
    }
  }
}