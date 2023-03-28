@extends('layouts.app')
@section('content')
    @php
        $date = date('Y-m-d');
    @endphp
    <div class="p-10 grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-5">
        <!--Card-->
        @foreach ($link as $item)
            <a href="{{ route('form.link.view', ['link' => $item->link_path]) }}">
                <div class="rounded overflow-hidden shadow-lg">
                    <img class="w-full transition bg-cool-gray-200 duration-300 object-contain h-72 ease-in-out md:h-64 sm:h-56 hover:scale-110"
                        src="{{ $item->banner == null ? asset('/images/default/no-image.png') : $item->banner }}"
                        alt="banner">
                    {{-- <img class="object-contain h-96 w-full bg-cool-gray-200 hover:h-75" src="{{ $item->banner == null ? asset('/images/default/no-image.png') : $item->banner }}" alt="banner"> --}}
                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2">{{ $item->title }}</div>
                        {{-- <p class="text-gray-700 text-base">
                {{$item->description}}
              </p> --}}
                    </div>
                    <div class="px-6 pt-4 pb-2">
                        <div class="grid grid-rows-2 grid-flow-col gap-1">
                            <span
                                class="row-span-1 w-50 inline-block bg-gray-200 rounded-full px-3 py-1 text-xs font-semibold text-gray-700 mr-2 mb-2">Open
                                Registration : {{ date('d/m/y', strtotime($item->active_from)) }}</span>
                            <span
                                class="row-span-1 w-50 inline-block bg-gray-200 rounded-full px-3 py-1 text-xs font-semibold text-gray-700 mr-2 mb-2">Close
                                Registration : {{ date('d/m/y', strtotime($item->active_until)) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <div class="col-auto">
                                @if ($date >= date('Y-m-d', strtotime($item->active_from)) && $date <= date('Y-m-d', strtotime($item->active_until)))
                                    <span
                                        class="inline-block bg-green-500 rounded-full px-3 py-1 text-sm font-semibold text-white mr-2 mb-2">#Open</span>
                                @elseif($date < date('Y-m-d', strtotime($item->active_from)))
                                    <span
                                        class="inline-block bg-yellow-500 rounded-full px-3 py-1 text-sm font-semibold text-white mr-2 mb-2">#Not
                                        Open Yet</span>
                                @else
                                    <span
                                        class="inline-block bg-red-500 rounded-full px-3 py-1 text-sm font-semibold text-white mr-2 mb-2">#Close</span>
                                @endif
                            </div>
                            <div class="col">
                                @if ($item->link_type == 'free')
                                    <span
                                        class="inline-block bg-yellow-500 rounded-full px-3 py-1 text-sm font-semibold text-white mr-2 mb-2">Free</span>
                                @elseif($item->link_type == 'pay')
                                    <span
                                        class="inline-block bg-blue-500 rounded-full px-3 py-1 text-sm font-semibold text-white mr-2 mb-2">Paid</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    </div>
@endsection
