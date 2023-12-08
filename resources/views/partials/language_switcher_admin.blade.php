<div class="dropdown">
    <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        @if (config('app.locale') == 'id')
            Bahasa
        @else
            Language
        @endif
    </button>
    <div class="dropdown-menu" style="min-width: 12rem" aria-labelledby="dropdownMenuButton">
        @foreach ($available_locales as $locale_name => $available_locale)
            @if ($available_locale === $current_locale)
                <h6 class="dropdown-header">{{ $locale_name }}</h6>
            @else
                <a class="dropdown-item" href="{{ route('language.change', ['locale' => $available_locale]) }}">
                    {{ $locale_name }}
                </a>
            @endif
        @endforeach
    </div>
</div>