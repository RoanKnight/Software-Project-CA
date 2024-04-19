<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use Illuminate\Support\Facades\Storage;
use Auth;

class LocationController extends Controller
{
  // Constructor to set middleware for routes
  public function __construct()
  {
    // Middleware to require authentication for all methods
    $this->middleware('auth', ['except' => []]);
    // Middleware to restrict access to admin-related methods to users with 'admin' role
    $this->middleware('role:admin', ['only' => ['restore', 'index']]);
  }

  // Display a list of all locations
  public function index()
  {
    $users = User::all();
    $locations = Location::all();
    return view('locations.index', [
      'locations' => $locations,
      'users' => $users,
    ]);
  }

  // Display a list of locations belonging to the current authenticated user
  public function userLocations()
  {
    $userLocations = auth()->user()->locations;

    // Set the active MPRN if not set and user has locations
    if (!auth()->user()->active_MPRN && $userLocations->isNotEmpty()) {
      auth()->user()->active_MPRN = $userLocations->first()->MPRN;
      auth()->user()->save();
    }

    return view('locations.user-locations', [
      'user_locations' => $userLocations,
    ]);
  }

  // Show the location creation form
  public function create()
  {
    $locations = auth()->user()->locations;
    return view('locations.create', ['locations' => $locations]);
  }

  // Store a newly created location
  public function store(Request $request)
  {
    $user = auth()->user();
    $request->merge(['EirCode' => str_replace(' ', '', $request->EirCode)]);

    // Validation rules
    $rules = [
      'MPRN' => 'required|digits:11|unique:locations,MPRN',
      'address' => 'required|string|max:255',
      'EirCode' => 'required|string|size:7|regex:/^[A-Z0-9]{4}[ -]?[A-Z0-9]{3}$/',
    ];

    // Validation messages
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

    // Create a new location
    $location = new Location();
    $location->MPRN = $request->MPRN;
    $location->address = $request->address;
    $location->EirCode = $request->EirCode;
    $location->user_id = Auth::id();
    $location->save();

    // Create a directory for the location
    $userDirectory = 'users/' . Auth::user()->email;
    $locationDirectory = $userDirectory . '/' . str_replace(' ', '_', $location->address);
    if (!Storage::exists($locationDirectory)) {
      Storage::makeDirectory($locationDirectory);
    }

    // Set active_MPRN for the user if not set or deleted
    if (!$user->active_MPRN || $user->activeLocation()->exists() && $user->activeLocation->deleted) {
      $user->active_MPRN = $location->MPRN;
      $user->save();
    }

    // If this is the first location for the user, set it as active
    if (Location::where('user_id', Auth::id())->count() == 1) {
      $user->active_MPRN = $location->MPRN;
      $user->save();
    }

    return redirect()->route('profile.edit')->with('status', 'Created a new location');
  }

  public function edit(string $MPRN)
  {
    $location = Location::findOrFail($MPRN);
    return view('locations.edit', [
      'location' => $location,
    ]);
  }

  public function update(Request $request, string $MPRN)
  {
    $location = Location::findOrFail($MPRN);
    $request->merge(['EirCode' => str_replace(' ', '', $request->EirCode)]);

    // Validation rules
    $rules = [
      'address' => 'required|string|max:255',
      'EirCode' => 'required|string|regex:/^[A-Z0-9]{3}[ -]?[A-Z0-9]{4}$/',
    ];

    // Validation messages
    $messages = [
      'address.required' => 'The address field is required.',
      'address.max' => 'The address field may not be greater than 255 characters.',
      'EirCode.required' => 'The EirCode field is required.',
      'EirCode.size' => 'The EirCode field must be exactly 7 characters.',
      'EirCode.regex' => 'The EirCode field must be in the correct format. eg: D02AB12',
    ];

    // Validate the request
    $validatedData = $request->validate($rules, $messages);

    // Update the location
    $location->fill($validatedData);

    // Update the location directory if the address changes
    if ($location->isDirty('address')) {
      $oldDirectory = 'users/' . Auth::user()->email . '/' . str_replace(' ', '_', $location->getOriginal('address'));
      $newDirectory = 'users/' . Auth::user()->email . '/' . str_replace(' ', '_', $request->address);

      // If the old directory exists, rename it to the new directory
      if (Storage::exists($oldDirectory)) {
        Storage::move($oldDirectory, $newDirectory);
      }
    }

    // Save the changes to the location
    $location->save();

    return redirect()->route('profile.edit')->with('status', 'Location updated successfully');
  }

  // Set the active location for the current authenticated user
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

  // Display the specified location
  public function show(string $id)
  {
    $location = Location::findOrFail($id);

    return view('locations.show', [
      'location' => $location,
    ]);
  }

  // Delete a location
  public function destroy(string $id)
  {
    $location = Location::findOrFail($id);
    $location->update(['deleted' => true]);

    return redirect()->back()->with('status', 'Location deleted successfully');
  }

  // Restore a deleted location
  public function restore(string $id)
  {
    $location = Location::findOrFail($id);
    $location->update(['deleted' => false]);

    return redirect()->route('locations.index')->with('status', 'Location restored successfully');
  }
}
