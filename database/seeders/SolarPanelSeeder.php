<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\SolarPanel;
use App\Models\User;
use App\Models\Location;

class SolarPanelSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $solarPanels = [
      [
        'location_MPRN' => '10000000001',
      ],
    ];

    // Insert solar panel data into the database
    SolarPanel::insert($solarPanels);

    // Create directories and files for solar panel data
    foreach ($solarPanels as $solarData) {
      $location = Location::where('MPRN', $solarData['location_MPRN'])->first();
      if ($location) {
        $solarDirectory = 'users/' . $location->user->email . '/' . str_replace(' ', '_', $location->address);
        if (!Storage::exists($solarDirectory)) {
          Storage::makeDirectory($solarDirectory);
        }
        $solarJsonData = json_encode([]);
        Storage::put($solarDirectory . '/solar.json', $solarJsonData);
      }
    }
  }
}


