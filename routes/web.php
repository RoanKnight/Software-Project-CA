<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SolarPanelController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
  return view('welcome');
});

Route::get('/example', function () {
  return view('example');
});

Route::get('/users', [RegisteredUserController::class, 'index'])->name('users.index');

Route::get('/users/{user}', [RegisteredUserController::class, 'show'])->name('users.show');

Route::get('/current-date-time', [SolarPanelController::class, 'getCurrentDateTime']);

Route::get('/update-solar-data', [SolarPanelController::class, 'updateSolarData']);

Route::post('/users/{user}/promote', [RegisteredUserController::class, 'promoteToAdmin'])->name('users.promote');

Route::delete('/users/{user}', [RegisteredUserController::class, 'destroy'])->name('users.destroy');

Route::match(['post', 'patch'], '/users/{user}/restore', [RegisteredUserController::class, 'restore'])
  ->name('users.restore');

Route::get('/dashboard', function () {
  return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
