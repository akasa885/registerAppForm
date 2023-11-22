@extends('layouts.app')
@section('title', 'Pendaftaran '.($link ? $link->title : $title))

@section('content')
<main class="flex justify-center container px-7 sm:px-8 md:px-2 lg:px-0 py-10 sm:container sm:mx-auto">
    <div class=" @if($message = Session::get('success') || isset($notYet) || isset($notFound) || $show) lg:w-6/12 sm:px-6 sm:w-10/12 @endif  ">
        <section class="flex flex-col">
            @if($message = Session::get('success'))
            <div class="break-words bg-white sm:border-1 sm:rounded-md sm:shadow-sm sm:shadow-lg">
                <div class="alert bg-green-100 rounded-lg py-5 px-6 text-base text-green-700 inline-flex items-center w-full alert-dismissible fade show" role="alert">
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check-circle" class="w-4 h-4 mr-2 fill-current" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                      <path fill="currentColor" d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"></path>
                    </svg>
                    {{ $message }}
                    <button type="button" class="btn-close box-content w-4 h-4 p-1 ml-auto text-yellow-900 border-none rounded-none opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-yellow-900 hover:opacity-75 hover:no-underline" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @elseif(isset($notYet))
             <div class="text-center break-words bg-white lg:rounded-lg sm:border-1 sm:rounded-md sm:shadow-sm sm:shadow-lg">
                <h3 class="font-semibold p-10 sm:mx-auto"> Link Register Not Open Yet </h3>
             </div>
            @elseif(isset($notFound))
            <div class="text-center break-words bg-white lg:rounded-lg sm:border-1 sm:rounded-md sm:shadow-sm sm:shadow-lg">
                @include('pages.pendaftaran.not_found_link')
            </div>
            @elseif($show)
            <div class="text-center break-words bg-white lg:rounded-lg sm:border-1 sm:rounded-md sm:shadow-sm sm:shadow-lg">
                <h3 class="font-semibold p-10 sm:mx-auto">Link Register Not Available</h3>
            </div>
            @else
                @include('pages.pendaftaran.form_registration')
            @endif
        </section>
    </div>
</main>
@endsection