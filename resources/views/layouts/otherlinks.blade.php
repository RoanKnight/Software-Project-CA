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
  <li class="mb-10 py-3">
    <a href="{{ route($link['route']) }}" class="flex items-center hover:underline"
      @if (isset($link['onclick'])) onclick="{{ $link['onclick'] }}" @endif>
      <img src="{{ $link['image'] }}" alt="" style="width: 20px; height: 20px">
      <h3 class="ml-2 lg:ml-5 text-xs lg:text-lg font-semibold">{{ $link['title'] }}</h3>
    </a>
  </li>
@endforeach
