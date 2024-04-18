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
    for ($i = 0; $i < 50; $i++) {
      $startTime = now()->subDays(rand(0, 30));
      $endTime = (clone $startTime)->addMinutes(rand(10, 120));

      CarCharging::create([
        'start_time' => $startTime,
        'end_time' => $endTime,
        'location_MPRN' => '10000000001',
        'charging_amount' => rand(7, 11) / 60 * rand(10, 120),
      ]);
    }
  }
}