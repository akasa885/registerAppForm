@extends('layouts.app')
@section('content')
@php
    $date = date("Y-m-d");
@endphp
<div class="p-10 grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-5">
    <!--Card-->
    @foreach ($link as $item)
    <a href="{{route('form.link.view', ['link' => $item->link_path])}}">
        <div class="rounded overflow-hidden shadow-lg">
            <img class="w-full" src="{{ $item->banner == null ? asset('/images/default/no-image.png') : $item->banner }}" alt="banner">
            <div class="px-6 py-4">
              <div class="font-bold text-xl mb-2">{{$item->title}}</div>
              {{-- <p class="text-gray-700 text-base">
                {{$item->description}}
              </p> --}}
            </div>
            <div class="px-6 pt-4 pb-2">
              <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">Open Registration : {{date("d/m/y", strtotime($item->active_from))}}</span>
              <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">Close Registration : {{date("d/m/y", strtotime($item->active_until))}}</span>
              @if( $date >= date("Y-m-d", strtotime($item->active_from)) && $date <= date("Y-m-d", strtotime($item->active_until)) )
              <span class="inline-block bg-green-500 rounded-full px-3 py-1 text-sm font-semibold text-white mr-2 mb-2">#Open</span>
              @elseif($date < date("Y-m-d", strtotime($item->active_from)))
              <span class="inline-block bg-yellow-500 rounded-full px-3 py-1 text-sm font-semibold text-white mr-2 mb-2">#Not Open Yet</span>
              @else
              <span class="inline-block bg-red-500 rounded-full px-3 py-1 text-sm font-semibold text-white mr-2 mb-2">#Close</span>
              @endif
            </div>
        </div>
    </a>
    @endforeach
  </div>
</div>
@endsection