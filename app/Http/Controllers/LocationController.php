<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use Illuminate\Support\Facades\Storage;
use Auth;

class LocationController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth', ['except' => []]);
    $this->middleware('role:admin', ['only' => ['delete', 'restore', 'index']]);
  }

  public function index()
  {
    $users = User::all();
    $locations = Location::all();
    return view('locations.index', [
      'locations' => $locations,
      'users' => $users
    ]);
  }

  public function userLocations()
  {
    $userLocations = auth()->user()->locations;

    if (!session('active_location_MPRN') && $userLocations->isNotEmpty()) {
      session(['active_location_MPRN' => $userLocations->first()->MPRN]);
    }

    return view('locations.user-locations', [
      'user_locations' => $userLocations
    ]);
  }

  public function create()
  {
    return view('locations.create');
  }

  public function store(Request $request)
  {
    $request->merge(['EirCode' => str_replace(' ', '', $request->EirCode)]);

    $rules = [
      'MPRN' => 'required|digits:11|unique:locations,MPRN',
      'address' => 'required|string|max:255',
      'EirCode' => [
        'required',
        'string',
        'size:7',
        'regex:/^[A-Z0-9]+$/',
        function ($attribute, $value, $fail) {
          if (Location::where('EirCode', $value)->where('user_id', Auth::id())->exists()) {
            $fail('You cannot create another location with the same EirCode.');
          }
        },
      ],
    ];

    $messages = [
      'MPRN.required' => 'The MPRN field is required.',
      'MPRN.digits' => 'The MPRN field must be exactly 11 digits.',
      'MPRN.unique' => 'The MPRN field must be unique.',
      'address.required' => 'The address field is required.',
      'address.max' => 'The address field may not be greater than 255 characters.',
      'EirCode.required' => 'The EirCode field is required.',
      'EirCode.size' => 'The EirCode field must be exactly 7 characters.',
      'EirCode.regex' => 'The EirCode field must be in the correct format. eg: D02AB12',
    ];

    $request->validate($rules, $messages);

    $location = new Location;
    $location->MPRN = $request->MPRN;
    $location->address = $request->address;
    $location->EirCode = $request->EirCode;
    $location->user_id = Auth::id();
    $location->save();

    $userDirectory = 'users/' . Auth::user()->email;
    $locationDirectory = $userDirectory . '/' . str_replace(' ', '_', $location->address);
    if (!Storage::exists($locationDirectory)) {
      Storage::makeDirectory($locationDirectory);
    }

    if (!session('active_location_MPRN')) {
      session(['active_location_MPRN' => $location->MPRN]);
    }

    $activeLocation = Location::where('MPRN', session('active_location_MPRN'))->first();
    if ($activeLocation && $activeLocation->deleted) {
        session(['active_location_MPRN' => $location->MPRN]);
    }

    if (Location::where('user_id', Auth::id())->count() == 1) {
      session(['active_location_MPRN' => $location->MPRN]);
    }

    return redirect()->route('locations.index')->with('status', 'Created a new location');
  }

  public function setActiveLocation($MPRN)
  {
    $user = auth()->user();
    $location = Location::where('MPRN', $MPRN)->first();

    if ($location && $user->locations->contains($location)) {
      session(['active_location_MPRN' => $MPRN]);
    }

    return back();
  }

  public function show(string $id)
  {
    $location = Location::findOrFail($id);

    return view('locations.show', [
      'location' => $location
    ]);
  }

  public function destroy(string $id)
  {
    $location = Location::findOrFail($id);

    $location->update(['deleted' => true]);

    return redirect()->route('locations.index')->with('status', 'Location deleted successfully');
  }

  public function restore(string $id)
  {
    $location = Location::findOrFail($id);

    $location->update(['deleted' => false]);

    return redirect()->route('locations.index')->with('status', 'Location restored successfully');
  }
}