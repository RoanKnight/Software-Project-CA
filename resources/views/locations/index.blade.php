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
                MPRN
              </th>
              <th scope="col" class="px-6 py-3 text-tableHeadingText">
                Address
              </th>
              <th scope="col" class="px-6 py-3 text-tableHeadingText">
                EirCode
              </th>
              <th scope="col" class="px-6 py-3 text-tableHeadingText">
                User ID
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
          @forelse($locations as $location)
            <tr class="bg-tableRowBG">
              <td scope="row" class="px-6 py-4 font-bold text-tableRowText whitespace-nowrap">
                {{ $location->MPRN }}
              </td>
              <td class="px-6 py-4 text-tableRowText">
                {{ $location->address }}
              </td>
              <td class="px-6 py-4 text-tableRowText">
                {{ $location->EirCode }}
              </td>
              <td class="px-6 py-4 text-tableRowText">
                {{ $location->user_id }}
              </td>
              <td class="px-6 py-4 text-tableRowText">
                <!-- Display 'True' if deleted, 'False' otherwise -->
                @if ($location->deleted)
                  <span class="text-red-500">True</span>
                @else
                  <span class="text-green-500">False</span>
                @endif
              </td>
              <td class="px-6 py-4">
                <!-- Link to the show page for the location -->
                <a href="{{ route('locations.show', $location->MPRN) }}" class="text-blue-500 hover:text-blue-700 underline">View</a>
              </td>
            </tr>
          @empty
            <!-- Displayed when no locations are found -->
            <h4>No Locations found!</h4>
          @endforelse
        </table>
      </div>
    </div>
  </div>

  @if (session('status'))
    <div id='alert'>
      {{ session('status') }}
    </div>
  @endif
</body>

</html>