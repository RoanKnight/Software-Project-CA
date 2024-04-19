@extends('layouts.myApp')

@section('head')
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
@endsection

@section('header')
  <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    Location Details
  </h2>
@endsection

@section('content')
  <div class="container mx-auto px-4 my-10">
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
      <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" class="px-6 py-3">
              MPRN
            </th>
            <th scope="col" class="px-6 py-3">
              Address
            </th>
            <th scope="col" class="px-6 py-3">
              EirCode
            </th>
            <th scope="col" class="px-6 py-3">
              Deleted
            </th>
          </tr>
        </thead>
        <tbody>
          <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
            <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
              {{ $location->MPRN }}
            </td>
            <td class="px-6 py-4">
              {{ $location->address }}
            </td>
            <td class="px-6 py-4">
              {{ $location->EirCode }}
            </td>
            <td class="px-6 py-4">
              @if ($location->deleted || $location->user->deleted)
                <span class="text-red-500">True</span>
              @else
                <span class="text-green-500">False</span>
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    </div>    
    @if ($location->deleted || $location->user->deleted)
      <form method="POST" action="{{ route('locations.restore', $location->MPRN) }}">
        @csrf
        @method('PATCH')
        <button type="submit" class="px-4 py-2 mt-3 text-white bg-green-500 rounded hover:bg-green-700">Restore</button>
      </form>
    @else
      <form method="POST" action="{{ route('locations.destroy', $location->MPRN) }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-4 py-2 mt-3 text-white bg-red-500 rounded hover:bg-red-700">Delete</button>
      </form>
    @endif
  </div>
@endsection
