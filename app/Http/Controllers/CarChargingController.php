<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarCharging;
use App\Models\User;

class CarChargingController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth', ['except' => []]);
    $this->middleware('role:admin', ['only' => ['index']]);
  }

  public function index()
  {
    $carChargings = CarCharging::all();
    return view('carCharging.index', [
      'carChargings' => $carChargings
    ]);
  }

  public function store(Request $request)
  {
    $users = User::with('locations')->get();

    foreach ($users as $user) {
      foreach ($user->locations as $location) {
        if ($location->deleted) {
          continue;
        }

        $chargingRatePerMinute = rand(7, 11) / 60;

        $carCharging = new CarCharging;
        $carCharging->start_time = now();
        $carCharging->end_time = now()->addMinutes(rand(10, 120));
        $carCharging->location_MPRN = $location->MPRN;

        $chargingTimeInMinutes = $carCharging->start_time->diffInMinutes($carCharging->end_time, true);
        $carCharging->charging_amount = $chargingTimeInMinutes * $chargingRatePerMinute;

        $carCharging->save();
      }
    }

    return redirect()->route('carCharging.index')->with('status', 'Created a new charging station');
  }

  public function show(string $id)
  {
  }
}