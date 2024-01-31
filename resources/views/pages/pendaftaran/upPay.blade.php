@extends('layouts.app')

@section('content')
    <main class="flex justify-center py-10 sm:container sm:mx-auto">
        <div class="lg:w-6/12 sm:px-6 sm:w-10/12">
            <section class="flex flex-col h-auto break-words bg-white sm:border-1 sm:rounded-md sm:shadow-sm sm:shadow-lg">

                @if ($expired)
                    @include('pages.pendaftaran.pay_confirmation.expired_link')
                @elseif($message = Session::get('success'))
                    <x-front-message-success-alert :message="$message" />
                @elseif($used)
                    @if (config('app.locale') == 'id')
                        <h3 class="font-semibold p-10 sm:mx-auto">Anda telah melakukan pembayaran, silahkan tunggu balasan pada email anda !</h3>
                    @else
                        <h3 class="font-semibold p-10 sm:mx-auto">You have made a payment, please wait for a reply to your email !</h3>
                    @endif
                @elseif($not_found)
                    @include('pages.pendaftaran.pay_confirmation.not_found_token')
                @else
                    <header
                        class="font-semibold flex justify-center bg-gray-200 text-gray-700 py-5 px-6 sm:py-6 sm:px-8 sm:rounded-t-md">
                        Form Bukti Bayar {{ $link->title }} <br />
                    </header>
                    <form class="w-full px-6 mb-5 space-y-6 sm:px-10 sm:space-y-8" method="POST"
                        action="{{ route('form.pay.store', ['payment' => $pay_code]) }}" enctype="multipart/form-data">
                        @csrf
                        @if($message = Session::get('info'))
                        <!-- begin::alert Info -->
                        <div class="alert alert-info bg-blue-100 rounded-lg py-5 px-6  w-full alert-dismissible fade show" role="alert">
                            <svg width="24" height="24" class="alert-icon w-10 h-10"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10"
                                    fill="currentColor" />
                                <rect x="11" y="14" width="7" height="2" rx="1"
                                    transform="rotate(-90 11 14)" fill="currentColor" />
                                <rect x="11" y="17" width="2" height="2" rx="1"
                                    transform="rotate(-90 11 17)" fill="currentColor" />
                            </svg>
                            <span class="ml-4 alert-message">{{ $message }}</span>
                            <button type="button" class="btn-close box-content w-4 h-4 p-1 ml-auto text-yellow-900 border-none rounded-none opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-yellow-900 hover:opacity-75 hover:no-underline" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <!-- end::alert Info -->
                        @endif
                        <!--begin::top rigth timer-->
                        <div class="flex justify-end">
                            <div class="flex flex-row gap-1">
                                <span class="text-gray-600 text-sm">Time Left</span>
                                <span id="time_left_payment" class="text-gray-900 text-sm font-bold">00:00:00</span>
                            </div>
                        </div>
                        <!--end::top rigth timer-->
                        @if (isset($snap_token))
                        @if (config('app.locale') == 'id')
                            <p class="sm:mt-4">Kepada Bpk/Ibu, <strong> {{ $member->full_name }} </strong> </p>
                            <p>
                                Terima kasih telah melakukan pendaftaran {{ $link->title }}.<br> <strong> Silahkan
                                    melakukan pembayaran dengan klik tombol dibawah ini </strong>
                            </p>
                        @else
                            <p class="sm:mt-4">To Mr/Mrs, <strong> {{ $member->full_name }} </strong> </p>
                            <p>
                                Thank you for registering {{ $link->title }}.<br> <strong> Please make a payment by
                                    clicking the button below </strong>
                            </p>
                        @endif
                        <div class="flex flex-wrap">
                            <!-- begin::Action-->
                            <a target="__blank" href="{{ $snap_redirect }}" id="button" class="w-full select-none font-bold whitespace-no-wrap p-3 rounded-lg text-base leading-normal no-underline text-gray-100 bg-blue-500 hover:bg-blue-700 sm:py-4">Continue To Payment</a>
                            <!-- end::Action-->
                        </div>
                        @else
                            @if (config('app.locale') == 'id')
                                <p class="sm:mt-4">Kepada Bpk/Ibu, <strong> {{ $member->full_name }} </strong> </p>
                                <p>
                                    Terima kasih telah melakukan pendaftaran {{ $link->title }}.<br> <strong> Informasi
                                        terkait pembayaran telah kami kirimkan ke email anda </strong>, <br>Silahkan upload
                                    bukti bayar anda pada form dibawah ini.
                                </p>
                            @else
                                <p class="sm:mt-4">To Mr/Mrs, <strong> {{ $member->full_name }} </strong> </p>
                                <p>
                                    Thank you for registering {{ $link->title }}.<br> <strong> Payment information has been
                                        sent to your email </strong>, <br>Please upload your proof of payment in the form
                                    below.
                                </p>
                            @endif
                            <div class="flex">
                                <div class="mb-3">
                                    @if (config('app.locale') == 'id')
                                        <label for="formFile" class="form-label inline-block mb-2 text-gray-700">Silakan
                                            upload file yang diminta :</label>
                                    @else
                                        <label for="formFile" class="form-label inline-block mb-2 text-gray-700">Please
                                            upload the requested file :</label>
                                    @endif
                                    <input
                                        class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                        type="file" name="bukti" accept=".jpeg, .jpg, .png" id="formFile">
                                </div>
                            </div>
                            <div class="flex flex-wrap">
                                <button type="submit"
                                    class="w-full select-none font-bold whitespace-no-wrap p-3 rounded-lg text-base leading-normal no-underline text-gray-100 bg-blue-500 hover:bg-blue-700 sm:py-4">
                                    {{ __('Kirim') }}
                                </button>
                            </div>
                        @endif

                    </form>
                @endif
            </section>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        $('document').ready(function () {
            @if ($timeLeft)
            var timerCounter = () => {
                let time_left = "{{ $timeLeft }}";
                let time_left_payment = document.getElementById('time_left_payment');
                let time_left_array = time_left.split(':');
                let hours = time_left_array[0];
                let minutes = time_left_array[1];
                let seconds = time_left_array[2];
                let x = setInterval(function () {
                    if (seconds > 0) { seconds--; } else { seconds = 59;
                        if (minutes > 0) { minutes--; } else { minutes = 59;
                            if (hours > 0) { hours--; } else { hours = 0; minutes = 0; seconds = 0; clearInterval(x); window.location.reload(); }
                        }
                    }
                    // if seconds < 10 then add 0 before seconds
                    if (seconds < 10) { seconds = '0' + seconds; }
                    time_left_payment.innerHTML = hours + ":" + minutes + ":" + seconds;
                    if (hours == 0 && minutes == 0 && seconds == 0) { clearInterval(x); window.location.reload(); }
                }, 1000);
            }

            // if user current tab is not active then stop timer
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'visible') {
                    timerCounter();
                } else {
                    clearInterval(timerCounter);
                }
            });
            @endif
        })
    </script>
@endpush
