@php
  $locationsWithoutSolar = $locations->filter(function ($location) {
      return $location->solarPanels->isEmpty();
  });
@endphp

<form action="{{ route('solar.store') }}" method="post">
  @csrf
  <div class="mb-4">
    <label class="block text-gray-700 text-sm font-bold mb-2">Location</label>
    <span class="text-red-500 text-xs">{{ $errors->first('location_id') }}</span>
    @if ($locationsWithoutSolar->isEmpty())
      <p>You have no locations without a solar panel.</p>
      <P>Please <a href="{{ route('locations.create') }}" class="text-blue-500">create
          one</a> first or remove the solar panel from an existing location.</p>
      </P>
    @else
      <select
        class="appearance-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        name="location_id" id="location_id">
        @foreach ($locationsWithoutSolar as $location)
          <option value="{{ $location->MPRN }}" {{ old('location_id') == $location->MPRN ? 'selected' : '' }}>
            {{ $location->address }}
          </option>
        @endforeach
      </select>
      <button
        class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150"
        type="submit">Create
        Solar Panel</button>
    @endif
  </div>
</form>
