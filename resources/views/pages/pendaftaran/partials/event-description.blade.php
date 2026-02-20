<!--begin::card form description-->
<div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden mb-6 w-full">
    <!-- Featured Image with Overlay -->
    <div class="relative aspect-[16/9] overflow-hidden">
        <img src="{{ $link->banner == null ? asset('/images/default/no-image.png') : $link->banner }}"
            alt="img-{{ Str::snake($link->title, '-') }}" class="object-cover w-full h-full" loading="lazy">
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 to-black/60"></div>
        <div class="absolute bottom-0 left-0 right-0 p-4">
            <h3 class="text-lg font-bold text-white drop-shadow-lg">{{ $link->title }}</h3>
        </div>
    </div>

    <!-- Description Header -->
    <div class="bg-blue-900 px-5 py-3">
        <h3 class="text-base font-semibold text-white">{{ __('form_regist.head.desc_block') }}</h3>
    </div>

    <!-- Description Content -->
    <div class="p-5">
        <div class="prose prose-sm max-w-none text-gray-700 dark:text-gray-300 text-sm html-description-content">
            {!! $link->description !!}
        </div>
    </div>
</div>
<!--end::card form description-->
