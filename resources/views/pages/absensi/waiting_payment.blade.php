@extends('layouts.app')
@section('title', $title)

@section('content')
    <main class="flex justify-center py-10 sm:container sm:mx-auto">
        <div class="lg:w-6/12 sm:px-6 sm:w-10/12">
            <section class="flex flex-col break-words bg-white sm:border-1 sm:rounded-md md:shadow-sm shadow-lg">
                <!--begin::view information card-->
                    <!--begin::header-->
                    <div class="flex flex-col justify-around items-center p-6 items-center">
                        <span class="text-2xl font-bold text-gray-900 mb-1">Waiting for Payment</span>
                        <!--begin::alert text : don't close the page-->
                        <span class="text-red-500 text-xs font-bold italic">Please Don't close or leave this page before you complete the payment</span>
                        {{-- <span class="text-gray-600 text-sm">Time Left <span class="text-red-500 font-bold">{{ '20:00' }}</span></span> --}}
                    </div>
                    <!--end::header-->
                    <!--begin::body-->
                    <div class="grid grid-cols-1 sm:grid-cols-2 px-6 py-2 gap-2">
                        <div class="col-1 flex flex-col">
                            <span class="text-gray-600 text-sm">Total Payment</span>
                            <span class="text-gray-900 text-lg font-bold">{{ $attendance->formatted_price_certificate }}</span>
                        </div>
                        <div class="col-2 flex flex-col text-left sm:text-right">
                            <span class="text-gray-600 text-sm">Customer Name</span>
                            <span class="text-gray-900 text-lg font-bold">{{ $order->member->full_name }}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 px-6 py-2 gap-2">
                        <div class="col-1 flex flex-col">
                            <span class="text-gray-600 text-sm">Payment For</span>
                            <span class="text-gray-900 text-lg font-bold">Certificate</span>
                        </div>
                        <div class="col-2 flex flex-col text-left sm:text-right">
                            <span class="text-gray-600 text-sm">Time Left</span>
                            <span id="time_left_payment" class="text-gray-900 text-lg font-bold">00:00:00</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 px-6 py-2">
                        <div class="col-1 flex flex-col">
                            <span class="text-gray-600 text-sm">Event Name</span>
                            <span class="text-gray-900 text-lg font-bold">{{ ucwords($attendance->link->title) }}</span>
                        </div>
                    </div>
                    <!--end::body-->
                    <!--begin::footer-->
                    <!--begin::link to payment-->
                    <div class="flex justify-center items-center p-6">
                        <a href="javascript:void();" id="redirect_to_payment_page" data-redirect="{{ !$finished ? $order->snap_redirect : '#' }}" aria-label="to payment page" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            @if($finished)Thank You @else Pay Now @endif
                        </a>
                    </div>
                    <!--end::link to payment-->
                    <!--end::footer-->
                <!--end::view information card-->

            </section>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        let page_paymentProcess = function ()
        {
            const REDIRECT_TO_PAYMENT_PAGE = document.getElementById('redirect_to_payment_page');
            const TIME_LEFT_PAYMENT = document.getElementById('time_left_payment');
            @if (!$finished)
            const payment_due_time = '{{ $order->due_date }}';
            const TIME_ZONE = '{{ config('app.timezone') }}';
            var initialTimeZone = "local";
            let isClicked = false;
            let isClosed = false;

            let localTime = new Date().toLocaleString("en-US", {timeZone: TIME_ZONE});
            let dueTime = new Date(payment_due_time).toLocaleString("en-US", {timeZone: TIME_ZONE});

            let time_left = new Date(dueTime) - new Date(localTime);

            let x = setInterval(function() {
                let hours = Math.floor((time_left % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let minutes = Math.floor((time_left % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((time_left % (1000 * 60)) / 1000);

                TIME_LEFT_PAYMENT.innerHTML = formatNumber(hours) + ":" + formatNumber(minutes) + ":" + formatNumber(seconds);

                if (time_left < 0) {
                    clearInterval(x);
                    TIME_LEFT_PAYMENT.innerHTML = "EXPIRED";
                    isClosed = true;
                    window.location.reload();
                }

                time_left -= 1000;
            }, 1000);
            @else
            TIME_LEFT_PAYMENT.innerHTML = 'COMPLETED';
            @endif

            let formatNumber = function (number)
            {
                return number.toString().padStart(2, '0');
            }

            REDIRECT_TO_PAYMENT_PAGE.addEventListener('click', function (e) {
                e.preventDefault();
                if (REDIRECT_TO_PAYMENT_PAGE.dataset.redirect != '#' && !isClicked && !isClosed) {
                    isClicked = true;
                    // redirect open new tab
                    window.open(REDIRECT_TO_PAYMENT_PAGE.dataset.redirect, '_blank');
                    // change button text to loading
                    REDIRECT_TO_PAYMENT_PAGE.innerHTML = 'Loading...';
                    // disable button
                    REDIRECT_TO_PAYMENT_PAGE.setAttribute('disabled', 'disabled');
                    // check payment process, every 5 seconds
                    setInterval(paymentProcessCheck, 5000);
                }
            });

            let paymentProcessCheck = function ()
            {
                let url = '{{ $checkPaymentStatusUrl }}';
                let data = {
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        if (response.status == 'success') {
                            // disable warning before unload
                            isClicked = false;
                            // redirect to success page
                            window.location.href = response.redirect;
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr);
                    }
                });
            }


            return {
                init: function () {
                    // if user want to close the page
                    window.addEventListener('beforeunload', function (e) {
                        // if user click button pay now
                        if (isClicked && !isClosed) {
                           // cancel the event
                           e.preventDefault();
                            // Chrome requires returnValue to be set
                            e.returnValue = 'Sure ?';
                        }
                    });
                }
            }
        }();

        jQuery(document).ready(function () {
            page_paymentProcess.init();
        });
    </script>
@endpush