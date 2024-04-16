<section class="space-y-6">
  <header>
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
      {{ __('Manage Your Locations') }}
    </h2>

    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
      {{ __('View and edit new or existing locations') }}
    </p>

    <div class="mt-6">
      <div>
        <div class="mb-5">
          <h1 class="mb-4 font-semibold">View your locations</h1>
          <a href="{{ route('locations.userLocations') }}"
            class="inline-block bg-black text-white py-2 px-4 rounded-lg">
            View your locations
          </a>
        </div>

        <div class="mb-5">
          <h1 class="mb-4 font-semibold">Set up a new location</h1>
          <a href="{{ route('locations.create') }}"
            class="inline-block bg-black text-white py-2 px-4 rounded-lg">
            Set up new location
          </a>
        </div>
      </div>
  </header>
</section>
