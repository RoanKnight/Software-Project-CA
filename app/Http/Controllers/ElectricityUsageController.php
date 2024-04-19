<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ElectricityUsage;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Auth;

class ElectricityUsageController extends Controller
{
  public function __construct()
  {
    // Middleware to ensure authentication is required for all methods except 'index'
    $this->middleware('auth', ['except' => ['index']]);
    // Middleware to ensure only admin users can access 'delete', 'restore', and 'index' methods
    $this->middleware('role:admin', ['only' => ['delete', 'restore', 'index']]);
  }

  // Display a listing of the resource.
  public function index()
  {
    // Retrieve all electricity usages and locations
    $electricityUsages = ElectricityUsage::all();
    $locations = Location::all();
    // Return the 'electricity.index' view with electricity usage and location data
    return view('electricity.index', [
      'electricityUsages' => $electricityUsages,
      'locations' => $locations
    ]);
  }

  // Show the form for creating a new resource.
  public function create()
  {
    // Retrieve all locations
    $locations = Location::all();
    // Return the 'electricity.create' view with location data
    return view('electricity.create', [
      'locations' => $locations,
    ]);
  }

  // Store a newly created resource in storage.
  public function store(Request $request)
  {
    // Define validation rules for the request
    $rules = [
      'location_id' => 'required|exists:locations,MPRN',
    ];

    // Define custom error messages for validation
    $messages = [
      'location_id.required' => 'The location field is required.'
    ];

    // Validate the incoming request data
    $request->validate($rules, $messages);

    // Create a new electricity usage instance
    $electricityUsage = new ElectricityUsage;
    $electricityUsage->location_MPRN = $request->location_id;
    $electricityUsage->save();

    // Retrieve location details based on the provided location ID
    $location = Location::where('MPRN', $request->location_id)->first();
    // Generate the address for the location directory
    $address = str_replace(' ', '_', $location->address);
    $locationDirectory = 'users/' . Auth::user()->email . '/' . $address;

    // Check if the location directory exists in storage
    if (!Storage::exists($locationDirectory)) {
      return redirect()->back()->with('error', 'Location directory does not exist');
    }

    // Define the path for the electricity JSON file
    $electricityJson = $locationDirectory . '/electricity.json';
    // Create an empty JSON file for electricity usage data storage
    Storage::put($electricityJson, json_encode([]));

    // Redirect to the 'electricity.index' route with a success message
    return redirect()->route('electricity.index')->with('status', 'Created a new electricity usage');
  }

