@php
    $date = date('Y-m-d');
@endphp
@foreach ($link as $item)
    @php
        $open = false;
        $close = false;
        if (
            $date >= date('Y-m-d', strtotime($item->active_from)) &&
            $date <= date('Y-m-d', strtotime($item->active_until))
        ) {
            $open = true;
        } elseif ($date < date('Y-m-d', strtotime($item->active_from))) {
            $open = false;
        } else {
            $close = true;
        }
    @endphp

    <div class="bg-white rounded-lg shadow-md overflow-hidden relative">
        <div class="relative">
            <img class="w-full transition bg-cool-gray-200 duration-300 object-contain h-72 ease-in-out md:h-64 sm:h-56 hover:scale-110 image-lazy-load"
                src="{{ $item->banner == null ? asset('/images/default/no-image.png') : $item->banner }}"
                alt="banner">
            @if ($item->link_type == 'free')
                <span
                    class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">{{ __('Free') }}</span>
            @else
                <span
                    class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">{{ __('Paid') }}</span>
            @endif
            @php
                $styleTag = 'bg-gray-200 text-gray-800';
                $messageTag = 'Not Open Yet';

                if (!$open && !$close) {
                    $styleTag = 'bg-gray-200 text-gray-800';
                } elseif ($open && !$close) {
                    $styleTag = 'bg-green-500 text-white';
                    $messageTag = 'Open';
                } else {
                    $styleTag = 'bg-red-500 text-white';
                    $messageTag = 'Close';
                }
            @endphp
            <div class="absolute top-2 right-2 {{ $styleTag }} text-xs px-2 py-1 rounded">{{ $messageTag }}</div>
        </div>
        <div class="p-4">
            <span class="bg-teal-100 text-teal-500 text-xs px-2 py-1 rounded">Form Registrasi</span>
            <h3 class="mt-2 font-semibold text-lg">{{ Str::limit($item->title, 50) }}</h3>
            @php
                $taglessBody = strip_tags($item->description);
            @endphp
            <p class="text-sm text-gray-500 mt-1">{{ Str::words($taglessBody, 7) }}</p>
            <div class="flex items-center mt-4 space-x-4 text-sm text-gray-600">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M5 2a2 2 0 00-2 2v10a2 2 0 002 2h6a2 2 0 002-2v-1h3.586l-1.293-1.293a1 1 0 011.414-1.414l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L16.586 14H13v1a4 4 0 01-4 4H5a4 4 0 01-4-4V4a4 4 0 014-4h6a4 4 0 014 4v3a1 1 0 11-2 0V4a2 2 0 00-2-2H5z" />
                    </svg>
                    <span>{{ $item->event_date != null ? date('d F Y', strtotime($item->event_date)) : date('d F Y', strtotime($item->active_until)) }}</span>
                </div>
                {{-- <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm2 1v7h12V6H4z"/>
                </svg>
                <span>4 SKP</span>
            </div> --}}
            </div>
            {{-- <div class="flex items-center mt-4">
            <div class="flex items-center text-yellow-500 mr-2">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
                <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
            </div>
            <span class="text-xs text-green-500">Online</span>
        </div> --}}
        <button class="w-full bg-blue-500 text-white py-2 mt-4 rounded-md" onclick="window.location.href= '{{ route('form.link.view', ['link' => $item->link_path]) }}'">{{ __('Register') }}</button>
        </div>
    </div>
@endforeach
