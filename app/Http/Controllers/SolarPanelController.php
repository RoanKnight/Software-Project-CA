<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class SolarPanelController extends Controller
{
  public function getCurrentDateTime()
  {
    $this->updateSolarData();
    return Carbon::now()->format('d-m-Y H:i:s');
  }

  public function updateSolarData()
  {
    $path = resource_path('js/solar.json');
    $data = json_decode(file_get_contents($path), true);
    $currentDate = Carbon::now()->format('d-m-Y');

    $dateEntry = null;
    foreach ($data as &$entry) {
      if ($entry['date'] === $currentDate) {
        $dateEntry = &$entry;
        break;
      }
    }

    if ($dateEntry === null) {
      // If the date entry does not exist, create it with an empty hours array
      $dateEntry = [
        'date' => $currentDate,
        'hours' => []
      ];
      $data[] = &$dateEntry;
    } else {
      // If the date entry exists, add the next hour entry only if the last hour is not '23:00'
      $lastHourEntry = end($dateEntry['hours']);
      if ($lastHourEntry['hour'] === '23:00') {
        return response()->json('Cannot add any more objects for the day', 200);
      }
      $nextHour = $lastHourEntry ? ((int)substr($lastHourEntry['hour'], 0, 2) + 1) % 24 : 0;
      $dateEntry['hours'][] = [
        'hour' => sprintf('%02d:00', $nextHour),
        'energyGeneration_kwh' => rand(200, 500) / 10.0,
      ];
    }

    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
  }
}
