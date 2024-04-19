<!-- dashboardlinks.blade.php -->
@php
  // Array to store other links such as profile edit and logout
  $otherLinks = [
      [
          'route' => 'profile.edit',
          'image' => '/images/User-icon.png',
          'title' => Auth::user()->name, // Get the authenticated user's name
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
  <!-- Iterating over each other link -->
  <li class="mb-10 py-3">
    <a href="{{ route($link['route']) }}" class="flex items-center hover:underline"
      @if (isset($link['onclick'])) onclick="{{ $link['onclick'] }}" @endif>
      <!-- Conditionally adding onclick event -->
      <img src="{{ $link['image'] }}" alt="" style="width: 20px; height: 20px"> <!-- Displaying link icon -->
      <h3 class="ml-2 lg:ml-5 text-xs lg:text-lg font-semibold">{{ $link['title'] }}</h3> <!-- Displaying link title -->
    </a>
  </li>
@endforeach
