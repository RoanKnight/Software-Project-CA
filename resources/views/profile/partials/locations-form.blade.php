<section class="space-y-6 py-6">
  <header>
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
      {{ __('Manage Your Locations') }}
    </h2>

    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
      {{ __('View and edit new or existing locations') }}
    </p>

    <a href="{{ route('locations.create') }}"
      class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150">
      {{ __('Set up a new location') }}
    </a>
  </header>
  </header>

  @forelse($locations as $location)
    @if (!$location->deleted)
      <div class="border rounded-xl w-full">
        @if (auth()->user()->active_MPRN == $location->MPRN)
          <h3 class="w-full border-b px-6 py-2">Active</h3>
        @else
        @endif
        <h3 class="font-semibold px-6 mt-2">{{ $location->MPRN }} </h3>
        <h3 class="px-6 mt-2">User: {{ Auth::user()->name }}</h3>
        <h3 class="px-6 mt-2">Address: {{ $location->address }}</h3>
        <h3 class="px-6 mt-2">EirCode: <span class="font-semibold">{{ $location->EirCode }}</span></h3>
        <div class="flex px-6 mt-2">
          <form method="POST" action="{{ route('locations.destroy', $location->MPRN) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="mt-3 mr-6 text-black hover:text-gray-500">Delete</button>
          </form>

          @if (auth()->user()->active_MPRN != $location->MPRN)
            <form method="POST" action="{{ route('setActiveLocation', $location->MPRN) }}">
              @csrf
              <button type="submit" class="mt-3">Make Active</button>
            </form>
          @endif
        </div>
      </div>
    @endif
  @empty
    <h4>No Locations found!</h4>
  @endforelse
</section>
