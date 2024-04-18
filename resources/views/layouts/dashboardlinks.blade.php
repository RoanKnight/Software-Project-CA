<!-- dashboardlinks.blade.php -->
@php
  $user = auth()->user();
  $hasSolar = $user->activeLocation && $user->activeLocation->solarPanels->isNotEmpty();
  $hasElectricityUsage = $user->activeLocation && $user->activeLocation->electricityUsages->isNotEmpty();
  $hasCarCharging = $user->activeLocation && $user->activeLocation->carChargings->isNotEmpty();

  $dashboardLinks = [
      [
          'route' => 'chargingStations.dashboard',
          'title' => 'Charging Locations',
      ],
  ];

  if ($hasCarCharging) {
      array_unshift($dashboardLinks, [
          'route' => 'carCharging.dashboard',
          'title' => 'EV Charging',
      ]);
  }

  if ($hasElectricityUsage) {
      array_unshift($dashboardLinks, [
          'route' => 'electricity.dashboard',
          'title' => 'Electricity usage',
      ]);
  }

  if ($hasSolar) {
      array_unshift($dashboardLinks, [
          'route' => 'solar.dashboard',
          'title' => 'Solar Dashboard',
      ]);
  }
@endphp

@foreach ($dashboardLinks as $link)

  <li
    class="mt-8 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition  hover:border-l-blue-700 hover:text-blue-700 lg:text-lg {{ Route::currentRouteName() == $link['route'] ? 'border-l-blue-700 text-blue-700' : '' }}">
    <a href="{{ route($link['route']) }}">{{ $link['title'] }}</a>
  </li>
@endforeach
