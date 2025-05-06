<!--begin::card form register event-->
<div class="break-words bg-white rounded-lg shadow-lg dark:bg-neutral-700">
    <header
        class="font-semibold flex justify-center text-center bg-gray-200 text-gray-700 py-5 px-6 sm:py-6 sm:px-8 sm:rounded-t-md">
        Form Register <br> {{ $link->title }}
    </header>

    <div class="">

    </div>
    <form class="w-full px-6 mb-5 space-y-6 sm:px-10 sm:space-y-8" method="POST"
        action="{{ route('form.link.store', ['link' => $link->link_path]) }}">
        @csrf
        <input type="hidden" name="link" value="{{ $link->link_path }}">
        <div class="text-center text-blue-700 text-sm font-bold">
            <p>{{ __('form_regist.main_help') }}</p>
        </div>
        @error('message')
            <div class="flex items-center bg-red-500 text-white text-sm font-bold px-4 py-3" role="alert">
                <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path
                        d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z" />
                </svg>
                <p>{{ $message }}</p>
            </div>
        @enderror
        <div class="flex flex-wrap">
            <label for="input-1" class="block required text-gray-700 text-sm font-bold mb-2 sm:mb-4">
                {{ __('Full Name') }}:
            </label>

            <input id="input-1" type="text" class="form-input w-full @error('fullname') border-red-500 @enderror"
                name="fullname" value="{{ old('fullname') }}" required autofocus>
            <span class="text-gray-600 text-xs italic mt-2 w-full">{{ __('form_regist.full_name.help') }}</span><br />

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

            <input id="input-2" type="email" class="form-input w-full @error('email') border-red-500 @enderror"
                name="email" value="{{ old('email') }}" required autofocus>
            <span class="text-gray-600 text-xs italic mt-2 w-full">{{ __('form_regist.email.help') }}</span><br />

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

            <input id="input-3" type="text" class="form-input w-full @error('no_telpon') border-red-500 @enderror"
                name="no_telpon" value="{{ old('no_telpon') }}" required autofocus>

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

            @if (isset($selectCities) && count($selectCities) > 0)
                <select name="sel_domisili" id="sel2_domisili"
                    class="form-control w-full form-select @error('sel_domisili') border-red-500 @enderror">
                    @foreach ($selectCities as $key => $item)
                        <option value="{{ $key }}">{{ $item }}</option>
                    @endforeach
                </select>
            @else
                <input id="input-4" type="text"
                    class="form-input w-full @error('domisili') border-red-500 @enderror" name="domisili"
                    value="{{ old('domisili') }}" required>
            @endif

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

            <input id="input-5" type="text" class="form-input w-full @error('instansi') border-red-500 @enderror"
                name="instansi" value="{{ old('instansi') }}" required autofocus>

            @error('instansi')
                <p class="text-red-500 text-xs italic mt-4">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="flex flex-wrap">
            <button type="button" id="submit-register"
                class="w-full select-none font-bold whitespace-no-wrap p-3 mb-3 lg:mb-8 md:mb-6 sm:mb-4 rounded-lg text-base leading-normal no-underline text-gray-100 bg-blue-500 hover:bg-blue-700 sm:py-4">
                {{ __('Submit') }}
            </button>
        </div>
    </form>
</div>
<!--end::card form register event-->
