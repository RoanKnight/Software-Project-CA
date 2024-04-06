<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolarPanel;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Auth;

class SolarPanelController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth', ['except' => []]);
    $this->middleware('role:admin', ['only' => ['delete', 'restore', 'index']]);
  }

  public function index()
  {
    $solarPanels = SolarPanel::all();
    $locations = Location::all();
    return view('solar.index', [
      'solarPanels' => $solarPanels,
      'locations' => $locations
    ]);
  }

  public function create()
  {
    $locations = Location::where('user_id', auth()->id())->get();
    return view('solar.create', [
      'locations' => $locations,
    ]);
  }

  public function store(Request $request)
  {
    $rules = [
      'location_id' => 'required|exists:locations,MPRN',
    ];

    $messages = [
      'location_id.required' => 'The location field is required.'
    ];

    $request->validate($rules, $messages);

    $solarPanel = new SolarPanel;
    $solarPanel->location_MPRN = $request->location_id;
    $solarPanel->save();

    $location = Location::where('MPRN', $request->location_id)->first();
    $address = str_replace(' ', '_', $location->address);
    $locationDirectory = 'users/' . Auth::user()->email . '/' . $address;

    if (!Storage::exists($locationDirectory)) {
      return redirect()->back()->with('error', 'Location directory does not exist');
    }

    $solarJsonPath = $locationDirectory . '/solar.json';
    Storage::put($solarJsonPath, json_encode([]));

    return redirect()->route('solar.index')->with('status', 'Created a new solar panel');
  }

  public function updateSolarData()
  {
    $users = User::all();

    foreach ($users as $user) {
      $locations = Location::where('user_id', $user->id)->get();

      foreach ($locations as $location) {
        $address = str_replace(' ', '_', $location->address);
        $path = 'users/' . $user->email . '/' . $address . '/solar.json';
        if (!Storage::exists($path)) {
          continue;
        }

        $data = json_decode(Storage::get($path), true);
        $currentDate = Carbon::now()->format('d-m-Y');

        $dateEntry = null;
        foreach ($data as &$entry) {
          if ($entry['date'] === $currentDate) {
            $dateEntry = &$entry;
            break;
          }
        }

        if ($dateEntry === null) {
          $dateEntry = [
            'date' => $currentDate,
            'hours' => []
          ];
          $data[] = &$dateEntry;
          Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));
          continue;
        }

        $lastHourEntry = end($dateEntry['hours']);
        if ($lastHourEntry !== false && $lastHourEntry['hour'] === '23:00') {
          continue;
        }

        $nextHour = $lastHourEntry && isset($lastHourEntry['hour']) ? ((int) substr($lastHourEntry['hour'], 0, 2) + 1) % 24 : 0;
        $dateEntry['hours'][] = [
          'hour' => sprintf('%02d:00', $nextHour),
          'energyGeneration_kwh' => rand(10, 20) / 10.0,
        ];

        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));
      }
    }

    return response()->json('Solar data updated for all users and locations');
  }

  public function show(string $id)
  {
    $solarPanel = SolarPanel::findOrFail($id);

    return view('solar.show', [
      'solarPanel' => $solarPanel
    ]);
  }

  public function destroy(string $id)
  {
    $solarPanel = SolarPanel::findOrFail($id);

    $solarPanel->update(['deleted' => true]);

    return redirect()->route('solar.index')->with('status', 'Solar panel deleted successfully');
  }

  public function restore(string $id)
  {
    $solarPanel = SolarPanel::findOrFail($id);

    $solarPanel->update(['deleted' => false]);

    return redirect()->route('solar.index')->with('status', 'Solar panel restored successfully');
  }
}
