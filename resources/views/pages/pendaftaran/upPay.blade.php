@extends('layouts.app')

@section('content')
<main class="flex justify-center py-10 sm:container sm:mx-auto">
    <div class="lg:w-6/12 sm:px-6 sm:w-10/12">
        <section class="flex flex-col break-words bg-white sm:border-1 sm:rounded-md sm:shadow-sm sm:shadow-lg">

            @if($expire)
                <h3 class="font-semibold p-10 sm:mx-auto">Link Pembayaran Telah Expired</h3>
            @elseif($message = Session::get('success'))
            <div class="alert bg-green-100 rounded-lg py-5 px-6 text-base text-green-700 inline-flex items-center w-full alert-dismissible fade show" role="alert">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check-circle" class="w-4 h-4 mr-2 fill-current" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                  <path fill="currentColor" d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"></path>
                </svg>
                {{ $message }}
                <button type="button" class="btn-close box-content w-4 h-4 p-1 ml-auto text-yellow-900 border-none rounded-none opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-yellow-900 hover:opacity-75 hover:no-underline" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @else
                <header class="font-semibold flex justify-center bg-gray-200 text-gray-700 py-5 px-6 sm:py-6 sm:px-8 sm:rounded-t-md">
                    Form Bukti Bayar {{$link->title}} <br/>
                </header>
                <form class="w-full px-6 mb-5 space-y-6 sm:px-10 sm:space-y-8" method="POST" action="{{ route('form.pay.store', ['payment' => $pay_code]) }}" enctype="multipart/form-data">
                    @csrf
                    <p class="sm:mt-4">Kepada Bpk/Ibu, <strong> {{$member->full_name}} </strong> </p>
                    <p>
                        Terima kasih telah melakukan pendaftaran {{$link->title}}. Silahkan upload bukti bayar anda pada form dibawah ini.
                    </p>
                    <div class="flex">
                        <div class="mb-3">
                          <label for="formFile" class="form-label inline-block mb-2 text-gray-700">Silakan upload file bukti bayar anda :</label>
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