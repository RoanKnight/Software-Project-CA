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
        <form action="{{ route('chargingStations.store') }}" method="post">
          @csrf
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Address</label>
            <span class="text-red-500 text-xs">{{ $errors->first('address') }}</span>
            <input class="appearance-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="address" id="address" value="{{ old('address') }}">
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Charging Efficiency</label>
            <span class="text-red-500 text-xs">{{ $errors->first('charging_efficiency') }}</span>
            <input class="appearance-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="charging_efficiency" id="charging_efficiency" value="{{ old('charging_efficiency') }}">
          </div>

          <!-- Add more input fields for other attributes of the charging station as needed -->

          <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">Create
            charging station</button>
        </form>
      </div>
    </div>
  </div>
</body>

</html>