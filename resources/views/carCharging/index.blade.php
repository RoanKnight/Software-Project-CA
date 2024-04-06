<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  @vite(resource_path('css/app.css'))
  @vite(resource_path('js/app.js'))
</head>

<body class="font-sans antialiased bg-background">
  <div class="min-h-screen">
    @include('layouts.navigation')
    <header>
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <button class="modeToggler bg-background text-tableHeadingText">
          <img id="modeIcon" src="/images/Light-mode.png" alt="" style="width: 30px; height: 30px;">
        </button>
      </div>
    </header>
    <div class="container mx-auto mt-10 px-4">
      <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left">
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
                Location MPRN
              </th>
              <th scope="col" class="px-6 py-3 text-tableHeadingText">
                Charging Amount (kwh)
              </th>
              <th scope="col" class="px-6 py-3 text-tableHeadingText">
                Deleted
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
                {{ $carCharging->location_MPRN }}
              </td>
              <td class="px-6 py-4 text-tableRowText">
                {{ $carCharging->charging_amount }}
              </td>
              <td class="px-6 py-4 text-tableRowText">
                <!-- Display 'True' if deleted, 'False' otherwise -->
                @if ($carCharging->deleted || $carCharging->location->deleted || $carCharging->location->user->deleted)
                  <span class="text-red-500">True</span>
                @else
                  <span class="text-green-500">False</span>
                @endif
              </td>
              <td class="px-6 py-4">
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
      <form class="py-4" method="POST" action="{{ route('carCharging.store') }}">
        @csrf
        <button type="submit">Create Charging Instances</button>
      </form>
    </div>
  </div>

  @if (session('status'))
    <div id='alert'>
      {{ session('status') }}
    </div>
  @endif
</body>

</html>
