<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarCharging;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;

class CarChargingController extends Controller
{
  public function __construct()
  {
    // Middleware to ensure authentication is required for all methods except 'index'
    $this->middleware('auth', ['except' => []]);
    // Middleware to ensure only admin users can access 'index' method
    $this->middleware('role:admin', ['only' => ['index']]);
  }

  // Display a listing of the resource.
  public function index()
  {
    // Retrieve all car charging records and locations
    $carChargings = CarCharging::all();
    $locations = Location::all();
    // Return the 'carCharging.index' view with car charging records and location data
    return view('carCharging.index', [
      'carChargings' => $carChargings,
      'locations' => $locations
    ]);
  }

  // Display car charging records for user's locations.
  public function LocationsCarChargings()
  {
    // Retrieve car charging records for the authenticated user's locations
    $locationCarChargings = auth()->user()->locations;
    // Return the 'carCharging.location_carChargings' view with location car charging data
    return view('carCharging.location_carChargings', [
      'location_car_charging' => $locationCarChargings
    ]);
  }

  // Store a newly created car charging record.
  public function store(Request $request)
  {
    // Retrieve the authenticated user
    $user = auth()->user();
    // Retrieve the active location associated with the user
    $activeLocation = Location::where('MPRN', $user->active_MPRN)->first();

    // Check if an active location exists and is not deleted
    if ($activeLocation && !$activeLocation->deleted) {
      // Generate a random charging rate per minute
      $chargingRatePerMinute = rand(7, 11) / 60;

      // Create a new car charging record
      $carCharging = new CarCharging;
      $carCharging->start_time = now();
      $carCharging->end_time = now()->addMinutes(rand(10, 120));
      $carCharging->location_MPRN = $activeLocation->MPRN;

      // Calculate charging amount based on charging time and rate
      $chargingTimeInMinutes = $carCharging->start_time->diffInMinutes($carCharging->end_time, true);
      $carCharging->charging_amount = $chargingTimeInMinutes * $chargingRatePerMinute;

      // Save the car charging record
      $carCharging->save();

      // Redirect to the 'carCharging.index' route with a success message
      return redirect()->route('carCharging.index')->with('status', 'Created a new charging station for active location');
    }

    // Redirect to the 'carCharging.index' route with a failure message
    return redirect()->route('carCharging.index')->with('status', 'Failed to create charging station for active location');
  }

  // Retrieve car charging data for the active user's location.
  public function getChargingData()
  {
    // Fetch the active location MPRN from the User model
    $activeLocationMPRN = auth()->user()->active_MPRN;

    // Find the location with the active MPRN
    $location = Location::where('MPRN', $activeLocationMPRN)->first();

    // If no location found, return a 404 error response
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
    // Retrieve the authenticated user
    $user = auth()->user();
    // Retrieve the active location associated with the user
    $activeLocation = Location::where('MPRN', $user->active_MPRN)->first();

    // Initialize collections for car charging data
    $carChargings = collect();
    $recentCarChargings = collect();

    // Check if an active location exists
    if ($activeLocation) {
      // Retrieve all car charging records for the active location
      $carChargings = CarCharging::where('location_MPRN', $activeLocation->MPRN)->get();
      // Retrieve the most recent car charging records for the active location
      $recentCarChargings = CarCharging::where('location_MPRN', $activeLocation->MPRN)
        ->latest()
        ->take(7)
        ->get();
    }

    // Return the 'carCharging.dashboard' view with user, car charging, and location data
    return view('carCharging.dashboard', [
      'user' => $user,
      'carChargings' => $carChargings,
      'recentCarChargings' => $recentCarChargings,
      'activeLocation' => $activeLocation,
    ]);
  }

  // Display the specified car charging record.
  public function show(string $id)
  {
    // Find the car charging record with the specified ID
    $carCharging = CarCharging::find($id);
    // Return the 'carCharging.show' view with the car charging record data
    return view('carCharging.show', [
      'carCharging' => $carCharging,
    ]);
  }
}
