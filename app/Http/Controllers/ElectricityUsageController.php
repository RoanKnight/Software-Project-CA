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
    $this->middleware('auth', ['except' => []]);
    $this->middleware('role:admin', ['only' => ['delete', 'restore', 'index']]);
  }

  public function index()
  {
    $electricityUsages = ElectricityUsage::all();
    $locations = Location::all();
    return view('electricity.index', [
      'electricityUsages' => $electricityUsages,
      'locations' => $locations
    ]);
  }

  public function create()
  {
    $locations = Location::all();
    return view('electricity.create', [
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

    $electricityUsage = new ElectricityUsage;
    $electricityUsage->location_MPRN = $request->location_id;
    $electricityUsage->save();

    $location = Location::where('MPRN', $request->location_id)->first();
    $address = str_replace(' ', '_', $location->address);
    $locationDirectory = 'users/' . Auth::user()->email . '/' . $address;

    if (!Storage::exists($locationDirectory)) {
      return redirect()->back()->with('error', 'Location directory does not exist');
    }

    $electricityJson = $locationDirectory . '/electricity.json';
    Storage::put($electricityJson, json_encode([]));

    return redirect()->route('electricity.index')->with('status', 'Created a new electricity usage');
  }

  public function updateElectricityData()
  {
    $users = User::all();

    foreach ($users as $user) {
      $locations = Location::where('user_id', $user->id)->get();

      foreach ($locations as $location) {
        $address = str_replace(' ', '_', $location->address);
        $path = 'users/' . $user->email . '/' . $address . '/electricity.json';
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
            'times' => []
          ];
          $data[] = &$dateEntry;
          Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));
        } else {
          $lastTimeEntry = end($dateEntry['times']);
          if ($lastTimeEntry !== false && $lastTimeEntry['time'] === '23:55') {
            continue;
          }

          $nextTime = $lastTimeEntry && isset($lastTimeEntry['time']) ? ((int) substr($lastTimeEntry['time'], 0, 2)) : -1;
          $nextMinute = $lastTimeEntry && isset($lastTimeEntry['time']) ? ((int) substr($lastTimeEntry['time'], 3, 2) + 5) % 60 : 0;
          if ($nextMinute === 0 && $lastTimeEntry !== null) {
            $nextTime = ($nextTime + 1) % 24;
          }

          $dateEntry['times'][] = [
            'time' => sprintf('%02d:%02d', $nextTime, $nextMinute),
            'energyUsage_kwh' => round(rand(26, 33) / 24 / 12, 3),
          ];

          Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));
        }
      }
    }
  }

  public function getElectricityData()
  {
    // Fetch the active location MPRN from the User model
    $activeLocationMPRN = auth()->user()->active_MPRN;

    // Find the location with the active MPRN
    $location = Location::where('MPRN', $activeLocationMPRN)->first();

    if (!$location) {
      return response()->json(['error' => 'No active location found.'], 404);
    }

    $address = str_replace(' ', '_', $location->address);
    $filePath = 'users/' . $location->user->email . '/' . $address . '/electricity.json';

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

      return response()->json($electricityConsumptionValues);
    } else {
      return response()->json(['error' => 'File does not exist.'], 404);
    }
  }

  public function dashboard()
  {
    $user = auth()->user();
    $locations = Location::where('user_id', auth()->id())->get();
    $electricityUsage = ElectricityUsage::whereIn('location_MPRN', $locations->pluck('MPRN'))->get();

    return view('electricity.dashboard', [
      'user' => $user,
      'electricityUsage' => $electricityUsage,
      'locations' => $locations,
    ]);
  }

  public function show(string $id)
  {
    $electricityUsage = ElectricityUsage::findOrFail($id);

    return view('electricity.show', [
      'electricityUsage' => $electricityUsage
    ]);
  }

  public function destroy(string $id)
  {
    $electricityUsage = ElectricityUsage::findOrFail($id);

    $electricityUsage->update(['deleted' => true]);

    return redirect()->route('electricity.index')->with('status', 'Electricity usage deleted successfully');
  }

  public function restore(string $id)
  {
    $electricityUsage = ElectricityUsage::findOrFail($id);

    $electricityUsage->update(['deleted' => false]);

    return redirect()->route('electricity.index')->with('status', 'Electricity usage restored successfully');
  }
}
