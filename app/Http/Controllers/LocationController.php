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
    $this->middleware('role:admin', ['only' => ['restore', 'index']]);
  }

  public function index()
  {
    $users = User::all();
    $locations = Location::all();
    return view('locations.index', [
      'locations' => $locations,
      'users' => $users,
    ]);
  }

  public function userLocations()
  {
    $userLocations = auth()->user()->locations;

    if (!auth()->user()->active_MPRN && $userLocations->isNotEmpty()) {
      auth()->user()->active_MPRN = $userLocations->first()->MPRN;
      auth()->user()->save();
    }

    return view('locations.user-locations', [
      'user_locations' => $userLocations,
    ]);
  }

  public function create()
  {
    $locations = auth()->user()->locations;

    return view('locations.create', ['locations' => $locations]);
  }

  public function store(Request $request)
  {
    $user = auth()->user();
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

    $location = new Location();
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

    if (!$user->active_MPRN) {
      $user->active_MPRN = $location->MPRN;
      $user->save();
    }

    $activeLocation = Location::where('MPRN', $user->active_MPRN)->first();
    if ($activeLocation && $activeLocation->deleted) {
      $user->active_MPRN = $location->MPRN;
      $user->save();
    }

    if (Location::where('user_id', Auth::id())->count() == 1) {
      $user->active_MPRN = $location->MPRN;
      $user->save();
    }

    return redirect()->route('profile.edit')->with('status', 'Created a new location');
  }

  public function setActiveLocation($MPRN)
  {
    $user = auth()->user();
    $location = Location::where('MPRN', $MPRN)->first();

    if ($location && $user->locations->contains($location)) {
      $user->active_MPRN = $MPRN;
      $user->save();
    }

    return back();
  }

  public function show(string $id)
  {
    $location = Location::findOrFail($id);

    return view('locations.show', [
      'location' => $location,
    ]);
  }

  public function destroy(string $id)
  {
    $location = Location::findOrFail($id);

    $location->update(['deleted' => true]);

    return redirect()->back()->with('status', 'Location deleted successfully');
  }

  public function restore(string $id)
  {
    $location = Location::findOrFail($id);

    $location->update(['deleted' => false]);

    return redirect()->route('locations.index')->with('status', 'Location restored successfully');
  }
}
