<!-- dashboardlinks.blade.php -->
@php
  $user = auth()->user();
  $hasSolar = $user->activeLocation && $user->activeLocation->solarPanels->isNotEmpty();
  $hasElectricityUsage = $user->activeLocation && $user->activeLocation->electricityUsages->isNotEmpty();
  $hasCarCharging = $user->activeLocation && $user->activeLocation->carChargings->isNotEmpty();

  $dashboardLinks = [
      [
          'route' => 'chargingStations.dashboard',
          'image' => '/images/ChargingLocations-dashboard-icon.png',
          'title' => 'Charging Locations',
      ],
  ];

  if ($hasCarCharging) {
      array_unshift($dashboardLinks, [
          'route' => 'carCharging.dashboard',
          'image' => '/images/CarCharging-dashboard-icon.png',
          'title' => 'EV Charging',
      ]);
  }

  if ($hasElectricityUsage) {
      array_unshift($dashboardLinks, [
          'route' => 'electricity.dashboard',
          'image' => '/images/Electricity-dashboard-icon.png',
          'title' => 'Electricity usage',
      ]);
  }

  if ($hasSolar) {
      array_unshift($dashboardLinks, [
          'route' => 'solar.dashboard',
          'image' => '/images/Solar-dashboard-icon.png',
          'title' => 'Solar Dashboard',
      ]);
  }
@endphp

@foreach ($dashboardLinks as $link)
  <li class="mb-10 px-4 py-3 {{ Route::currentRouteName() == $link['route'] ? 'bg-blue-500 rounded-2xl' : '' }}">
    <a href="{{ route($link['route']) }}" class="flex items-center hover:underline"
      @if (isset($link['onclick'])) onclick="{{ $link['onclick'] }}" @endif>
      <img src="{{ $link['image'] }}" alt="" style="width: 25px; height: 25px">
      <h3 class="text-xl font-normal ml-5">{{ $link['title'] }}</h3>
    </a>
  </li>
@endforeach