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
  @vite('resources//js/electricityCharts/Allcharts.js')
</head>

<body class="font-sans bg-background">
  <div class="grid grid-cols-20 gap-2">
    @include('layouts.sideBar')

    <div class="col-span-20 md:col-span-16 xl:col-span-12 max-h-screen mx-4">
      <p class="text-sm md:text-lg font-light mt-6 mb-6">General overview</p>
      <h1 class="text-xl sm:text-2xl lg:text-4xl font-medium mb-10">Your electricity usage metrics</h1>

      <div class="bg-white p-8 rounded-3xl mb-4">
        <div class="flex items-center justify-between mb-10">
          <h1 class="text-lg md:text-xl lg:text-2xl xl:text-3xl">Your electricity consumption</h1>
          <select name="" class="text-xs md:text-base rounded-xl ml-4">
            <option value="hourly">Hourly</option>
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
          </select>
        </div>

        <div class="bg-gray-100 flex-grow p-3 exampleChart rounded-xl"></div>
      </div>

      <div class="bg-white p-6 lg:p-10 rounded-3xl w-full flex justify-between solarData">
        <div>
          <h2 class="text-sm md:text-base lg:text-lg xl:text-2xl font-medium mb-8">Total electricity consumed</h2>
          <h3 class="text-lg md:text-xl lg:text-2xl xl:text-3xl font-semibold mb-5 totalEnergy"></h3>
          <p class="previousTotal text-xs md:text-sm lg:text-lg"></p>
        </div>

        <div>
          <h2 class="text-sm md:text-base lg:text-lg xl:text-2xl font-medium mb-8">Average electricity usage</h2>
          <h3 class="text-lg md:text-xl lg:text-2xl xl:text-3xl font-semibold mb-5 averageEnergy"></h3>
          <p class="averageComparison text-xs md:text-sm lg:text-lg"></p>
        </div>

        @php
          $currentEnergy = rand(1, 2) / 10.0;
          $previousEntry = rand(1, 2) / 10.0;
          $comparison = $currentEnergy > $previousEntry ? 'more' : 'less';
          $colorClass = $currentEnergy > $previousEntry ? 'text-red-500' : 'text-green-500';
          $averageEnergy = rand(1, 2) / 10.0;
          $projectedCost = $currentEnergy * 0.12;
        @endphp

        <div>
          <h2 class="text-sm md:text-base lg:text-lg xl:text-2xl font-medium mb-8">Current electricity usage</h2>
          <h3 class="text-lg md:text-xl lg:text-2xl xl:text-3xl font-semibold mb-5 currentEnergy">{{ $currentEnergy }}
            kWh</h3>
          <p class="comparison text-xs md:text-sm lg:text-lg"><span
              class="currentComparison {{ $colorClass }}">{{ abs($currentEnergy - $previousEntry) }}</span> kWh
            {{ $comparison }} than previous hour</p>
        </div>

      </div>
    </div>

    <div class="col-span-3 flex flex-col max-h-screen mt-40">
      <div class="relative p-6 rounded-2xl bg-white shadow dark:bg-gray-800">
        <div class="text-lg font-semibold">
          <h2 class="mb-6 mt-2 text-xl">Total electricity cost</h2>
          <h2 class="mb-4 font-light">Your total electricity cost</h2>
        </div>
        <div class="text-3xl font-bold">
          <span class="totalCost"></span>
        </div>
        <div class="flex items-center text-sm font-medium">
          <span class="mt-6 costComparison"></span>
        </div>
      </div>
      <div class="relative p-6 rounded-2xl bg-white shadow dark:bg-gray-800 mt-5">
        <div class="text-lg font-semibold">
          <h2 class="mb-6 mt-2 text-xl">Estimated average cost</h2>
          <h2 class="mb-4 font-light">Your average electricity cost</h2>
        </div>
        <div class="text-3xl font-bold">
          <span class="averageCost"></span>
        </div>
      </div>
      <div class="relative p-6 rounded-2xl bg-white shadow dark:bg-gray-800 mt-5">
        <div class="text-lg font-semibold">
            <h2 class="mb-6 mt-2 text-xl">Peak Usage Times</h2>
            <h2 class="mb-4 font-light">Your highest electricity usage times</h2>
        </div>
        <div class="text-xl font-medium">
            <span class="peakUsageTimes"></span>
        </div>
    </div>
    </div>
  </div>
</body>

</html>
