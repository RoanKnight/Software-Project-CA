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

  @vite(resource_path('css/app.css'))
  @vite(resource_path('js/app.js'))
</head>

<body class="font-sans bg-background">
  <div class="grid grid-cols-20 gap-6">
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
        <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">
          <a href="{{ route('solar.dashboard') }}">Solar dashboard</a>
        </li>
        <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">
          <a href="{{ route('electricity.dashboard') }}">Electricity usage</a>
        </li>
        <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">
          <a href="{{ route('carCharging.dashboard') }}">EV charging</a>
        </li>
        <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">
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

    <div class="col-span-20 md:col-span-16 max-h-screen my-10 rounded-3xl mr-6">
      <div class="map-display h-screen"></div>
    </div>
  </div>

  <script>
    var stations = @json($stations);
  </script>
</body>

</html>
