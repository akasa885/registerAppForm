@php
    $date = date('Y-m-d');
@endphp
@foreach ($link as $item)
    @php
        $open = false;
        $close = false;
        if ($date >= date('Y-m-d', strtotime($item->active_from)) && $date <= date('Y-m-d', strtotime($item->active_until))) {
            $open = true;
        } elseif ($date < date('Y-m-d', strtotime($item->active_from))) {
            $open = false;
        } else {
            $close = true;
        }
    @endphp

    <div class="flex-1 p-4">
        <a href="{{ route('form.link.view', ['link' => $item->link_path]) }}">
            <div class="flex flex-col h-full bg-white rounded-lg shadow-lg">
                <div class="border-b border-gray-200">
                    <img class="w-full transition bg-cool-gray-200 duration-300 object-contain h-72 ease-in-out md:h-64 sm:h-56 hover:scale-110"
                        src="{{ $item->banner == null ? asset('/images/default/no-image.png') : $item->banner }}"
                        alt="banner">
                </div>
                <div class="px-4 py-2 flex-grow">
                    <div class="font-bold text-xl mb-2">
                        {{ $item->title }}
                    </div>
                    {{-- <div class="text-gray-700 text-sm mb-2">
                                        {!! $item->description !!}
                                    </div> --}}
                    <div class="grid grid-rows-2 grid-flow-col gap-1">
                        @if (!$open && !$close)
                            <span
                                class="row-span-1 w-50 inline-block bg-gray-200 rounded-full px-3 py-1 text-xs font-semibold text-gray-700 mr-2 mb-2">Open
                                Registration : {{ date('d/m/y', strtotime($item->active_from)) }}</span>
                        @elseif($open && !$close)
                            <span
                                class="row-span-1 w-50 inline-block bg-gray-200 rounded-full px-3 py-1 text-xs font-semibold text-gray-700 mr-2 mb-2">Close
                                Registration : {{ date('d/m/y', strtotime($item->active_until)) }}</span>
                        @else
                            <span
                                class="row-span-1 w-50 inline-block bg-gray-200 rounded-full px-3 py-1 text-xs font-semibold text-gray-700 mr-2 mb-2">Close
                                Registration : {{ date('d/m/y', strtotime($item->active_until)) }}</span>
                        @endif
                    </div>
                </div>
                <div class="px-4 py-2 mt-auto border-t border-gray-200">
                    <div class="flex justify-between px-4 py-2 border-t border-gray-200">
                        <div class="col-auto">
                            @if ($open && !$close)
                                <span
                                    class="inline-block bg-green-500 rounded-full px-3 py-1 text-sm font-semibold text-white mr-2 mb-2">#Open</span>
                            @elseif(!$open && !$close)
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
    </div>
@endforeach
