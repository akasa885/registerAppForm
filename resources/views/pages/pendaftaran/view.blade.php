@extends('layouts.app')

@section('content')
<main class="flex justify-center py-10 sm:container sm:mx-auto">
    <div class="lg:w-6/12 sm:px-6 sm:w-10/12">
        <section class="flex flex-col break-words bg-white sm:border-1 sm:rounded-md sm:shadow-sm sm:shadow-lg">

            @if($show)
                <h3 class="font-semibold p-10 sm:mx-auto">Link Pendaftaran Tidak Tersedia</h3>
            @else
                <header class="font-semibold flex justify-center bg-gray-200 text-gray-700 py-5 px-6 sm:py-6 sm:px-8 sm:rounded-t-md">
                    Form Pendaftaran {{$link->title}}
                </header>

                <div class="">

                </div>
                <form class="w-full px-6 mb-5 space-y-6 sm:px-10 sm:space-y-8" method="POST" action="{{ route('form.link.store', ['link' => $link->link_path]) }}">
                    @csrf
                    <input type="hidden" name="link" value="{{$link->link_path}}">
                    <div class="flex flex-wrap">
                        <label for="input-1" class="block text-gray-700 text-sm font-bold mb-2 sm:mb-4">
                            {{ __('Nama Lengkap') }}:
                        </label>

                        <input id="input-1" type="text"
                            class="form-input w-full @error('fullname') border-red-500 @enderror" name="fullname"
                            value="{{ old('fullname') }}" required autofocus>

                        @error('fullname')
                        <p class="text-red-500 text-xs italic mt-4">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap">
                        <label for="input-2" class="block text-gray-700 text-sm font-bold mb-2 sm:mb-4">
                            {{ __('Alamat Email') }}:
                        </label>

                        <input id="input-2" type="email"
                            class="form-input w-full @error('email') border-red-500 @enderror" name="email"
                            value="{{ old('email') }}" required autofocus>

                        @error('email')
                        <p class="text-red-500 text-xs italic mt-4">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap">
                        <label for="input-3" class="block text-gray-700 text-sm font-bold mb-2 sm:mb-4">
                            {{ __('Nomor Telpon (WA)') }}:
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
                        <label for="input-4" class="block text-gray-700 text-sm font-bold mb-2 sm:mb-4">
                            {{ __('Instansi') }}:
                        </label>

                        <input id="input-4" type="text"
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
                        class="w-full select-none font-bold whitespace-no-wrap p-3 rounded-lg text-base leading-normal no-underline text-gray-100 bg-blue-500 hover:bg-blue-700 sm:py-4">
                            {{ __('Kirim') }}
                        </button>
                    </div>
                </form>
            @endif
        </section>
    </div>
</main>
@endsection