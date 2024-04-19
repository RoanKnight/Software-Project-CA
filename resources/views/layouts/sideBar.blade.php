<div class="relative my-4 w-56 md:hidden">
  <input class="peer hidden" type="checkbox" name="select-1" id="select-1" />
  <label for="select-1"
    class="flex w-full cursor-pointer select-none rounded-lg border p-2 px-3 text-sm text-gray-700 ring-blue-700 peer-checked:ring">Dashboards
  </label>
  <svg xmlns="http://www.w3.org/2000/svg"
    class="pointer-events-none absolute right-0 top-3 ml-auto mr-5 h-4 text-slate-700 transition peer-checked:rotate-180"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
  </svg>
  <ul
    class="max-h-0 select-none flex-col overflow-hidden rounded-b-lg shadow-md transition-all duration-300 peer-checked:max-h-56 peer-checked:py-3">
    <!-- Dashboard links -->
    <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">
      <a href="{{ route('solar.dashboard') }}">Solar dashboard</a>
    </li>
    <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">
      <a href="{{ route('electricity.dashboard') }}">Electricity usage</a>
    </li>
    <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">
      <a href="{{ route('carCharging.dashboard') }}">EV charging</a>
    </li>
    <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">
      <a href="{{ route('carCharging.chargingStations') }}">Charging locations</a>
    </li>
  </ul>
</div>

<div class="col-span-4 bg-white px-6 lg:px-10 pt-10 min-h-screen rounded-l-3xl hidden md:block">
  <!-- Header -->
  <div class="flex justify-center border-b pb-5">
    <img src="/images/Solar-icon.png" alt="" class="lg:block hidden">
    <h1 class="sm:text-base lg:text-2xl pl-4 font-semibold">Dashboard</h1>
    {{-- <button class="modeToggler text-tableHeadingText ml-auto">
    <img id="modeIcon" src="/images/Light-mode.png" alt="" style="width: 30px; height: 30px;">
  </button> --}}
  </div>

  <!-- Section for Dashboards -->
  <h1 class="text-base my-10 underline font-semibold">Dashboards</h1>
  <ul class="menu">
    <!-- Include dashboard links -->
    @include('../layouts/dashboardlinks')
  </ul>

  <!-- Section for Other Links -->
  <h1 class="text-base my-10 underline font-semibold">Other</h1>
  <div>
    <ul>
      <!-- Include other links -->
      @include('../layouts/otherlinks')
    </ul>
  </div>

  <!-- Logout Modal Component -->
  <x-logout-modal />
</div>
