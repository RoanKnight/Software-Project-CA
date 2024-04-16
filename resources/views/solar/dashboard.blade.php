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

  @vite(resource_path('css/app.css'))
  @vite(resource_path('js/app.js'))
  @vite('resources//js/solarCharts/Allcharts.js')
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
        <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">Solar dashboard
        </li>
        @if (auth()->check() && auth()->user()->isAdmin())
          <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">Electricity
            usage
          </li>
        @endif
        <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">EV charging
        </li>
        <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">Charging
          locations
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
          <li class="mb-6 px-4 py-3">
            <a href="{{ route('profile.edit') }}" class="flex hover:underline">
              <img src="/images/User-icon.png" alt="" style="width: 25px; height: 25px">
              <h3 class="text-xl font-normal ml-5">User</h3>
            </a>
          </li>
          <li class="mb-6 px-4 py-3">
            <a href="{{ route('logout') }}" class="flex hover:underline"
              onclick="event.preventDefault(); openModal('.logoutModal')">
              <img src="/images/Log-out-icon.png" alt="" style="width: 25px; height: 25px">
              <h3 class="text-xl font-normal ml-5">Log out</h3>
            </a>
          </li>
        </ul>
      </div>

      <x-logout-modal />
    </div>

    {{-- <div class="col-span-12 max-h-screen">
      <p class="text-lg font-light mt-6 mb-6">General overview</p>
      <h1 class="text-4xl font-medium mb-10">Your energy generation metrics</h1>

      <div class="bg-white p-8 rounded-3xl mb-4">
        <div class="flex items-center justify-between mb-10">
          <h1 class="text-3xl">Your solar panel energy generation</h1>
          <select name="" class="rounded-xl ml-4">
            <option value="hourly">Hourly</option>
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
          </select>
        </div>

        <div class="bg-gray-100 flex-grow p-2 exampleChart rounded-xl"></div>
      </div>

      <div class="bg-white p-10 rounded-3xl w-full flex justify-between solarData">
        <div>
          <h2 class="text-xl mb-8">Total energy generated</h2>
          <h3 class="text-3xl mb-5 totalEnergy"></h3>
          <p class="previousTotal"></p>
        </div>

        <div>
          <h2 class="text-xl mb-8">Average energy generation</h2>
          <h3 class="text-3xl mb-5 averageEnergy"></h3>
          <p class="averageComparison"></p>
        </div>

        @php
          $currentEnergy = rand(10, 20) / 10.0;
          $previousEntry = rand(10, 20) / 10.0;
          $comparison = $currentEnergy > $previousEntry ? 'more' : 'less';
          $colorClass = $currentEnergy > $previousEntry ? 'text-green-500' : 'text-red-500';
        @endphp

        <div>
          <h2 class="text-xl mb-8">Current energy generation</h2>
          <h3 class="text-3xl mb-5 currentEnergy">{{ $currentEnergy }} kWh</h3>
          <p class="comparison"><span
              class="currentComparison {{ $colorClass }}">{{ abs($currentEnergy - $previousEntry) }}</span> kWh
            {{ $comparison }} than previous hour</p>
        </div>
      </div>
    </div>

    @php
      $sunrise = $weather['sys']['sunrise'];
      $sunset = $weather['sys']['sunset'];
      $hoursOfSunlight = ($sunset - $sunrise) / 3600;
      $estimatedSolarGeneration = $currentEnergy * $hoursOfSunlight;
      $estimatedSolarGeneration = number_format($estimatedSolarGeneration, 2);
    @endphp

    <div class="col-span-3 flex flex-col max-h-screen">
      <div class="bg-white h-fit mt-48 px-5 py-10 rounded-3xl flex-shrink-0">
        <div class="text-center">
          <h1 class="text-3xl font-semibold">Today</h1>
          <h3 class="text-xl">{{ now()->format('F j') }}</h3>
        </div>
        <img class="mx-auto w-52" src="http://openweathermap.org/img/w/{{ $weather['weather'][0]['icon'] }}.png"
          alt="Weather icon">

        <p class="text-lg text-center">{{ $weather['weather'][0]['description'] }}</p>

        <div class="flex justify-between mt-5 mx-8">
          <div>
            <h2 class="text-2xl mb-4">Temp</h2>
            <p class="mb-6 text-3xl font-bold"> {{ $weather['main']['temp'] - 273.15 }}°C</p>

            <div class="flex">
              <img src="/images/Humidity.png" alt="" class="mr-4" style="height: 25px; width: 25px">
              <p>{{ $weather['main']['humidity'] }}%</p>
            </div>
          </div>
          <div>
            <h2 class="text-2xl mb-4">Feels like</h2>
            <p class="mb-6 text-3xl font-bold"> {{ $weather['main']['feels_like'] - 273.15 }}°C</p>

            <div class="flex">
              <img src="/images/Wind.png" alt="" class="mr-4" style="height: 25px; width: 25px">
              <p>{{ $weather['wind']['speed'] }} m/s</p>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-white h-fit mt-6 px-5 py-10 rounded-3xl flex-shrink-0">
        <div class="text-center">
          <h1 class="text-3xl font-semibold">Tomorrow</h1>
          <h3 class="text-xl">{{ now()->addDay()->format('F j') }}</h3>
        </div>
        <img class="mx-auto w-52"
          src="http://openweathermap.org/img/w/{{ $tomorrowWeather['weather'][0]['icon'] }}.png" alt="Weather icon">

        <p class="text-lg text-center">{{ $tomorrowWeather['weather'][0]['description'] }}</p>

        <div class="flex justify-between mt-5 mx-8">
          <div>
            <h2 class="text-2xl mb-4">Temp</h2>
            <p class="mb-6 text-3xl font-bold"> {{ $tomorrowWeather['main']['temp'] - 273.15 }}°C</p>

            <div class="flex">
              <img src="/images/Humidity.png" alt="" class="mr-4" style="height: 25px; width: 25px">
              <p>{{ $tomorrowWeather['main']['humidity'] }}%</p>
            </div>
          </div>
          <div>
            <h2 class="text-2xl mb-4">Feels like</h2>
            <p class="mb-6 text-3xl font-bold"> {{ $tomorrowWeather['main']['feels_like'] - 273.15 }}°C</p>

            <div class="flex">
              <img src="/images/Wind.png" alt="" class="mr-4" style="height: 25px; width: 25px">
              <p>{{ $tomorrowWeather['wind']['speed'] }} m/s</p>
            </div>
          </div>
        </div>
      </div>
    </div> --}}
  </div>
</body>

</html>
