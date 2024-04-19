<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://d3js.org/d3.v6.js"></script>

  @vite(resource_path('css/app.css'))
  @vite(resource_path('js/app.js'))
  @vite(resource_path('js/carChargingChart/chargingChart.js'))
</head>

<body class="font-sans bg-background">
  <div class="grid grid-cols-20 gap-10">
    <div class="relative my-4 w-56 md:hidden">
      <input class="peer hidden" type="checkbox" name="select-1" id="select-1" />
      <label for="select-1"
        class="flex w-full cursor-pointer select-none rounded-lg border p-2 px-3 text-sm text-gray-700 ring-blue-700 peer-checked:ring">Dashboards
      </label>
      <svg xmlns="http://www.w3.org/2000/svg"
        class="pointer-events-none absolute right-0 top-3 ml-auto mr-5 h-4 text-slate-700 transition peer-checked:rotate-180"
        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
      </svg>
      <ul
        class="max-h-0 select-none flex-col overflow-hidden rounded-b-lg shadow-md transition-all duration-300 peer-checked:max-h-56 peer-checked:py-3">
        <li class="cursor-pointer px-3 py-3 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">
          <a href="{{ route('solar.dashboard') }}">Solar dashboard</a>
        </li>
        <li class="cursor-pointer px-3 py-3 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">
          <a href="{{ route('electricity.dashboard') }}">Electricity usage</a>
        </li>
        <li class="cursor-pointer px-3 py-3 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">
          <a href="{{ route('carCharging.dashboard') }}">EV charging</a>
        </li>
        <li class="cursor-pointer px-3 py-3 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">
          <a href="{{ route('chargingStations.dashboard') }}">Charging locations</a>
        </li>
      </ul>
    </div>

    <div class="col-span-4 bg-white px-6 lg:px-10 pt-10 h-screen rounded-l-3xl hidden md:block">
      <div class="flex justify-center border-b pb-5">
        <img src="/images/Solar-icon.png" alt="" class="lg:block hidden">
        <h1 class="sm:text-base lg:text-2xl pl-4 font-semibold">Dashboard</h1>
        {{-- <button class="modeToggler text-tableHeadingText ml-auto">
        <img id="modeIcon" src="/images/Light-mode.png" alt="" style="width: 30px; height: 30px;">
      </button> --}}
      </div>

      <h1 class="text-base my-10 underline font-semibold">Dashboards</h1>

      <ul class="menu">
        @include('../layouts/dashboardlinks')
      </ul>

      <h1 class="text-base my-10 underline font-semibold">Other</h1>

      <div>
        <ul>
          @include('../layouts/otherlinks')
        </ul>
      </div>

      <x-logout-modal />
    </div>

    <div class="col-span-20 md:col-span-16 xl:col-span-12 max-h-screen mx-4 mt-4">
      <div class="sm:-mx-6 lg:-mx-8 bg-white rounded-xl mb-6">
        <h1 class="pl-8 text-base md:text-xl py-4 font-bold border-b">Your recent charging sessions</h1>
        <div class="spy-3 sm:px-6 lg:px-8">
          <table class="min-w-full text-left text-sm font-light dark:text-white">
            <thead class="border-b border-neutral-200 font-medium dark:border-white/10">
              <tr>
                <th scope="col" class="px-2 md:px-6 py-3 text-xs md:text-sm">Start time</th>
                <th scope="col" class="px-2 md:px-6 py-3 text-xs md:text-sm">End time</th>
                <th scope="col" class="px-2 md:px-6 py-3 text-xs md:text-sm">Charging Duration (minutes)</th>
                <th scope="col" class="px-2 md:px-6 py-3 text-xs md:text-sm">Charging amount (kWh)</th>
                <th scope="col" class="px-2 md:px-6 py-3 text-xs md:text-sm">Location MRPN</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentCarChargings as $recentCarCharging)
                <tr
                  class="border-b border-neutral-200 transition duration-300 ease-in-out hover:bg-neutral-100 dark:border-white/10 dark:hover:bg-neutral-600">
                  <td class="px-2 md:px-6 py-3 text-xs md:text-sm font-medium">{{ $recentCarCharging->start_time }}</td>
                  <td class="px-2 md:px-6 py-3 text-xs md:text-sm">{{ $recentCarCharging->end_time }}</td>
                  <td class="px-2 md:px-6 py-3 text-xs md:text-sm">
                    {{ \Carbon\Carbon::parse($recentCarCharging->end_time)->diffInMinutes(\Carbon\Carbon::parse($recentCarCharging->start_time)) }}
                  </td>
                  <td class="px-2 md:px-6 py-3 text-xs md:text-sm">{{ $recentCarCharging->charging_amount }}</td>
                  <td class="px-2 md:px-6 py-3 text-xs md:text-sm">{{ $recentCarCharging->location_MPRN }}</td>
                @empty
                  <!-- Displayed when no car chargings are found -->
                  <h4>No Car Chargings found!</h4>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="bg-white p-8 rounded-3xl sm:-mx-6 lg:-mx-8">
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-base md:text-xl font-bold">Daily Energy Consumption for Car Charging</h1>
        </div>

        <div class="bg-gray-100 chargingChart rounded-xl"></div>
      </div>
    </div>

    <div class="col-span-3 max-h-screen mt-4">
      <div class="relative p-6 rounded-2xl bg-white shadow dark:bg-gray-800">
        <div class="text-lg font-semibold">
          <h2 class="mb-6 mt-2 text-xl">Total car charging cost</h2>
        </div>
        <div class="text-3xl font-bold border-b mb-4">
          <span class="totalCost"></span>
        </div>

        <div class="text-lg font-semibold">
          <h2 class="mb-6 mt-2 text-xl">Total energy consumed</h2>
        </div>
        <div class="text-3xl font-bold border-b">
          <span class="totalEnergy"></span>
        </div>
      </div>
      <div class="relative p-6 rounded-2xl bg-white shadow dark:bg-gray-800 mt-5">
        <div class="text-lg font-semibold">
          <h2 class="mb-6 mt-2 text-xl">Average Cost Per Session</h2>
        </div>
        <div class="text-3xl font-bold border-b mb-4">
          <span class="avgCostPerSession"></span>
        </div>

        <div class="text-lg font-semibold">
          <h2 class="mb-6 mt-2 text-xl">Average Energy per session</h2>
        </div>
        <div class="text-3xl font-bold border-b">
          <span class="avgEnergyPerSession"></span>
        </div>
      </div>
      <div class="relative p-6 rounded-2xl bg-white shadow dark:bg-gray-800 mt-5">
        <div class="text-lg font-semibold">
          <h2 class="mb-6 mt-2 text-xl">Average Cost Per kWh</h2>
          <h2 class="mb-4 font-light">Average cost per kwh across all sessions</h2>
        </div>
        <div class="text-3xl font-bold">
          <span class="avgCostPerKWh"></span>
        </div>
      </div>
      <div class="relative p-6 rounded-2xl bg-white shadow dark:bg-gray-800 mt-5">
        <div class="text-lg font-semibold">
          <h2 class="mb-6 mt-2 text-xl">Average Charging Time</h2>
          <h2 class="mb-4 font-light">Average charging time across all sessions</h2>
        </div>
        <div class="text-3xl font-bold">
          <span class="avgChargingTime"></span>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
