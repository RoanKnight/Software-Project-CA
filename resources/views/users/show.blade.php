@extends('layouts.myApp')

@section('head')
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
@endsection

@section('header')
  <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    User Details
  </h2>
@endsection

@section('content')
  <!-- Main content section -->
  <div class="container mx-auto px-4 my-10">
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
      <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
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
          </tr>
        </thead>
        <!-- Table body -->
        <tbody>
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
              @if ($user->deleted)
                <span class="text-red-500">True</span>
              @else
                <span class="text-green-500">False</span>
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Conditional forms -->
    @if (!$user->deleted)
      <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-4 py-2 mt-3 text-white bg-red-500 rounded hover:bg-red-700">Delete</button>
      </form>
    @else
      <form action="{{ route('users.restore', $user->id) }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="px-4 py-2 mt-3 text-white bg-green-500 rounded hover:bg-green-700">Restore</button>
      </form>
    @endif

    @if ($user->role == 'user')
      <form action="{{ route('users.promote', $user->id) }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">Promote</button>
      </form>
    @endif
  </div>
@endsection
