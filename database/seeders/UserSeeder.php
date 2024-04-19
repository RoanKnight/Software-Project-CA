<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Create an admin user
    $admin = new User;
    $admin->name = "Admin user";
    $admin->email = "admin@example.com";
    $admin->password = Hash::make("ExamplePassword123");
    $admin->role = 'admin';
    $admin->save();

    // Create a directory for the admin user
    $adminDirectory = 'users/' . $admin->email;
    if (!Storage::exists($adminDirectory)) {
      Storage::makeDirectory($adminDirectory);
    }

    // Create a regular user
    $user = new User;
    $user->name = "John Jones";
    $user->email = "user@example.com";
    $user->password = Hash::make("Itsurboii69");
    $user->role = 'user';
    $user->save();

    // Create a directory for the regular user
    $userDirectory = 'users/' . $user->email;
    if (!Storage::exists($userDirectory)) {
      Storage::makeDirectory($userDirectory);
    }
  }
}
