<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarCharging;
use App\Models\Location;
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
    $locations = Location::all();
    return view('carCharging.index', [
      'carChargings' => $carChargings,
      'locations' => $locations
    ]);
  }

  public function LocationsCarChargings()
  {
    $locationCarChargings = auth()->user()->locations;
    return view('carCharging.location_carChargings', [
      'location_car_charging' => $locationCarChargings
    ]);
  }

  public function store(Request $request)
  {
    $user = auth()->user();
    $activeLocation = Location::where('MPRN', $user->active_MPRN)->first();

    if ($activeLocation && !$activeLocation->deleted) {
      $chargingRatePerMinute = rand(7, 11) / 60;

      $carCharging = new CarCharging;
      $carCharging->start_time = now();
      $carCharging->end_time = now()->addMinutes(rand(10, 120));
      $carCharging->location_MPRN = $activeLocation->MPRN;

      $chargingTimeInMinutes = $carCharging->start_time->diffInMinutes($carCharging->end_time, true);
      $carCharging->charging_amount = $chargingTimeInMinutes * $chargingRatePerMinute;

      $carCharging->save();

      return redirect()->route('carCharging.index')->with('status', 'Created a new charging station for active location');
    }

    return redirect()->route('carCharging.index')->with('status', 'Failed to create charging station for active location');
  }

  public function dashboard()
  {
    $user = auth()->user();
    $activeLocation = Location::where('MPRN', $user->active_MPRN)->first();

    $carChargings = collect();

    if ($activeLocation) {
      $carChargings = CarCharging::where('location_MPRN', $activeLocation->MPRN)->get();
    }

    return view('carCharging.dashboard', [
      'user' => $user,
      'carChargings' => $carChargings,
      'activeLocation' => $activeLocation,
    ]);
  }

  public function show(string $id)
  {
    $carCharging = CarCharging::find($id);
    return view('carCharging.show', [
      'carCharging' => $carCharging
    ]);
  }
}