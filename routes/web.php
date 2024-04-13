<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SolarPanelController;
use App\Http\Controllers\ElectricityUsageController;
use App\Http\Controllers\ChargingStationController;
use App\Http\Controllers\CarChargingController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return view('welcome');
});

Route::get('/example', function () {
  return view('example');
});

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
  Route::get('/user-locations', [LocationController::class, 'userLocations'])->name('locations.userLocations');
  Route::get('/create', [LocationController::class, 'create'])->name('locations.create');
  Route::post('/', [LocationController::class, 'store'])->name('locations.store');
  Route::post('/{MPRN}/setActiveLocation', [LocationController::class, 'setActiveLocation'])->name('setActiveLocation');
  Route::get('/{location}', [LocationController::class, 'show'])->name('locations.show');
  Route::delete('/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
  Route::patch('/{MPRN}/restore', [LocationController::class, 'restore'])->name('locations.restore');
});

Route::prefix('solar')->group(function () {
  Route::get('/', [SolarPanelController::class, 'index'])->name('solar.index');
  Route::get('/create', [SolarPanelController::class, 'create'])->name('solar.create');
  Route::post('/', [SolarPanelController::class, 'store'])->name('solar.store');
  Route::get('/update-solar-data', [SolarPanelController::class, 'updateSolarData'])->name('solar.updateSolarData');
  Route::get('/get-solar-data', [SolarPanelController::class, 'getSolarData'])->name('solar.getSolarData');
  Route::get('/dashboard', [SolarPanelController::class, 'dashboard'])->name('solar.dashboard');
  Route::get('/{solarPanel}', [SolarPanelController::class, 'show'])->name('solar.show');
  Route::delete('/{solarPanel}', [SolarPanelController::class, 'destroy'])->name('solar.destroy');
  Route::patch('/{solarPanel}/restore', [SolarPanelController::class, 'restore'])->name('solar.restore');
});

Route::prefix('electricity')->group(function () {
  Route::get('/', [ElectricityUsageController::class, 'index'])->name('electricity.index');
  Route::get('/create', [ElectricityUsageController::class, 'create'])->name('electricity.create');
  Route::post('/', [ElectricityUsageController::class, 'store'])->name('electricity.store');
  Route::get('/update-electricity-data', [ElectricityUsageController::class, 'updateElectricityData'])->name('electricity.updateElectricityData');
  Route::get('/get-electricity-data', [ElectricityUsageController::class, 'getElectricityData'])->name('electricity.getElectricityData');
  Route::get('/dashboard', [ElectricityUsageController::class, 'dashboard'])->name('electricity.dashboard');
  Route::get('/{electricityUsage}', [ElectricityUsageController::class, 'show'])->name('electricity.show');
  Route::delete('/{electricityUsage}', [ElectricityUsageController::class, 'destroy'])->name('electricity.destroy');
  Route::patch('/{electricityUsage}/restore', [ElectricityUsageController::class, 'restore'])->name('electricity.restore');
});

Route::prefix('chargingStations')->group(function () {
  Route::get('/', [ChargingStationController::class, 'index'])->name('chargingStations.index');
  Route::get('/create', [ChargingStationController::class, 'create'])->name('chargingStations.create');
  Route::post('/', [ChargingStationController::class, 'store'])->name('chargingStations.store');
  Route::get('/dashboard', [ChargingStationController::class, 'dashboard'])->name('chargingStations.dashboard');
  Route::get('/{chargingStation}', [ChargingStationController::class, 'show'])->name('chargingStations.show');
  Route::delete('/{chargingStation}', [ChargingStationController::class, 'destroy'])->name('chargingStations.destroy');
  Route::patch('/{chargingStation}/restore', [ChargingStationController::class, 'restore'])->name('chargingStations.restore');
});

Route::prefix('carCharging')->group(function () {
  Route::get('/', [CarChargingController::class, 'index'])->name('carCharging.index');
  Route::get('/location-car-charging', [CarChargingController::class, 'LocationsCarChargings'])->name('carCharging.locationCarCharging');
  Route::post('/', [CarChargingController::class, 'store'])->name('carCharging.store');
  Route::get('/dashboard', [CarChargingController::class, 'dashboard'])->name('carCharging.dashboard');
  Route::get('/{id}', [CarChargingController::class, 'show'])->name('carCharging.show');
});

require __DIR__ . '/auth.php';