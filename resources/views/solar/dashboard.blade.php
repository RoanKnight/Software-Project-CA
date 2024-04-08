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
  @vite(resource_path('js/solarCharts.js'))
</head>

<body class="font-sans bg-background">
  <div class="grid grid-cols-20 gap-6">
    <div class="col-span-3 bg-white px-10 pt-10 h-screen rounded-l-3xl">
      <div class="flex justify-center border-b pb-5">
        <img src="/images/Solar-icon.png" alt="">
        <h1 class="text-3xl pl-4 font-medium">Dashboard</h1>
        {{-- <button class="modeToggler text-tableHeadingText ml-auto">
        <img id="modeIcon" src="/images/Light-mode.png" alt="" style="width: 30px; height: 30px;">
      </button> --}}
      </div>

      <div class="my-12">
        <p class="text-md font-light">Dashboards</p>
      </div>

      <ul>
        @include('layouts.dashboardlinks')
      </ul>

      <div class="mt-28 mb-10">
        <p class="text-md font-light">Other</p>
      </div>

      <ul>
        @include('layouts.otherlinks')
      </ul>

      <x-logout-modal />
    </div>

    <div class="col-span-12 max-h-full">
      <p class="text-lg font-light mt-12 mb-10">General overview</p>
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

    <div class="col-span-4 bg-white h-fit mt-48 p-5 rounded-3xl">
      <p>Current temperature: {{ $weather['main']['temp'] - 273.15 }}°C</p>
      <p>Feels like: {{ $weather['main']['feels_like'] - 273.15 }}°C</p>
      <p>Humidity: {{ $weather['main']['humidity'] }}%</p>
      <p>Wind speed: {{ $weather['wind']['speed'] }} m/s</p>
      <p>Sunrise: {{ date('H:i:s', $weather['sys']['sunrise']) }}</p>
      <p>Sunset: {{ date('H:i:s', $weather['sys']['sunset']) }}</p>
      <p>Atmospheric pressure: {{ $weather['main']['pressure'] }} hPa</p>
      <p>Estimated solar generation today: {{ $estimatedSolarGeneration }} kWh</p>
    </div>
  </div>
</body>

</html>
