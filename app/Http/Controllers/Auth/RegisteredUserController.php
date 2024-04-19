<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
  // Constructor to set middleware for routes
  public function __construct()
  {
    // Middleware to require authentication for all methods except create and store
    $this->middleware('auth', ['except' => ['create', 'store']]);
    // Middleware to restrict access to admin-related methods to users with 'admin' role
    $this->middleware('role:admin', ['only' => ['promoteToAdmin', 'destroy', 'restore', 'index']]);
  }

  // Display a list of all registered users
  public function index(): View
  {
    $users = User::all();
    return view('users.index', ['users' => $users]);
  }

  // Show the registration form
  public function create(): View
  {
    return view('auth.register');
  }

  // Handle a registration request
  public function store(Request $request): RedirectResponse
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role' => User::ROLE_USER,
    ]);

    // Create a directory for the user using their email address
    $userDirectory = 'users/' . $user->email;
    Storage::makeDirectory($userDirectory);

    event(new Registered($user));

    Auth::login($user);

    // Redirect to the registered user's home page
    return redirect(RouteServiceProvider::REGISTEREDHOME);
  }

  // Display the profile of a specific user
  public function show(User $user): View
  {
    return view('users.show', ['user' => $user]);
  }

  // Promote a user to admin role
  public function promoteToAdmin(User $user): RedirectResponse
  {
    $user->role = User::ROLE_ADMIN;
    $user->save();

    return redirect()->back()->with('success', 'User promoted to admin successfully');
  }

  // Delete a user
  public function destroy(string $id)
  {
    $user = User::findOrFail($id);
    $user->update(['deleted' => true]);

    return redirect()->route('users.index')->with('status', 'User deleted successfully');
  }

  // Restore a deleted user
  public function restore(string $id)
  {
    $user = User::findOrFail($id);
    $user->update(['deleted' => false]);

    return redirect()->route('users.index')->with('status', 'User restored successfully');
  }
}
