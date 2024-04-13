<?php

namespace Database\Seeders;

use App\Models\ElectricityUsage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Location;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Database\Seeder;

class ElectricityUsageSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $electricityUsages = [
      [
        'location_MPRN' => '10000000001',
      ],
    ];

    ElectricityUsage::insert($electricityUsages);

    foreach ($electricityUsages as $electricityData) {
      $location = Location::where('MPRN', $electricityData['location_MPRN'])->first();
      if ($location) {
        $electricityDirectory = 'users/' . $location->user->email . '/' . str_replace(' ', '_', $location->address);
        if (!Storage::exists($electricityDirectory)) {
          Storage::makeDirectory($electricityDirectory);
        }
        $electricityJsonData = json_encode([]);
        Storage::put($electricityDirectory . '/electricity.json', $electricityJsonData);
      }
    }
  }
}
