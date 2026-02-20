@push('stylesUp')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* select2 up style */
        .select2-container .select2-selection--single {
            height: 2.5rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #000;
            line-height: 1.5;
            padding: .375rem .75rem;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 1.5rem;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: 1px;
            width: 20px;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear {
            height: 1.5rem;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: 1px;
            width: 20px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #000 transparent transparent transparent;
            border-style: solid;
            border-width: 5px 4px 0 4px;
            height: 0;
            left: 50%;
            margin-left: -4px;
            margin-top: -2px;
            position: absolute;
            top: 50%;
            width: 0;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
<div class="max-w-7xl mx-auto">
    <div class="grid gap-4 md:gap-6 lg:grid-cols-2">
        <!-- Main Form Section -->
        <div class="lg:order-1 order-2 space-y-4">
            <!-- Event Description -->
            @include('pages.pendaftaran.partials.event-description')

            @if ($expired_regist)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-8 text-center">
                        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-100 mb-4">
                            <svg class="h-7 w-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('form_regist.alert.close') }}</h3>
                    </div>
                </div>
            @elseif ($isLinkFull)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-8 text-center">
                        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-yellow-100 mb-4">
                            <svg class="h-7 w-7 text-yellow-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('form_regist.alert.quota') }}</h3>
                    </div>
                </div>
            @else
                @if ($link->is_membership_only)
                    <!--begin::membership only form-->
                    @include('pages.pendaftaran.partials.membership-form')
                    <!--end::membership only form-->
                @else
                    <!--begin::standard registration form-->
                    @include('pages.pendaftaran.partials.regular-form')
                    <!--end::standard registration form-->
                @endif
            @endif
        </div>

        <!-- Event Info Sidebar -->
        <div class="lg:order-2 order-1 lg:sticky lg:top-6 space-y-4 h-fit">
            <!--begin::card event info-->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                <div class="bg-blue-900 px-5 py-3">
                    <h3 class="text-base font-semibold text-white">{{ __('form_regist.head.info_block') }}</h3>
                </div>
                <div class="p-5 space-y-4">
                    <!-- Event Title -->
                    <div class="border-l-4 border-blue-500 pl-3">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">
                            {{ __('form_regist.event.title') }}</p>
                        <p class="text-sm font-semibold text-gray-900 text-dark leading-snug">{{ $link->title }}</p>
                    </div>

                    <!-- Event Dates -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-gray-500 mb-1">{{ __('form_regist.event.event_date') }}
                            </p>
                            <p class="text-sm font-semibold text-gray-900 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span class="text-xs">{{ date('d-m-Y', strtotime($link->event_date)) }}</span>
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-gray-500 mb-1">
                                {{ __('form_regist.event.register_end') }}</p>
                            <p class="text-sm font-semibold text-gray-900 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs">{{ date('d-m-Y', strtotime($link->active_until)) }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Event Type & Price -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1.5">{{ __('form_regist.event.type') }}</p>
                            @if ($link->link_type == 'free')
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('form_regist.event.free') }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z">
                                        </path>
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('form_regist.event.paid') }}
                                </span>
                            @endif
                        </div>
                        @if ($link->link_type == 'pay')
                            <div class="text-right">
                                <p class="text-xs font-medium text-gray-500 mb-1">{{ __('form_regist.event.price') }}
                                </p>
                                <p class="text-lg font-bold text-blue-600">{{ $link->price_formatted }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!--end::card event info-->
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(function() {
            $('#sel2_domisili').select2({
                placeholder: '{{ __('Select City') }}',
                allowClear: true,
            });

            let imageOverlayHtml =
                '<div class="absolute inset-0 bg-gray-500 opacity-75 transition duration-300 ease-in-out hover:opacity-0"></div>';
            let imageLoaderHtml =
                '<div class="absolute inset-0 flex justify-center items-center w-full" id="image-loader-animation"><button class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Loading</button></div>';
            $('img').each(function() {
                // check is it has data-src attribute
                if ($(this).attr('data-src') == undefined) {
                    return;
                }

                $(this).wrap('<div class="relative"></div>');
                $(this).after(imageOverlayHtml);
                $(this).after(imageLoaderHtml);
                $(this).attr('src', $(this).attr('data-src'));
                $(this).removeAttr('data-src');
                $(this).on('load', function() {
                    $(this).next().remove();
                    $(this).next().remove();
                });

                $(this).on('error', function() {
                    $(this).next().remove();
                    $(this).next().remove();
                });
            });
        });
        @php
            $pageRegHelp = [
                'warn_txt' => __('form_regist.alert.warn_confirmation'),
                'submit_txt' => __('Submit'),
                'event_txt' => __('form_regist.alert.warn_subTxt', ['event' => Str::limit($link->title, 40)]),
                'swal_ok' => __('Yes'),
                'swal_cancel' => __('Cancel'),
                'err_filled' => __('form_regist.alert.err_filled'),
                'end_site' => \App\Models\Link::ENDPOINTMEMBERSHIP,
            ];
        @endphp
        const pageRegHelper = @json($pageRegHelp);
        window.pageRegHelper = pageRegHelper;
    </script>
    <script defer id="front_script" src="{{ mix('js/front.js') }}" data-page="form_reg" data-page-script="true"></script>
@endpush
