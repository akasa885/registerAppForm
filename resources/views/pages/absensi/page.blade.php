@extends('layouts.app')
@section('title', 'Absensi ' . $link?->title ?? 'Link Absensi Tidak Tersedia')

@section('content')
    <main class="flex justify-center py-10 sm:container sm:mx-auto">
        <div class="lg:w-6/12 sm:px-6 sm:w-10/12">
            <section class="flex flex-col break-words bg-white sm:border-1 sm:rounded-md sm:shadow-sm sm:shadow-lg">
                @if (!$show)
                    <h3 class="font-semibold p-10 sm:mx-auto">Link Absensi Tidak Tersedia / Sudah ditutup</h3>
                @elseif($message = Session::get('success'))
                    <div class="alert bg-green-100 rounded-lg py-5 px-6 text-base text-green-700 inline-flex items-center w-full alert-dismissible fade show"
                        role="alert">
                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check-circle"
                            class="w-4 h-4 mr-2 fill-current" role="img" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 512 512">
                            <path fill="currentColor"
                                d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z">
                            </path>
                        </svg>
                        {{ $message }}
                        <button type="button"
                            class="btn-close box-content w-4 h-4 p-1 ml-auto text-yellow-900 border-none rounded-none opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-yellow-900 hover:opacity-75 hover:no-underline"
                            data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @else
                    <header
                        class="font-semibold flex justify-center bg-gray-200 text-gray-700 py-5 px-6 sm:py-6 sm:px-8 sm:rounded-t-md">
                        Absen {{ $link->title }}
                    </header>

                    @if (Session::has('errors') || Session::has('info') || Session::has('error'))
                        <div class="px-10 pt-5">
                            <div class="@if (Session::has('errors')) bg-red-100 text-red-700 border-red-400
                        @elseif(Session::has('info'))
                            bg-blue-100 text-blue-700 border-blue-400 @endif border border-red-400 px-4 py-3 rounded relative"
                                role="alert">
                                @if (Session::has('errors') || Session::has('error'))
                                    <strong class="font-bold">Alert:</strong>
                                @else
                                    <strong class="font-bold">Info:</strong>
                                @endif
                                <ul class="list-disc list-inside">
                                    @if (Session::has('errors'))
                                        @foreach ($errors->all() as $message)
                                            <li>{{ $message }}</li>
                                        @endforeach
                                    @elseif (Session::has('error'))
                                        <li>{{ Session::get('error') }}</li>
                                    @elseif (Session::has('info'))
                                        <li>{{ Session::get('info') }}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif
                    <form class="w-full px-6 mb-5 space-y-6 sm:px-10 sm:space-y-8" method="POST"
                        enctype="multipart/form-data" action="{{ route('attend.link', $attendance) }}">
                        @csrf
                        <input type="hidden" name="link" value="{{ $attendance->attendance_path }}">

                        <div class="flex flex-wrap">
                            <label for="input-1" class="block text-gray-700 text-sm font-bold mb-2 sm:mb-4">
                                {{ __('attend.form.full_name') }}
                            </label>

                            <input id="input-1" type="text"
                                class="form-input w-full @error('full_name') border-red-500 @enderror" name="full_name"
                                value="{{ old('full_name') }}" autofocus>
                            <span
                                class="text-gray-600 text-xs mt-2 w-full">{{ __('attend.form.full_name_helper') }}</span><br />

                            @error('full_name')
                                <p class="text-red-500 text-xs italic mt-2">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap">
                            <label for="input-2" class="block text-gray-700 text-sm font-bold mb-2 sm:mb-4 required">
                                {{ __('attend.form.email') }}
                            </label>

                            <input id="input-2" type="email"
                                class="form-input w-full @error('email') border-red-500 @enderror" name="email"
                                value="{{ old('email') }}" required autofocus>
                            <span
                                class="text-gray-600 text-xs mt-2 w-full">{{ __('attend.form.email_helper') }}</span><br />

                            @error('email')
                                <p class="text-red-500 text-xs italic mt-2">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap">
                            <label for="input-3" class="block text-gray-700 text-sm font-bold mb-2 sm:mb-4 required">
                                {{ __('attend.form.phone_number') }}
                            </label>

                            <input id="input-3" type="text"
                                class="form-input w-full @error('no_telpon') border-red-500 @enderror" name="no_telpon"
                                value="{{ old('no_telpon') }}" required autofocus>
                            <span
                                class="text-gray-600 text-xs mt-2 w-full">{{ __('attend.form.phone_helper') }}</span><br />

                            @error('no_telpon')
                                <p class="text-red-500 text-xs italic mt-2">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        @if ($attendance->allow_non_register)
                        <div class="flex flex-wrap">
                            <label for="input-4" class="block text-gray-700 text-sm font-bold mb-2 sm:mb-4">
                                {{ __('Instance / Company Name') }}:
                            </label>
    
                            <input id="input-4" type="text"
                                class="form-input w-full @error('corporation') border-red-500 @enderror" name="corporation"
                                value="{{ old('corporation') }}" required autofocus>
    
                            @error('corporation')
                            <p class="text-red-500 text-xs italic mt-4">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                        @endif

                        @if ($attendance->isCertNeedVerification())
                        <div class="flex flex-wrap">
                            <label for="input-4" class="block w-full text-gray-700 text-sm font-bold mb-2 sm:mb-4">
                                {{ __('attend.form.is_certificate') }} ?
                            </label>

                            <div class="row flex-fill">
                                <label class="block mb-2 font-bold">
                                    <input type="radio" name="is_certificate" value="no" checked
                                        class="mr-2 leading-tight" onchange="toggleUploadForm(this)">
                                    No
                                </label>
                                <label class="block mb-2 font-bold">
                                    <input type="radio" name="is_certificate" id="is_cert_needed" value="yes" class="mr-2 leading-tight"
                                        onchange="toggleUploadForm(this)">
                                    Yes
                                </label>
                            </div>
                        </div>
                        <x-attendance.payment-part-option :attendance="$attendance" />
                        @else
                            <input type="hidden" name="is_certificate" value="no">
                        @endif

                        <div class="flex flex-wrap">
                            <button type="submit"
                                class="w-full select-none font-bold whitespace-no-wrap p-3 rounded-lg text-base leading-normal no-underline text-gray-100 bg-blue-500 hover:bg-blue-700 sm:py-4">
                                {{ __('Submit') }}
                            </button>
                        </div>
                    </form>
                @endif
            </section>
        </div>
    </main>
@endsection


@push('scripts')
    <script>
        function toggleUploadForm(radioButton) {
            const uploadForm = document.getElementById("uploadPay");
            if (radioButton.value === "yes") {
                uploadForm.classList.remove("hidden");
            } else {
                uploadForm.classList.add("hidden");
            }
        }

        $(document).ready(function () {
            // catch is_cert_needed value radio button is yes or no
            var isCertNeeded = $('input[name="is_certificate"]:checked').val();
            if (isCertNeeded === "yes") {
                $('#uploadPay').removeClass('hidden');
            }
        })
    </script>
@endpush
