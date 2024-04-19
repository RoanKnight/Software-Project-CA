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
    <div class="container mx-auto pt-10">
      <div class="mx-auto max-w-xl">
        <h1 class="font-bold text-gray-900 not-italic text-2xl text-center">
          Edit Location
        </h1>

        <form action="{{ route('locations.update', $location->MPRN) }}" method="post"
          class="bg-white shadow rounded px-8 pt-6 pb-8 mb-4 mt-4">
          @csrf
          @method('PATCH')

          @foreach (['MPRN', 'address', 'EirCode'] as $field)
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full px-3">
                <div class="flex justify-between">
                  <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                    for="{{ $field }}">
                    {{ $field }}
                  </label>
                  <p class="text-red-500 text-xs italic">{{ $errors->first($field) }}</p>
                </div>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border {{ $errors->has($field) ? 'border-red-500' : 'border-gray-200' }} rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                  id="{{ $field }}" type="text" name="{{ $field }}"
                  placeholder="Location's {{ $field }}...." value="{{ $location->$field }}">
              </div>
            </div>
          @endforeach

          <div class="flex justify-between items-center mb-6">
            <button
              class="bg-indigo-700 h-12 hover:shadow hover:bg-indigo-500 text-white font-bold px-4 rounded focus:outline-none focus:shadow-outline"
              type="submit">
              Update Location
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>
