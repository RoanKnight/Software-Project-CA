<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SolarPanelController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return view('welcome');
});

Route::get('/example', function () {
  return view('example');
});

Route::get('/current-date-time', [SolarPanelController::class, 'getCurrentDateTime']);

Route::get('/update-solar-data', [SolarPanelController::class, 'updateSolarData']);

Route::get('/dashboard', function () {
  return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('users')->group(function () {
  Route::get('/', [RegisteredUserController::class, 'index'])->name('users.index');
  Route::get('/{user}', [RegisteredUserController::class, 'show'])->name('users.show');
  Route::post('/{user}/promote', [RegisteredUserController::class, 'promoteToAdmin'])->name('users.promote');
  Route::delete('/{user}', [RegisteredUserController::class, 'destroy'])->name('users.destroy');
  Route::match(['post', 'patch'], '/{user}/restore', [RegisteredUserController::class, 'restore'])
    ->name('users.restore');
});

Route::prefix('locations')->group(function () {
  Route::get('/', [LocationController::class, 'index'])->name('locations.index');
  Route::get('/create', [LocationController::class, 'create'])->name('locations.create');
  Route::post('/', [LocationController::class, 'store'])->name('locations.store');
  Route::get('/{location}', [LocationController::class, 'show'])->name('locations.show');
  Route::delete('/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
  Route::patch('/{MPRN}/restore', [LocationController::class, 'restore'])->name('locations.restore');
});

require __DIR__ . '/auth.php';