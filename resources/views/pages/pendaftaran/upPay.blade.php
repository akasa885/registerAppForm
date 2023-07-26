@extends('layouts.app')

@section('content')
<main class="flex justify-center py-10 sm:container sm:mx-auto">
    <div class="lg:w-6/12 sm:px-6 sm:w-10/12">
        <section class="flex flex-col h-auto break-words bg-white sm:border-1 sm:rounded-md sm:shadow-sm sm:shadow-lg">

            @if($expired)
                @include('pages.pendaftaran.pay_confirmation.expired_link')
            @elseif($message = Session::get('success'))
                <x-front-message-success-alert :message="$message" />
            @elseif($used)
                @if (config('app.locale') == 'id')
                    <h3 class="font-semibold p-10 sm:mx-auto">Anda telah upload bukti bayar, silahkan tunggu balasan konfirmasi di email anda !</h3>
                @else
                    <h3 class="font-semibold p-10 sm:mx-auto">You have uploaded proof of payment, please wait for a confirmation reply in your email !</h3>
                @endif
            @elseif($not_found)
                @include('pages.pendaftaran.pay_confirmation.not_found_token')
            @else
                <header class="font-semibold flex justify-center bg-gray-200 text-gray-700 py-5 px-6 sm:py-6 sm:px-8 sm:rounded-t-md">
                    Form Bukti Bayar {{$link->title}} <br/>
                </header>
                <form class="w-full px-6 mb-5 space-y-6 sm:px-10 sm:space-y-8" method="POST" action="{{ route('form.pay.store', ['payment' => $pay_code]) }}" enctype="multipart/form-data">
                    @csrf
                    @if (config('app.locale') == 'id')
                    <p class="sm:mt-4">Kepada Bpk/Ibu, <strong> {{$member->full_name}} </strong> </p>
                    <p>
                        Terima kasih telah melakukan pendaftaran {{$link->title}}.<br> <strong> Informasi terkait pembayaran telah kami kirimkan ke email anda </strong>, <br>Silahkan upload bukti bayar anda pada form dibawah ini.
                    </p>
                    @else
                    <p class="sm:mt-4">To Mr/Mrs, <strong> {{$member->full_name}} </strong> </p>
                    <p>
                        Thank you for registering {{$link->title}}.<br> <strong> Payment information has been sent to your email </strong>, <br>Please upload your proof of payment in the form below.
                    </p>
                    @endif
                    <div class="flex">
                        <div class="mb-3">
                          @if (config('app.locale') == 'id')
                            <label for="formFile" class="form-label inline-block mb-2 text-gray-700">Silakan upload file yang diminta :</label>
                          @else
                            <label for="formFile" class="form-label inline-block mb-2 text-gray-700">Please upload the requested file :</label>
                          @endif
                          <input class="form-control
                          block
                          w-full
                          px-3
                          py-1.5
                          text-base
                          font-normal
                          text-gray-700
                          bg-white bg-clip-padding
                          border border-solid border-gray-300
                          rounded
                          transition
                          ease-in-out
                          m-0
                          focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" type="file" name="bukti" accept=".jpeg, .jpg, .png" id="formFile">
                        </div>
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