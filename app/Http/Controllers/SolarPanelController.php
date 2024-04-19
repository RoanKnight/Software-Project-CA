<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolarPanel;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Services\WeatherService;
use Auth;

class SolarPanelController extends Controller
{
  protected $weatherService;

  public function __construct(WeatherService $weatherService)
  {
    // Middleware to ensure authentication is required for all methods except 'index'
    $this->middleware('auth', ['except' => ['index']]);
    // Middleware to ensure only admin users can access 'delete', 'restore', and 'index' methods
    $this->middleware('role:admin', ['only' => ['delete', 'restore', 'index']]);
    $this->weatherService = $weatherService;
  }

  // Display a listing of the resource.
  public function index()
  {
    // Retrieve all solar panels and locations
    $solarPanels = SolarPanel::all();
    $locations = Location::all();
    // Return the 'solar.index' view with solar panel and location data
    return view('solar.index', [
      'solarPanels' => $solarPanels,
      'locations' => $locations,
    ]);
  }

  // Show the form for creating a new resource.
  public function create()
  {
    // Retrieve locations associated with the authenticated user
    $locations = Location::where('user_id', auth()->id())->get();
    // Return the 'solar.create' view with location data
    return view('solar.create', [
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
      'location_id.required' => 'The location field is required.',
    ];

    // Validate the incoming request data
    $request->validate($rules, $messages);

    // Create a new solar panel instance
    $solarPanel = new SolarPanel();
    $solarPanel->location_MPRN = $request->location_id;
    $solarPanel->save();

    // Retrieve location details based on the provided location ID
    $location = Location::where('MPRN', $request->location_id)->first();
    // Generate the address for the location directory
    $address = str_replace(' ', '_', $location->address);
    $locationDirectory = 'users/' . Auth::user()->email . '/' . $address;

    // Check if the location directory exists in storage
    if (!Storage::exists($locationDirectory)) {
      return redirect()->back()->with('error', 'Location directory does not exist');
    }

    // Define the path for the solar JSON file
    $solarJsonPath = $locationDirectory . '/solar.json';
    // Create an empty JSON file for solar panel data storage
    Storage::put($solarJsonPath, json_encode([]));

    // Redirect to the 'solar.index' route with a success message
    return redirect()->route('solar.index')->with('status', 'Created a new solar panel');
  }

  // Update solar panel data for all users and locations.
  public function updateSolarData()
  {
    // Retrieve all users
    $users = User::all();
    // Define the location for weather data retrieval
    $location = 'Dún Laoghaire, IE';

    // Fetch sunrise and sunset times
    $weather = $this->weatherService->getCurrentWeather($location);
    $sunriseHour = date('H', $weather['sys']['sunrise']);
    $sunsetHour = date('H', $weather['sys']['sunset']);

    // Iterate through each user
    foreach ($users as $user) {
      // Retrieve locations associated with the current user
      $locations = Location::where('user_id', $user->id)->get();

      // Iterate through each location
      foreach ($locations as $location) {
        // Generate the path for the solar JSON file
        $address = str_replace(' ', '_', $location->address);
        $path = 'users/' . $user->email . '/' . $address . '/solar.json';

        // Check if the JSON file exists in storage
        if (!Storage::exists($path)) {
          continue;
        }

        // Retrieve existing solar panel data from the JSON file
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
            'hours' => [],
          ];
          $data[] = &$dateEntry;
          Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));
          continue;
        }

        // Check if the last hour entry is for 23:00
        $lastHourEntry = end($dateEntry['hours']);
        if ($lastHourEntry !== false && $lastHourEntry['hour'] === '23:00') {
          continue;
        }

        // Calculate energy generation based on sunrise and sunset hours
        $nextHour = $lastHourEntry && isset($lastHourEntry['hour']) ? ((int) substr($lastHourEntry['hour'], 0, 2) + 1) % 24 : 0;
        $energyGeneration = $nextHour >= $sunriseHour && $nextHour < $sunsetHour ? rand(10, 20) / 10.0 : 0;

        // Add energy generation data for the next hour
        $dateEntry['hours'][] = [
          'hour' => sprintf('%02d:00', $nextHour),
          'energyGeneration_kwh' => $energyGeneration,
        ];

