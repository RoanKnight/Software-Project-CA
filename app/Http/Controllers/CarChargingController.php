<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarCharging;
use App\Models\Location;
use App\Services\OpenChargeMapService;
use App\Models\User;
use Carbon\Carbon;

class CarChargingController extends Controller
{
  protected $openChargeMapService;

  public function __construct(OpenChargeMapService $openChargeMapService)
  {
    // Middleware to ensure authentication is required for all methods
    $this->middleware('auth', ['except' => []]);
    // Middleware to ensure only admin users can access 'index' method
    $this->middleware('role:admin', ['only' => ['index']]);
    $this->openChargeMapService = $openChargeMapService;
  }

  // Display a listing of the resource.
  public function index()
  {
    $carChargings = CarCharging::all();
    $locations = Location::all();
    return view('carCharging.index', [
      'carChargings' => $carChargings,
      'locations' => $locations
    ]);
  }

  // Display car charging records for user's locations.
  public function LocationsCarChargings()
  {
    $locationCarChargings = auth()->user()->locations;
    return view('carCharging.location_carChargings', [
      'location_car_charging' => $locationCarChargings
    ]);
  }

  // Store a newly created car charging record.
  public function store(Request $request)
  {
    $user = auth()->user();
    $activeLocation = Location::where('MPRN', $user->active_MPRN)->first();

    // Check if an active location exists and is not deleted
    if ($activeLocation && !$activeLocation->deleted) {
      $chargingRatePerMinute = rand(7, 11) / 60;

      $carCharging = new CarCharging;
      $carCharging->start_time = now();
      $carCharging->end_time = now()->addMinutes(rand(10, 120));
      $carCharging->location_MPRN = $activeLocation->MPRN;

      // Calculate charging amount based on charging time and rate
      $chargingTimeInMinutes = $carCharging->start_time->diffInMinutes($carCharging->end_time, true);
      $carCharging->charging_amount = $chargingTimeInMinutes * $chargingRatePerMinute;

      // Save the car charging record
      $carCharging->save();


      return redirect()->route('carCharging.index')->with('status', 'Created a new charging station for active location');
    }

    // Redirect to the 'carCharging.index' route with a failure message
    return redirect()->route('carCharging.index')->with('status', 'Failed to create charging station for active location');
  }

  // Retrieve car charging data for the active user's location.
  public function getChargingData()
  {
    $activeLocationMPRN = auth()->user()->active_MPRN;

    $location = Location::where('MPRN', $activeLocationMPRN)->first();

    // If no location found, return an error
    if (!$location) {
      return response()->json(['error' => 'No active location found.'], 404);
    }

    // Fetch the charging data for the active location
    $chargingData = CarCharging::where('location_MPRN', $activeLocationMPRN)->get();

    // Return the charging data as JSON response
    return response()->json($chargingData);
  }

  // Display the dashboard with car charging records.
  public function dashboard()
  {
    $user = auth()->user();
    $activeLocation = Location::where('MPRN', $user->active_MPRN)->first();

    // Initialize collections for car charging data
    $carChargings = collect();
    $recentCarChargings = collect();

    if ($activeLocation) {
      $carChargings = CarCharging::where('location_MPRN', $activeLocation->MPRN)->get();
      // Retrieve the most recent car charging records for the active location
      $recentCarChargings = CarCharging::where('location_MPRN', $activeLocation->MPRN)
        ->latest()
        ->take(7)
        ->get();
    }

    return view('carCharging.dashboard', [
      'user' => $user,
      'carChargings' => $carChargings,
      'recentCarChargings' => $recentCarChargings,
      'activeLocation' => $activeLocation,
    ]);
  }

  public function chargingStations()
  {
    $stations = $this->openChargeMapService->getStations(53.3498, -6.2603, 10);

    return view('carCharging.chargingStations', [
      'stations' => $stations
    ]);
  }

  // Display the specified car charging record.
  public function show(string $id)
  {
    $carCharging = CarCharging::find($id);
    return view('carCharging.show', [
      'carCharging' => $carCharging,
    ]);
  }
}
