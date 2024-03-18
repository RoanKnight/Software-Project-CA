@extends('layouts.myApp')

<!-- Include the CSS file -->
@section('head')
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
@endsection

@section('header')
  <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    Users
  </h2>
@endsection

@section('content')
  <!-- Table for displaying user information -->
  <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
      <!-- Table Header -->
      <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
          <th scope="col" class="px-6 py-3">
            Name
          </th>
          <th scope="col" class="px-6 py-3">
            Email
          </th>
          <th scope="col" class="px-6 py-3">
            Role
          </th>
          <th scope="col" class="px-6 py-3">
            Deleted
          </th>
          <th scope="col" class="px-6 py-3">
            View
          </th>
        </tr>
      </thead>

      <!-- Table Body -->
      @forelse($users as $user)
        <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
          <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
            {{ $user->name }}
          </td>
          <td class="px-6 py-4">
            {{ $user->email }}
          </td>
          <td class="px-6 py-4">
            {{ $user->role }}
          </td>
          <td class="px-6 py-4">
            <!-- Display 'True' if deleted, 'False' otherwise -->
            @if ($user->deleted)
              <span class="text-red-500">True</span>
            @else
              <span class="text-green-500">False</span>
            @endif
          </td>
          <td class="px-6 py-4">
            <!-- Link to the show page for the user -->
            <a href="{{ route('users.show', $user->id) }}" class="text-blue-500 hover:text-blue-700 underline">View</a>
          </td>
        </tr>
      @empty
        <!-- Displayed when no users are found -->
        <h4>No Users found!</h4>
      @endforelse
    </table>
  </div>
@endsection
