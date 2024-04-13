<section class="space-y-6">
  <header>
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
      {{ __('Manage application database') }}
    </h2>

    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
      {{ __('View and edit entries in your database') }}
    </p>

    <div class="mt-6">
      <div>
        <div class="border p-5 mb-5">
          <h1 class="mb-5">Users table</h1>
          <a href="{{ route('users.index') }}"
            class="inline-block bg-black text-white py-2 px-4 rounded-lg">
            View all users
          </a>
        </div>

        <div class="border p-5 mb-5">
          <h1 class="mb-5">Locations table</h1>
          <a href="{{ route('locations.index') }}"
            class="inline-block bg-black text-white py-2 px-4 rounded-lg">
            View all locations
          </a>
        </div>

        <div class="border p-5 mb-5">
          <h1 class="mb-5">Solar panel table</h1>
          <a href="{{ route('solar.index') }}"
            class="inline-block bg-black text-white py-2 px-4 rounded-lg">
            View all solar instances
          </a>
        </div>

        <div class="border p-5 mb-5">
          <h1 class="mb-5">Electricity usage table</h1>
          <a href="{{ route('electricity.index') }}"
            class="inline-block bg-black text-white py-2 px-4 rounded-lg">
            View all electricity instances
          </a>
        </div>

        <div class="border p-5 mb-5">
          <h1 class="mb-5">Car charging table table</h1>
          <a href="{{ route('carCharging.index') }}"
            class="inline-block bg-black text-white py-2 px-4 rounded-lg">
            View all Car charging instances
          </a>
        </div>
      </div>
  </header>
</section>
