<!-- dashboardlinks.blade.php -->

@php
  // Fetching the authenticated user
  $user = auth()->user();

  // Checking if the user has solar panels, electricity usage, or car charging associated with their active location
  $hasSolar = $user->activeLocation && $user->activeLocation->solarPanels->isNotEmpty();
  $hasElectricityUsage = $user->activeLocation && $user->activeLocation->electricityUsages->isNotEmpty();
  $hasCarCharging = $user->activeLocation && $user->activeLocation->carChargings->isNotEmpty();

  // Initializing an array to hold dashboard links
  $dashboardLinks = [
      [
          'route' => 'carCharging.chargingStations',
          'title' => 'Charging Locations',
      ],
  ];

  // Adding the EV Charging link if the user has car charging data
  if ($hasCarCharging) {
      array_unshift($dashboardLinks, [
          'route' => 'carCharging.dashboard',
          'title' => 'EV Charging',
      ]);
  }

  // Adding the Electricity Usage link if the user has electricity usage data
  if ($hasElectricityUsage) {
      array_unshift($dashboardLinks, [
          'route' => 'electricity.dashboard',
          'title' => 'Electricity usage',
      ]);
  }

  // Adding the Solar Dashboard link if the user has solar panels
  if ($hasSolar) {
      array_unshift($dashboardLinks, [
          'route' => 'solar.dashboard',
          'title' => 'Solar Dashboard',
      ]);
  }
@endphp

@foreach ($dashboardLinks as $link)
  <!-- Iterating over each dashboard link -->
  <li
    class="mt-8 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition  hover:border-l-blue-700 hover:text-blue-700 lg:text-lg {{ Route::currentRouteName() == $link['route'] ? 'border-l-blue-700 text-blue-700' : '' }}">
    <!-- Rendering the link and applying styles based on current route -->
    <a href="{{ route($link['route']) }}">{{ $link['title'] }}</a>
  </li>
@endforeach
