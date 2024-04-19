<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
  /**
   * Display the user's profile form.
   */
  public function edit(Request $request): View
  {
    // Retrieve locations associated with the authenticated user
    $locations = auth()->user()->locations;

    // Return the 'profile.edit' view with user and location data
    return view('profile.edit', [
      'user' => $request->user(),
      'locations' => $locations,
    ]);
  }

  /**
   * Update the user's profile information.
   */
  public function update(ProfileUpdateRequest $request): RedirectResponse
  {
    // Get the current user
    $user = $request->user();

    // Update user's profile with validated data
    $user->fill($request->validated());

    // If email has been updated
    if ($user->isDirty('email')) {
      // Define the old and new directory paths
      $oldDirectory = 'users/' . $user->getOriginal('email');
      $newDirectory = 'users/' . $user->email;

      // If the old directory exists, rename it
      if (Storage::exists($oldDirectory)) {
        Storage::move($oldDirectory, $newDirectory);
      }

      // Reset email verification
      $user->email_verified_at = null;
    }

    // Save the updated user profile
    $user->save();

    // Redirect to the profile edit page with a success message
    return Redirect::route('profile.edit')->with('status', 'profile-updated');
  }

  /**
   * Delete the user's account.
   */
  public function destroy(Request $request): RedirectResponse
  {
    // Validate the password for user deletion
    $request->validateWithBag('userDeletion', [
      'password' => ['required', 'current_password'],
    ]);

    // Retrieve the authenticated user
    $user = $request->user();

    // Define the user's directory
    $userDirectory = 'users/' . $user->email;

    // Logout the user
    Auth::logout();

    // Delete the user account
    $user->delete();

    // Delete the user's directory
    if (Storage::exists($userDirectory)) {
      Storage::deleteDirectory($userDirectory);
    }

    // Invalidate the session and regenerate the CSRF token
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Redirect to the homepage after account deletion
    return Redirect::to('/');
  }
}
