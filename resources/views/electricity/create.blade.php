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
      <div class="relative">
        <form action="{{ route('electricity.store') }}" method="post">
          @csrf
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Location</label>
            <span class="text-red-500 text-xs">{{ $errors->first('location_id') }}</span>
            @if ($locations->isEmpty())
              <p>You have no locations. Please <a href="{{ route('locations.create') }}" class="text-blue-500">create
                  one</a> first.</p>
            @else
              <select
                class="appearance-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                name="location_id" id="location_id">
                @foreach ($locations as $location)
                  <option value="{{ $location->MPRN }}" {{ old('location_id') == $location->MPRN ? 'selected' : '' }}>
                    {{ $location->address }}
                  </option>
                @endforeach
              </select>
            @endif
          </div>

          <!-- Add more input fields for other attributes of the electricity usage as needed -->

          <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">Create
            electricity usage</button>
      </div>
      </form>
    </div>
  </div>
  </div>
</body>

</html>
