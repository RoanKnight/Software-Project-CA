<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChargingStation;
use App\Services\OpenChargeMapService;
use Illuminate\Support\Facades\Storage;
use Auth;

class ChargingStationController extends Controller
{
  protected $openChargeMapService;

  public function __construct(OpenChargeMapService $openChargeMapService)
  {
    $this->middleware('auth', ['except' => []]);
    $this->middleware('role:admin', ['only' => ['create', 'store', 'delete', 'restore', 'index']]);
    $this->openChargeMapService = $openChargeMapService;
  }

  public function index()
  {
    $chargingStations = ChargingStation::all();
    return view('chargingStations.index', [
      'chargingStations' => $chargingStations,
    ]);
  }

  public function create()
  {
    return view('chargingStations.create');
  }

  public function store(Request $request)
  {
    $rules = [
      'address' => 'required|string|max:255',
      'charging_efficiency' => 'required|string|max:255'
    ];

    $messages = [
      'address.required' => 'Address is required',
      'address.string' => 'Address must be a string',
      'address.max' => 'Address cannot be longer than 255 characters',
      'charging_efficiency.required' => 'Charging efficiency is required',
      'charging_efficiency.string' => 'Charging efficiency must be a string',
      'charging_efficiency.max' => 'Charging efficiency cannot be longer than 255 characters'
    ];

    $request->validate($rules, $messages);

    $chargingStation = new ChargingStation;
    $chargingStation->address = $request->address;
    $chargingStation->charging_efficiency = $request->charging_efficiency;
    $chargingStation->save();

    return redirect()->route('chargingStations.index')->with('status', 'Created a new charging station');
  }

  public function dashboard()
  {
    $stations = $this->openChargeMapService->getStations(53.3498, -6.2603, 10);

    $chargingStation = ChargingStation::all();

    return view('chargingStations.dashboard', [
      'chargingStation' => $chargingStation,
      'stations' => $stations
    ]);
  }

  public function show(string $id)
  {
    $chargingStation = ChargingStation::findOrFail($id);

    return view('chargingStations.show', [
      'chargingStation' => $chargingStation
    ]);
  }

  public function destroy(string $id)
  {
    $chargingStation = ChargingStation::findOrFail($id);

    $chargingStation->update(['deleted' => true]);

    return redirect()->route('chargingStations.index')->with('status', 'Charging station deleted successfully');
  }

  public function restore(string $id)
  {
    $chargingStation = ChargingStation::findOrFail($id);

    $chargingStation->update(['deleted' => false]);

    return redirect()->route('chargingStations.index')->with('status', 'Charging station restored successfully');
  }
}