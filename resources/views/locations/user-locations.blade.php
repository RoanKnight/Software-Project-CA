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
  <div class="h-fit">
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
                Active
              </th>
              <th scope="col" class="px-6 py-3 text-tableHeadingText">
                Action
              </th>
              <th scope="col" class="px-6 py-3 text-tableHeadingText">
                View
              </th>
            </tr>
          </thead>

          @php
            $locations = auth()
                ->user()
                ->locations->filter(function ($location) {
                    return !$location->deleted;
                });
          @endphp

          <!-- Table Body -->

          @php
            $locations = auth()
                ->user()
                ->locations->filter(function ($location) {
                    return !$location->deleted;
                });
          @endphp

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
                {{ auth()->user()->active_MPRN == $location->MPRN ? 'Yes' : 'No' }}
              </td>
              <td class="px-6 py-4 text-tableRowText">
                @if (auth()->user()->active_MPRN != $location->MPRN)
                  <form method="POST" action="{{ route('setActiveLocation', $location->MPRN) }}">
                    @csrf
                    <button type="submit">Make Active</button>
                  </form>
                @endif
              </td>
              <td class="px-6 py-4  text-tableRowText">
                <a class="text-blue-500 hover:text-blue-700 underline"
                  href="{{ route('locations.show', $location->MPRN) }}">View</a>
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