  // Update electricity usage data for all users and locations.
  public function updateElectricityData()
  {
    // Retrieve all users
    $users = User::all();

    // Iterate through each user
    foreach ($users as $user) {
      // Retrieve locations associated with the current user
      $locations = Location::where('user_id', $user->id)->get();

      // Iterate through each location
      foreach ($locations as $location) {
        // Generate the path for the electricity JSON file
        $address = str_replace(' ', '_', $location->address);
        $path = 'users/' . $user->email . '/' . $address . '/electricity.json';

        // Check if the JSON file exists in storage
        if (!Storage::exists($path)) {
          continue;
        }

        // Retrieve existing electricity usage data from the JSON file
        $data = json_decode(Storage::get($path), true);
        $currentDate = Carbon::now()->format('d-m-Y');

        // Check if data entry for the current date exists
        $dateEntry = null;
        foreach ($data as &$entry) {
          if ($entry['date'] === $currentDate) {
            $dateEntry = &$entry;
            break;
          }
        }

        // If no entry exists for the current date, create a new entry
        if ($dateEntry === null) {
          $dateEntry = [
            'date' => $currentDate,
            'times' => []
          ];
          $data[] = &$dateEntry;
          Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));
        } else {
          // Check if the last time entry is for 23:55
          $lastTimeEntry = end($dateEntry['times']);
          if ($lastTimeEntry !== false && $lastTimeEntry['time'] === '23:55') {
            continue;
          }

          // Calculate next time and energy usage
          $nextTime = $lastTimeEntry && isset($lastTimeEntry['time']) ? ((int) substr($lastTimeEntry['time'], 0, 2)) : -1;
          $nextMinute = $lastTimeEntry && isset($lastTimeEntry['time']) ? ((int) substr($lastTimeEntry['time'], 3, 2) + 5) % 60 : 0;
          if ($nextMinute === 0 && $lastTimeEntry !== null) {
            $nextTime = ($nextTime + 1) % 24;
          }

          // Add energy usage data for the next time
          $dateEntry['times'][] = [
            'time' => sprintf('%02d:%02d', $nextTime, $nextMinute),
            'energyUsage_kwh' => round(rand(26, 33) / 24 / 12, 3),
          ];

          // Save updated data to the JSON file
          Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));
        }
      }
    }
  }

  // Retrieve electricity usage data for the active user's location.
  public function getElectricityData()
  {
    // Fetch the active location MPRN from the User model
    $activeLocationMPRN = auth()->user()->active_MPRN;

    // Find the location with the active MPRN
    $location = Location::where('MPRN', $activeLocationMPRN)->first();

    // If no location found, return a 404 error response
    if (!$location) {
      return response()->json(['error' => 'No active location found.'], 404);
    }

    // Generate the file path for the electricity JSON data
    $address = str_replace(' ', '_', $location->address);
    $filePath = 'users/' . $location->user->email . '/' . $address . '/electricity.json';

    // If the JSON file exists, retrieve and format the data
    if (Storage::exists($filePath)) {
      $data = Storage::get($filePath);

      $jsonData = json_decode($data, true);

      $electricityConsumptionValues = [];

      foreach ($jsonData as $dateData) {
        $dateElectricityConsumptionValues = array_map(function ($time) {
          return [
            'time' => $time['time'],
            'energyUsage_kwh' => $time['energyUsage_kwh'],
          ];
        }, $dateData['times']);

        $electricityConsumptionValues[] = [
          'date' => $dateData['date'],
          'times' => $dateElectricityConsumptionValues,
        ];
      }

      // Return the formatted electricity consumption data
      return response()->json($electricityConsumptionValues);
    } else {
      // If the file does not exist, return a 404 error response
      return response()->json(['error' => 'File does not exist.'], 404);
    }
  }

  // Display the dashboard with electricity usage data.
  public function dashboard()
  {
    // Retrieve the authenticated user
    $user = auth()->user();
    // Retrieve locations associated with the authenticated user
    $locations = Location::where('user_id', auth()->id())->get();
    // Retrieve electricity usage based on the associated location MPRNs
    $electricityUsage = ElectricityUsage::whereIn('location_MPRN', $locations->pluck('MPRN'))->get();

    // Return the 'electricity.dashboard' view with user, electricity usage, and location data
    return view('electricity.dashboard', [
      'user' => $user,
      'electricityUsage' => $electricityUsage,
      'locations' => $locations,
    ]);
  }

  // Display the specified resource.
  public function show(string $id)
  {
    // Find the electricity usage with the specified ID
    $electricityUsage = ElectricityUsage::findOrFail($id);

    // Return the 'electricity.show' view with the electricity usage data
    return view('electricity.show', [
      'electricityUsage' => $electricityUsage
    ]);
  }

  // Soft delete the specified resource.
  public function destroy(string $id)
  {
    // Find the electricity usage with the specified ID
    $electricityUsage = ElectricityUsage::findOrFail($id);

    // Soft delete the electricity usage
    $electricityUsage->update(['deleted' => true]);

    // Redirect to the 'electricity.index' route with a success message
    return redirect()->route('electricity.index')->with('status', 'Electricity usage deleted successfully');
  }

  // Restore the specified resource.
  public function restore(string $id)
  {
    // Find the electricity usage with the specified ID
    $electricityUsage = ElectricityUsage::findOrFail($id);

    // Restore the soft deleted electricity usage
    $electricityUsage->update(['deleted' => false]);

    // Redirect to the 'electricity.index' route with a success message
    return redirect()->route('electricity.index')->with('status', 'Electricity usage restored successfully');
  }
}

