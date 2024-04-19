<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\ElectricityUsage;
use App\Models\Location;

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

    // Insert electricity usage data into the database
    ElectricityUsage::insert($electricityUsages);

    // Create directories and files for electricity usage data
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

