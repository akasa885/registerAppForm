<div class="dropdown inline-block relative">
    <button
        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        <span class="mr-1">
            @if (config('app.locale') == 'id')
                Bahasa
            @else
                Language
            @endif
        </span>
        <svg class="w-2.5 h-2.5 ms-3 mx-1 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 4 4 4-4" />
        </svg>
    </button>
    <ul class="dropdown-menu absolute hidden text-gray-700 pt-1 bg-blue-800 rounded-b right-0">
        @foreach ($available_locales as $locale_name => $available_locale)
            <li class="w-100 px-5 hover:bg-blue-400">
                @if ($available_locale === $current_locale)
                    <span class="block px-4 py-2 text-center text-white">{{ $locale_name }}</span>
                @else
                    <a class="block ml-1 py-2 text-white underline text-center dark:hover:bg-gray-600 dark:hover:text-white"
                        href="{{ route('language.change', ['locale' => $available_locale]) }}">
                        <span>{{ $locale_name }}</span>
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
</div>
