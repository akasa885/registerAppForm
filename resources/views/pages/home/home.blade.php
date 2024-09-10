@extends('layouts.app')
@section('content')
    @php
        $date = date('Y-m-d');
    @endphp
    <div class="container-fluid mx-auto p-6 @if($link->count() == 0) h-full @endif">
        <div class="flex items-stretch -mx-4 flex-col">
            @if ($link->count() == 0)
                <div class="grid cols-1"  style="height: calc(70vh + 48px)">
                    <div class="col-span-full text-center">
                        <div class="flex justify-center items-center w-full">
                            <span class="text-gray-500"> No Data Found </span>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-10 grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5" id="list-wrapper">
                    @foreach ($link as $item)
                        @if ($item->isLinkViewable() == false)
                            @continue
                        @endif
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
                        <div class="bg-white rounded-lg shadow-md overflow-hidden relative">
                            <div class="relative">
                                <img class="w-full transition bg-cool-gray-200 duration-300 object-contain h-72 ease-in-out md:h-64 sm:h-56 hover:scale-110 image-lazy-load"
                                    data-src="{{ $item->banner == null ? asset('/images/default/no-image.png') : $item->banner }}"
                                    loading="lazy"
                                    alt="banner">
                                @if($item->link_type == 'free')
                                    <span class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">{{ __('Free') }}</span>
                                @else
                                    <span class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">{{ __('Paid') }}</span>
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
                                            <path d="M5 2a2 2 0 00-2 2v10a2 2 0 002 2h6a2 2 0 002-2v-1h3.586l-1.293-1.293a1 1 0 011.414-1.414l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L16.586 14H13v1a4 4 0 01-4 4H5a4 4 0 01-4-4V4a4 4 0 014-4h6a4 4 0 014 4v3a1 1 0 11-2 0V4a2 2 0 00-2-2H5z"/>
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
                                <button class="w-full bg-blue-500 text-white py-2 mt-4 rounded-md">{{ __('Register') }}</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            @if ($link->hasPages())
                <div class="grid cols-1">
                    <div class="col-span-full text-center">
                        <!--begin::data loader loading-->
                        <div class="flex justify-center items-center w-full hidden" id="item-loader-animation">
                            <button
                                class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                                    </path>
                                </svg>
                                Loading
                            </button>
                        </div>
                        <!--end::data loader loading-->
                        <!--begin::data loader button-->
                        <button type="button"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full"
                            id="load_more_button">
                            Load More
                        </button>
                        <!--end::data loader button-->
                        <!--begin::data loader no more-->
                        <div class="flex justify-center items-center w-full hidden" id="item-loader-no-more">
                            <span class="text-gray-500"> All Data Loaded </span>
                        </div>
                        <!--end::data loader no more-->
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let linkListComponent = function () {
            let page = 1;
            let ENDPOINT = "{{ route('home') }}";

            let loadMore = function () {
                $('#load_more_button').on('click', function () {
                    page++;
                    loadMoreData(page);
                });
            }

            let loadMoreData = function (page) {
                $.ajax({
                    url: ENDPOINT+"?page="+page,
                    type: "GET",
                    beforeSend: function () {
                        $('#item-loader-animation').removeClass('hidden');
                        $('#load_more_button').addClass('hidden');
                        $('#item-loader-no-more').addClass('hidden');
                    },
                    success: function (response) {
                        setTimeout(function () {
                            $('#item-loader-animation').addClass('hidden');
                            $('#load_more_button').removeClass('hidden');
                            $('#item-loader-no-more').addClass('hidden');

                            if (response.html == "") {
                                $('#load_more_button').addClass('hidden');
                                $('#item-loader-no-more').removeClass('hidden');
                            } else {
                                $('#load_more_button').removeClass('hidden');
                                $('#item-loader-no-more').addClass('hidden');
                            }

                            $('#list-wrapper').append(response.html);
                        }, 1000);
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }

            return {
                init: function () {
                    loadMore();
                }
            }
        }();

        $(function () {
            let imageOverlayHtml = '<div class="absolute inset-0 bg-gray-500 opacity-75 transition duration-300 ease-in-out hover:opacity-0"></div>';
            let imageLoaderHtml = '<div class="absolute inset-0 flex justify-center items-center w-full" id="image-loader-animation"><button class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Loading</button></div>';
            $('img').each(function(){ 
                $(this).wrap('<div class="relative"></div>');
                $(this).after(imageOverlayHtml);
                $(this).after(imageLoaderHtml);
                // iamge is data-src
                $(this).attr('src', $(this).attr('data-src'));
                $(this).removeAttr('data-src');
                $(this).on('load', function(){
                    $(this).next().remove();
                    $(this).next().remove();
                });

                $(this).on('error', function(){
                    $(this).next().remove();
                    $(this).next().remove();
                });
            });
        });

        $(document).ready(function () {
            linkListComponent.init();
        });
    </script>
@endpush
