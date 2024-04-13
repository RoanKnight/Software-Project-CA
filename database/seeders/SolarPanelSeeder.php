<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\SolarPanel;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Seeder;

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

    SolarPanel::insert($solarPanels);

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
