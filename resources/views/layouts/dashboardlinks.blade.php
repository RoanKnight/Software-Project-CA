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
  {{-- <li class="mb-10 px-4 py-3 {{ Route::currentRouteName() == $link['route'] ? 'bg-blue-500 rounded-2xl' : '' }}">
    <a href="{{ route($link['route']) }}" class="flex items-center hover:underline"
      @if (isset($link['onclick'])) onclick="{{ $link['onclick'] }}" @endif>
      <img src="{{ $link['image'] }}" alt="" style="width: 25px; height: 25px">
      <h3 class="text-xl font-normal ml-5">{{ $link['title'] }}</h3>
    </a>
  </li> --}}

  <li
    class="mt-5 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition  hover:border-l-blue-700 hover:text-blue-700 lg:text-xl {{ Route::currentRouteName() == $link['route'] ? 'border-l-blue-700 text-blue-700' : '' }}">
    <a href="{{ route($link['route']) }}">{{ $link['title'] }}</a>
  </li>
@endforeach
