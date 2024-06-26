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
                        <div class="flex-1 p-4">
                            <a href="{{ route('form.link.view', ['link' => $item->link_path]) }}">
                                <div class="flex flex-col h-full bg-white rounded-lg shadow-lg">
                                    <div class="border-b border-gray-200">
                                        <img class="w-full transition bg-cool-gray-200 duration-300 object-contain h-72 ease-in-out md:h-64 sm:h-56 hover:scale-110 image-lazy-load"
                                            data-src="{{ $item->banner == null ? asset('/images/default/no-image.png') : $item->banner }}"
                                            loading="lazy"
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
