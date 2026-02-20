<div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
    <div class="bg-blue-900 px-5 py-4">
        <h2 class="text-lg font-bold text-white text-center">
            {{ __('form_regist.standard_form_title') }}</h2>
        <p class="text-blue-100 text-center text-sm mt-1">{{ $link->title }}</p>
    </div>
    <form class="p-5 space-y-4" method="POST" action="{{ route('form.link.store', ['link' => $link->link_path]) }}">
        @csrf
        <input type="hidden" name="link" value="{{ $link->link_path }}">

        <!--begin::helper text-->
        <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded">
            <p class="text-xs font-medium text-red-800">{{ __('form_regist.main_help') }}</p>
        </div>

        @error('message')
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded-lg flex items-start" role="alert">
                <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="text-xs font-medium">{{ $message }}</span>
            </div>
        @enderror

        <div class="w-full">
            <label for="input-1" class="block text-sm font-medium text-gray-700 mb-1.5">
                {{ __('Full Name (as pelataran account)') }}
                <span class="text-red-500">*</span>
            </label>
            <input id="input-1" type="text"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('fullname') border-red-500 ring-2 ring-red-200 @enderror"
                name="fullname" value="{{ old('fullname') }}" placeholder="Enter your full name" required>
            <p class="text-gray-500 text-sm mt-1">{{ __('form_regist.full_name.help') }}</p>
            @error('fullname')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="w-full">
            <label for="input-2" class="block text-sm font-medium text-gray-700 mb-1.5">
                {{ __('Email Address (used in pelataran account)') }}
                <span class="text-red-500">*</span>
            </label>
            <input id="input-2" type="email"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-500 ring-2 ring-red-200 @enderror"
                name="email" value="{{ old('email') }}" placeholder="your.email@example.com" required>
            <p class="text-gray-500 text-sm mt-1">{{ __('form_regist.email.help') }}</p>
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="w-full">
            <label for="input-3" class="block text-sm font-medium text-gray-700 mb-1.5">
                {{ __('Phone Number (WhatsApp)') }}
                <span class="text-red-500">*</span>
            </label>
            <input id="input-3" type="text"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('no_telpon') border-red-500 ring-2 ring-red-200 @enderror"
                name="no_telpon" value="{{ old('no_telpon') }}" placeholder="08123456789" required>
            @error('no_telpon')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="w-full">
            <label for="input-4" class="block text-sm font-medium text-gray-700 mb-1.5">
                {{ __('Domicile (City)') }}
                <span class="text-red-500">*</span>
            </label>
            @if (isset($selectCities) && count($selectCities) > 0)
                <select name="sel_domisili" id="sel2_domisili"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('sel_domisili') border-red-500 ring-2 ring-red-200 @enderror">
                    @foreach ($selectCities as $key => $item)
                        <option value="{{ $key }}">{{ $item }}</option>
                    @endforeach
                </select>
            @else
                <input id="input-4" type="text"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('domisili') border-red-500 ring-2 ring-red-200 @enderror"
                    name="domisili" value="{{ old('domisili') }}" placeholder="Enter your city" required>
            @endif
            @error('domisili')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="w-full">
            <label for="input-5" class="block text-sm font-medium text-gray-700 mb-1.5">
                {{ __('Instance / Company Name') }}
                <span class="text-red-500">*</span>
            </label>
            <input id="input-5" type="text"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('instansi') border-red-500 ring-2 ring-red-200 @enderror"
                name="instansi" value="{{ old('instansi') }}" placeholder="Your company or institution name" required>
            @error('instansi')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="w-full pt-2">
            <button type="button" id="submit-register"
                class="w-full bg-blue-900 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                {{ __('Submit') }}
            </button>
        </div>
    </form>
</div>
