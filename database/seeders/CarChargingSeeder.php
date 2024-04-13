<?php

namespace Database\Seeders;

use App\Models\CarCharging;
use Illuminate\Database\Seeder;

class CarChargingSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run(): void
  {
    $carChargings = [
      [
        'start_time' => now(),
        'end_time' => now()->addMinutes(rand(10, 120)),
        'location_MPRN' => '10000000001',
        'charging_amount' => rand(7, 11) / 60 * rand(10, 120),
      ],
      [
        'start_time' => now(),
        'end_time' => now()->addMinutes(rand(10, 120)),
        'location_MPRN' => '10000000001',
        'charging_amount' => rand(7, 11) / 60 * rand(10, 120),
      ],
      [
        'start_time' => now(),
        'end_time' => now()->addMinutes(rand(10, 120)),
        'location_MPRN' => '10000000001',
        'charging_amount' => rand(7, 11) / 60 * rand(10, 120),
      ],
      [
        'start_time' => now(),
        'end_time' => now()->addMinutes(rand(10, 120)),
        'location_MPRN' => '10000000001',
        'charging_amount' => rand(7, 11) / 60 * rand(10, 120),
      ],
      [
        'start_time' => now(),
        'end_time' => now()->addMinutes(rand(10, 120)),
        'location_MPRN' => '10000000001',
        'charging_amount' => rand(7, 11) / 60 * rand(10, 120),
      ],
      [
        'start_time' => now(),
        'end_time' => now()->addMinutes(rand(10, 120)),
        'location_MPRN' => '10000000001',
        'charging_amount' => rand(7, 11) / 60 * rand(10, 120),
      ]
    ];

    CarCharging::insert($carChargings);
  }
}