        // Save updated data to the JSON file
        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));
      }
    }
  }

  // Retrieve solar panel data for the active user's location.
  public function getSolarData()
  {
    // Fetch the active location MPRN from the User model
    $activeLocationMPRN = auth()->user()->active_MPRN;

    // Find the location with the active MPRN
    $location = Location::where('MPRN', $activeLocationMPRN)->first();

    // If no location found, return a 404 error response
    if (!$location) {
      return response()->json(['error' => 'No active location found.'], 404);
    }

    // Generate the file path for the solar JSON data
    $address = str_replace(' ', '_', $location->address);
    $filePath = 'users/' . $location->user->email . '/' . $address . '/solar.json';

    // If the JSON file exists, retrieve and format the data
    if (Storage::exists($filePath)) {
      $data = Storage::get($filePath);

      $jsonData = json_decode($data, true);

      $energyGenerationValues = [];

      foreach ($jsonData as $dateData) {
        $dateEnergyGenerationValues = array_map(function ($hour) {
          return [
            'hour' => $hour['hour'],
            'energyGeneration_kwh' => $hour['energyGeneration_kwh'],
          ];
        }, $dateData['hours']);

        $energyGenerationValues[] = [
          'date' => $dateData['date'],
          'hours' => $dateEnergyGenerationValues,
        ];
      }

      // Return the formatted energy generation data
      return response()->json($energyGenerationValues);
    } else {
      // If the file does not exist, return a 404 error response
      return response()->json(['error' => 'File does not exist.'], 404);
    }
  }

  // Display the dashboard with solar panel and weather data.
  public function dashboard()
  {
    // Retrieve the authenticated user
    $user = auth()->user();
    // Retrieve locations associated with the authenticated user
    $locations = Location::where('user_id', auth()->id())->get();
    // Retrieve solar panels based on the associated location MPRNs
    $solarPanels = SolarPanel::whereIn('location_MPRN', $locations->pluck('MPRN'))->get();

    // Define the location for weather data retrieval
    $location = 'Dún Laoghaire, IE';
    // Retrieve current weather and forecast data for the location
    $weather = $this->weatherService->getCurrentWeather($location);
    $forecast = $this->weatherService->getForecast($location);

    // Format forecast data by day
    $forecastByDay = [];
    $today = date('Y-m-d');
    foreach ($forecast['list'] as $item) {
      $date = date('Y-m-d', $item['dt']);
      if ($date > $today) {
        if (!isset($forecastByDay[$date])) {
          $forecastByDay[$date] = [
            'temp' => round($item['main']['temp'] - 273.15),
            'icon' => $item['weather'][0]['icon'],
          ];
        }
      }
    }

    // Return the 'solar.dashboard' view with user, solar panel, location, weather, and forecast data
    return view('solar.dashboard', [
      'user' => $user,
      'solarPanels' => $solarPanels,
      'locations' => $locations,
      'weather' => $weather,
      'forecastByDay' => $forecastByDay
    ]);
  }

  // Display the specified resource.
  public function show(string $id)
  {
    // Find the solar panel with the specified ID
    $solarPanel = SolarPanel::findOrFail($id);

    // Return the 'solar.show' view with the solar panel data
    return view('solar.show', [
      'solarPanel' => $solarPanel,
    ]);
  }

  // Remove the specified resource from storage.
  public function destroy(string $id)
  {
    // Find the solar panel with the specified ID
    $solarPanel = SolarPanel::findOrFail($id);

    // Soft delete the solar panel
    $solarPanel->update(['deleted' => true]);

    // Redirect to the 'solar.index' route with a success message
    return redirect()->route('solar.index')->with('status', 'Solar panel deleted successfully');
  }

  // Restore the specified resource.
  public function restore(string $id)
  {
    // Find the solar panel with the specified ID
    $solarPanel = SolarPanel::findOrFail($id);

    // Restore the soft deleted solar panel
    $solarPanel->update(['deleted' => false]);

    // Redirect to the 'solar.index' route with a success message
    return redirect()->route('solar.index')->with('status', 'Solar panel restored successfully');
  }
}
