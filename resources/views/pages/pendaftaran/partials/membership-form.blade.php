<!--begin::membership only form-->
<!--begin::card form register event-->
<div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
    <div class="bg-blue-900 px-5 py-4">
        <h2 class="text-lg font-bold text-white text-center">
            {{ __('form_regist.membership_form_title') }}
        </h2>
        <p class="text-blue-100 text-center text-sm mt-1">{{ $link->title }}</p>
    </div>
    <form class="p-5 space-y-4" method="POST" action="{{ route('form.link.store', ['link' => $link->link_path]) }}">
        @csrf
        <input type="hidden" name="link" value="{{ $link->link_path }}">
        <input type="hidden" name="membership_status" value="0">

        <!--begin::helper text-->
        <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded">
            <p class="text-xs font-medium text-red-800">{{ __('form_regist.member_help') }}</p>
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
                {{ __('Membership Registration Number') }}
                <span class="text-red-500">*</span>
            </label>
            <input id="input-1" type="text"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('registration_number') border-red-500 ring-2 ring-red-200 @enderror"
                name="registration_number" value="{{ old('registration_number') }}" required>
            @error('registration_number')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="w-full">
            <label for="input-2" class="block text-sm font-medium text-gray-700 mb-1.5">
                {{ __('Email Address (used in membership account)') }}
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
            <label for="status-member" class="block text-sm font-medium text-gray-700 mb-1.5">
                {{ __('Membership Status') }}
            </label>
            <span id="status-member"
                class="inline-block text-sm font-medium text-blue-800 bg-blue-50 px-3 py-1 rounded">{{ __('Unverified') }}</span>
        </div>

        <div class="w-full pt-2 flex gap-3">
            <button type="button" id="submit_check_member"
                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow transition-all duration-200 text-sm">
                {{ __('Check Membership') }}
            </button>
            <button type="button" id="submit-register"
                class="flex-1 bg-blue-900 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                {{ __('Submit') }}
            </button>
        </div>
    </form>
</div>
<!--end::card form register event-->
<!--end::membership only form-->
