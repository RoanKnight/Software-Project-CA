<!-- dashboardlinks.blade.php -->
@php
  $otherLinks = [
      [
          'route' => 'profile.edit',
          'image' => '/images/User-icon.png',
          'title' => Auth::user()->name,
      ],
      [
          'route' => 'logout',
          'image' => '/images/Log-out-icon.png',
          'title' => 'Log out',
          'onclick' => "event.preventDefault(); openModal('.logoutModal')",
      ],
  ];
@endphp

@foreach ($otherLinks as $link)
  <li class="mb-10 px-4 py-3 {{ Route::currentRouteName() == $link['route'] ? '' : '' }}">
    <a href="{{ route($link['route']) }}" class="flex hover:underline"
      @if (isset($link['onclick'])) onclick="{{ $link['onclick'] }}" @endif>
      <img src="{{ $link['image'] }}" alt="" style="width: 25px; height: 25px">
      <h3 class="text-xl font-normal ml-5">{{ $link['title'] }}</h3>
    </a>
  </li>
@endforeach