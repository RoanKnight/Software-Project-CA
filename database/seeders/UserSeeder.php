<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $admin = new User;
      $admin->name = "Roan Knight";
      $admin->email = "roanknight21@gmail.com";
      $admin->password = Hash::make("Roanknight123");
      $admin->role = 'admin';
      $admin->save();
      
      $user = new User;
      $user->name = "John Jones";
      $user->email = "user@example.com";
      $user->password = Hash::make("Itsurboii69");
      $user->role = 'user';
      $user->save();
    }
}