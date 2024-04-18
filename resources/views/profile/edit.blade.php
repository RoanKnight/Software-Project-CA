<x-app-layout>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap"
    rel="stylesheet" />
  <style>
    * {
      font-family: 'Source Sans Pro';
    }
  </style>

  <div class="mx-4 min-h-screen max-w-screen-xl sm:mx-8 xl:mx-auto">
    <h1 class="border-b py-6 text-4xl font-semibold">Settings</h1>
    <div class="grid grid-cols-8 pt-3 sm:grid-cols-10">
      <div class="relative my-4 w-56 sm:hidden">
        <input class="peer hidden" type="checkbox" name="select-1" id="select-1" />
        <label for="select-1"
          class="flex w-full cursor-pointer select-none rounded-lg border p-2 px-3 text-sm text-gray-700 ring-blue-700 peer-checked:ring">Settings
        </label>
        <svg xmlns="http://www.w3.org/2000/svg"
          class="pointer-events-none absolute right-0 top-3 ml-auto mr-5 h-4 text-slate-700 transition peer-checked:rotate-180"
          fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
        <ul
          class="max-h-0 select-none flex-col overflow-hidden rounded-b-lg shadow-md transition-all duration-300 peer-checked:max-h-56 peer-checked:py-3">
          <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">Your account
          </li>
          @if (auth()->check() && auth()->user()->isAdmin())
            <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">Database
              management</li>
          @endif
          <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">Your locations
          </li>
          <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">Your solar
            panels</li>
          <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">Your
            electricity usage</li>
          <li class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white">Your car
            chargings</li>
        </ul>
      </div>

      <div class="col-span-2 hidden sm:block">
        <ul class="menu">
          <li
            class="menu-item mt-5 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition border-l-blue-700  hover:border-l-blue-700 hover:text-blue-700 text-blue-700 active">
            Your account
          </li>
          @if (auth()->check() && auth()->user()->isAdmin())
            <li
              class="menu-item mt-5 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition hover:border-l-blue-700 hover:text-blue-700">
              Database management
            </li>
          @endif
          <li
            class="menu-item mt-5 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition hover:border-l-blue-700 hover:text-blue-700">
            Your locations
          </li>
          <li
            class="menu-item mt-5 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition hover:border-l-blue-700 hover:text-blue-700">
            Your solar panels
          </li>
          <li
            class="menu-item mt-5 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition hover:border-l-blue-700 hover:text-blue-700">
            Your electricity usage
          </li>
          <li
            class="menu-item mt-5 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition hover:border-l-blue-700 hover:text-blue-700">
            Your car chargings
          </li>
        </ul>
      </div>

      <div class="col-span-8 overflow-hidden rounded-xl sm:bg-gray-50 sm:px-8 sm:shadow">
        <div class="pt-4">
          <h1 class="py-2 text-2xl font-semibold">Account settings</h1>
        </div>
        <hr class="mt-4 mb-8" />
        <div class="account-settings form-content active">
          @include('profile.partials.update-profile-information-form')
          @include('profile.partials.update-password-form')
          @include('profile.partials.delete-user-form')
        </div>

        @if (auth()->check() && auth()->user()->isAdmin())
          <div class="database-management form-content">
            @include('profile.partials.database-management-form')
          </div>
        @else
        @endif

        <div class="locations form-content">
          @include('profile.partials.locations-form')
        </div>

        <div class="solar-panels form-content">
          @include('profile.partials.solar-form')
        </div>

        <div class="electricity-usage form-content">
          @include('profile.partials.electricity-form')
        </div>

        <div class="car-chargings form-content">
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
