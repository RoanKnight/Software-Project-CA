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
    <div class="col-span-4 bg-white px-10 pt-10 h-screen rounded-l-3xl">
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
    <div class="col-span-10 mt-10 bg-white h-fit rounded-xl">
      <table class="w-full text-sm text-left rounded-xl overflow-hidden">
        <!-- Table Header -->
        <thead class="text-xs uppercase bg-tableHeadingBG">
          <tr>
            <th scope="col" class="px-6 py-3 text-tableHeadingText">
              Start Time
            </th>
            <th scope="col" class="px-6 py-3 text-tableHeadingText">
              End Time
            </th>
            <th scope="col" class="px-6 py-3 text-tableHeadingText">
              Charging Amount (kwh)
            </th>
            <th scope="col" class="px-6 py-3 text-tableHeadingText">
              Action
            </th>
          </tr>
        </thead>

        <!-- Table Body -->
        @forelse($carChargings as $carCharging)
          <tr class="bg-tableRowBG">
            <td scope="row" class="px-6 py-4 font-bold text-tableRowText whitespace-nowrap">
              {{ $carCharging->start_time }}
            </td>
            <td class="px-6 py-4 text-tableRowText">
              {{ $carCharging->end_time }}
            </td>
            <td class="px-6 py-4 text-tableRowText">
              {{ $carCharging->charging_amount }}
            </td>
            <td class="px-6 pt-4">
              <!-- Link to the show page for the car charging -->
              <a href="{{ route('carCharging.show', $carCharging->id) }}"
                class="text-blue-500 hover:text-blue-700 underline">View</a>
            </td>
          </tr>
        @empty
          <!-- Displayed when no car chargings are found -->
          <h4>No Car Chargings found!</h4>
        @endforelse
      </table>
    </div>
  </div>
</body>

</html>
