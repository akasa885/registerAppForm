<div class="grid gap-8 lg:grid-cols-2 md:grid-cols-1">
    <div class="lg:order-2 sm:order-1 col-span-1">
        <!--begin::card event mini info-->
        <div class="rounded-lg bg-white shadow-lg dark:bg-neutral-700 mb-5">
            <header class="font-semibold flex justify-center text-center bg-gray-200 text-gray-700 py-5 px-6 sm:py-6 sm:px-8 sm:rounded-t-md">
                Event Info
            </header>
            <!--begin::left sided information-->
            <div class="flex flex-col p-5 sm:p-8">
                <div class="block mb-2">
                    <!--begin::label event title-->
                    <span class="text-sm text-bold font-bold text-gray-700 dark:text-white">
                        {{ __('form_regist.event.title').' :' }}
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
                                {{ __('form_regist.event.event_date').' :' }}
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
                                {{ __('form_regist.event.register_end').' :' }}
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
                                {{ __('form_regist.event.type').' :' }}
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
                                {{ __('form_regist.event.price').' :' }}
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
            <header class="font-semibold flex justify-center text-center bg-gray-200 text-gray-700 py-5 px-6 sm:py-6 sm:px-8 sm:rounded-t-md">
                Featured Image
            </header>
            <!--begin::left sided information-->
            <div class="flex flex-col p-5 sm:p-8">
                <div class="block mb-2">
                    <div class="flex justify-center">
                        <img data-src="{{ $link->banner == null ? asset('/images/default/no-image.png') : $link->banner }}" alt="img-{{ Str::snake($link->title,'-') }}" class="md:h-64 sm:h-56 image-lazy-load" loading="lazy" >
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
        <!--begin::card form register event-->
        <div class="break-words bg-white rounded-lg shadow-lg dark:bg-neutral-700">
            <header class="font-semibold flex justify-center text-center bg-gray-200 text-gray-700 py-5 px-6 sm:py-6 sm:px-8 sm:rounded-t-md">
                Form Register <br> {{$link->title}}
            </header>

            <div class="">

            </div>
            <form class="w-full px-6 mb-5 space-y-6 sm:px-10 sm:space-y-8" method="POST" action="{{ route('form.link.store', ['link' => $link->link_path]) }}">
                @csrf
                <input type="hidden" name="link" value="{{$link->link_path}}">
                @error('message')
                <div class="flex items-center bg-red-500 text-white text-sm font-bold px-4 py-3" role="alert">
                    <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z"/></svg>
                    <p>{{ $message }}</p>
                </div>
                @enderror
                <div class="flex flex-wrap">
                    <label for="input-1" class="block required text-gray-700 text-sm font-bold mb-2 sm:mb-4">
                        {{ __('Full Name') }}:
                    </label>

                    <input id="input-1" type="text"
                        class="form-input w-full @error('fullname') border-red-500 @enderror" name="fullname"
                        value="{{ old('fullname') }}" required autofocus>
                    <span class="text-gray-600 text-xs italic mt-2 w-full">{{ __('form_regist.full_name.help') }}</span><br/>

                    @error('fullname')
                    <p class="text-red-500 text-xs italic mt-2">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="flex flex-wrap">
                    <label for="input-2" class="block required text-gray-700 text-sm font-bold mb-2 sm:mb-4">
                        {{ __('Email Address') }}:
                    </label>

                    <input id="input-2" type="email"
                        class="form-input w-full @error('email') border-red-500 @enderror" name="email"
                        value="{{ old('email') }}" required autofocus>
                        <span class="text-gray-600 text-xs italic mt-2 w-full">{{ __('form_regist.email.help') }}</span><br/>

                    @error('email')
                    <p class="text-red-500 text-xs italic mt-2">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="flex flex-wrap">
                    <label for="input-3" class="block required text-gray-700 text-sm font-bold mb-2 sm:mb-4">
                        {{ __('Phone Number (WhatsApp)') }}:
                    </label>

                    <input id="input-3" type="text"
                        class="form-input w-full @error('no_telpon') border-red-500 @enderror" name="no_telpon"
                        value="{{ old('no_telpon') }}" required autofocus>

                    @error('no_telpon')
                    <p class="text-red-500 text-xs italic mt-4">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="flex flex-wrap">
                    <label for="input-4" class="block required text-gray-700 text-sm font-bold mb-2 sm:mb-4">
                        {{ __('Domicile (City)') }}:
                    </label>

                    <input id="input-4" type="text"
                        class="form-input w-full @error('domisili') border-red-500 @enderror" name="domisili"
                        value="{{ old('domisili') }}" required autofocus>

                    @error('domisili')
                    <p class="text-red-500 text-xs italic mt-4">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="flex flex-wrap">
                    <label for="input-5" class="block required text-gray-700 text-sm font-bold mb-2 sm:mb-4">
                        {{ __('Instance / Company Name') }}:
                    </label>

                    <input id="input-5" type="text"
                        class="form-input w-full @error('instansi') border-red-500 @enderror" name="instansi"
                        value="{{ old('instansi') }}" required autofocus>

                    @error('instansi')
                    <p class="text-red-500 text-xs italic mt-4">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="flex flex-wrap">
                    <button type="submit"
                    class="w-full select-none font-bold whitespace-no-wrap p-3 mb-3 lg:mb-8 md:mb-6 sm:mb-4 rounded-lg text-base leading-normal no-underline text-gray-100 bg-blue-500 hover:bg-blue-700 sm:py-4">
                        {{ __('Submit') }}
                    </button>
                </div>
            </form>
        </div>
        <!--end::card form register event-->
    </div>
</div>
@push('scripts')
<script>
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
</script>
@endpush