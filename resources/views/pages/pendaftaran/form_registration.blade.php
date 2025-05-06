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

<div class="grid gap-8 lg:grid-cols-2 md:grid-cols-1">
    <div class="lg:order-2 sm:order-1 col-span-1">
        <!--begin::card event mini info-->
        <div class="rounded-lg bg-white shadow-lg dark:bg-neutral-700 mb-5">
            <header
                class="font-semibold flex justify-center text-center bg-gray-200 text-gray-700 py-5 px-6 sm:py-6 sm:px-8 sm:rounded-t-md">
                {{ __('form_regist.head.info_block') }}
            </header>
            <!--begin::left sided information-->
            <div class="flex flex-col p-5 sm:p-8">
                <div class="block mb-2">
                    <!--begin::label event title-->
                    <span class="text-sm text-bold font-bold text-gray-700 dark:text-white">
                        {{ __('form_regist.event.title') . ' :' }}
                    </span>
                    <!--end::label event title-->
                    <!--begin::event title-->
                    <span class="text-sm text-gray-500 dark:text-gray-300">
                        {{ $link->title }}
                    </span>
                    <!--end::event title-->
                </div>
                <div class="block mb-2">
                    <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-2">
                        <div class="col-span-1 order-2">
                            <!--begin::label event date-->
                            <span class="text-sm text-bold font-bold text-gray-700 dark:text-white">
                                {{ __('form_regist.event.event_date') . ' :' }}
                            </span>
                            <!--end::label event date-->
                            <!--begin::event date-->
                            <span class="text-sm text-gray-500 dark:text-gray-300">
                                {{ date('d-m-Y', strtotime($link->event_date)) }}
                            </span>
                            <!--end::event date-->
                        </div>
                        <div class="col-span-1 order-1">
                            <!--begin::label register end-->
                            <span class="text-sm text-bold font-bold text-gray-700 dark:text-white">
                                {{ __('form_regist.event.register_end') . ' :' }}
                            </span>
                            <!--end::label register end-->
                            <!--begin::register end-->
                            <span class="text-sm text-gray-500 dark:text-gray-300">
                                {{ date('d-m-Y', strtotime($link->active_until)) }}
                            </span>
                            <!--end::register end-->
                        </div>
                    </div>
                </div>
                <div class="block">
                    <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-2">
                        <div class="col-span-1 order-1">
                            <!--begin::label event type-->
                            <span class="text-sm text-bold font-bold text-gray-700 dark:text-white">
                                {{ __('form_regist.event.type') . ' :' }}
                            </span>
                            <!--end::label event type-->
                            <!--begin::event date-->
                            <span class="text-sm italic text-gray-500 dark:text-gray-300">
                                {{ $link->link_type == 'free' ? __('form_regist.event.free') : __('form_regist.event.paid') }}
                            </span>
                            <!--end::event date-->
                        </div>
                        @if ($link->link_type == 'pay')
                            <div class="col-span-1 order-2">
                                <!--begin::label event price-->
                                <span class="text-sm text-bold font-bold text-gray-700 dark:text-white">
                                    {{ __('form_regist.event.price') . ' :' }}
                                </span>
                                <!--end::label event price-->
                                <!--begin::event price-->
                                <span class="text-sm text-gray-500 dark:text-gray-300">
                                    {{ $link->price_formatted }}
                                </span>
                                <!--end::event price-->
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!--end::left sided information-->
        </div>
        <!--end::card event mini info-->
        <!--begin::card event featured image-->
        <div class="rounded-lg hidden bg-white shadow-lg md:block dark:bg-neutral-700 ">
            <header
                class="font-semibold flex justify-center text-center bg-gray-200 text-gray-700 py-5 px-6 sm:py-6 sm:px-8 sm:rounded-t-md">
                Featured Image
            </header>
            <!--begin::left sided information-->
            <div class="flex flex-col p-5 sm:p-8">
                <div class="block mb-2">
                    <div class="flex justify-center">
                        <img data-src="{{ $link->banner == null ? asset('/images/default/no-image.png') : $link->banner }}"
                            alt="img-{{ Str::snake($link->title, '-') }}" class="md:h-64 sm:h-56 image-lazy-load"
                            loading="lazy">
                    </div>
                </div>
            </div>
            <!--end::left sided information-->
        </div>
        <!--end::card event featured image-->
        <div class="md:hidden block">
            @include('pages.pendaftaran.partials.event-description')
        </div>
    </div>
    <div class="lg:order-1 sm:order-2 col-span-1">
        <div class="md:block hidden">
            @include('pages.pendaftaran.partials.event-description')
        </div>
        @if ($expired_regist)
            <div
                class="text-center break-words bg-white lg:rounded-lg sm:border-1 sm:rounded-md sm:shadow-sm sm:shadow-lg">
                <h3 class="font-semibold p-10 sm:mx-auto"> {{ __('form_regist.alert.close') }} </h3>
            </div>
        @elseif ($isLinkFull)
            <div
                class="text-center break-words bg-white lg:rounded-lg sm:border-1 sm:rounded-md sm:shadow-sm sm:shadow-lg">
                <h3 class="font-semibold p-10 sm:mx-auto"> {{ __('form_regist.alert.quota') }} </h3>
            </div>
        @else
            @if ($link->is_membership_only)
                @include('pages.pendaftaran.partials.membership-form')
            @else
                @include('pages.pendaftaran.partials.regular-form')
            @endif
        @endif
    </div>
</div>
@push('scripts')
    <script>
        $(function() {
            @if (isset($selectCities) && count($selectCities) > 0)
                $('#sel2_domisili').select2({
                    placeholder: '{{ __('Select City') }}',
                    allowClear: true,
                });
            @endif

            let imageOverlayHtml =
                '<div class="absolute inset-0 bg-gray-500 opacity-75 transition duration-300 ease-in-out hover:opacity-0"></div>';
            let imageLoaderHtml =
                '<div class="absolute inset-0 flex justify-center items-center w-full" id="image-loader-animation"><button class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Loading</button></div>';
            $('img').each(function() {
                $(this).wrap('<div class="relative"></div>');
                $(this).after(imageOverlayHtml);
                $(this).after(imageLoaderHtml);
                // iamge is data-src
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
