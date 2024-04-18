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

  public function __construct()
  {
    $this->middleware('auth', ['except' => ['create', 'store']]);
    $this->middleware('role:admin', ['only' => ['promote', 'delete', 'restore', 'index']]);
  }

  public function index()
  {
    $users = User::all();

    return view('users.index', [
      'users' => $users,
    ]);
  }

  public function create(): View
  {
    return view('auth.register');
  }

  /**
   * Handle an incoming registration request.
   *
   * @throws \Illuminate\Validation\ValidationException
   */
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

    // Use the user's email for the directory
    $userDirectory = 'users/' . $user->email;
    Storage::makeDirectory($userDirectory);

    event(new Registered($user));

    Auth::login($user);

    return redirect(RouteServiceProvider::REGISTEREDHOME);
  }

  public function show(User $user): View
  {
    return view('users.show', [
      'user' => $user,
    ]);
  }

  public function promoteToAdmin(User $user): RedirectResponse
  {
    $user->role = User::ROLE_ADMIN;
    $user->save();

    return redirect()->back()->with('success', 'User promoted to admin successfully');
  }

  public function destroy(string $id)
  {
    $user = User::findOrFail($id);
    $user->update(['deleted' => true]);

    return redirect()->route('users.index')->with('status', 'User deleted successfully');
  }

  public function restore(string $id)
  {
    $user = User::findOrFail($id);

    $user->update(['deleted' => false]);

    return redirect()->route('users.index')->with('status', 'User restored successfully');
  }
}
